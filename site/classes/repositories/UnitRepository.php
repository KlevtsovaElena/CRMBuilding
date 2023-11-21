<?php 
    namespace repositories;
    require_once($_SERVER['DOCUMENT_ROOT'].'/classes/autoloader.php');
    
    use models\Unit;
    use abstraction\BaseRepository;
    use utils\SqlHelper;

    class UnitRepository extends BaseRepository
    {
        const TABLE_NAME = 'units';
        const CLASS_NAME = 'models\Unit';
        const GET_COUNT_WITH_DETAILS = 'SELECT COUNT(*) as `count`
                                        FROM units u
                                        %s';
        const GET_WITH_DETAILS = 'SELECT    u.`id` as `id`,
                                            u.`name` as `name`,
                                            u.`name_short` as `name_short`,
                                            u.`delete` as `delete`,
                                        FROM units u
                                            %s';

        private static array $unitDetailsAccosiations = [
                                            'id' => 'u.id',
                                            'name' => 'u.name',
                                            'name_short' => 'u.name_short',
                                            'delete' => 'u.delete'
                                            ];




        public function getTableName() : string
        {
            return static::TABLE_NAME;
        }
    
        public function getObjectClassName() : string
        {
            return static::CLASS_NAME;
        }
    
        public function map(array $row): Unit
        {
            $item = new Unit();

            foreach(SqlHelper::filterParamsByNames($this->entityFields, $row) as $key => $value)
                $item->$key = $value;
    
            return $item;
        }

        public function getCountWithDetails(array $inputParams): int
        {
            // Параметры однозначного совпадения (WHERE)
            $whereParams = SqlHelper::filterParamsWithReplace(static::$unitDetailsAccosiations, $inputParams);
    
            // Все переданные параметры для поиска (не зависимо от полей объекта)
            $allSearchParams = SqlHelper::getAllSearchParams($inputParams);
            // Параметры подходящие к нашему объекту
            $searchObjectParams = SqlHelper::filterParamsWithReplace(static::$unitDetailsAccosiations, $allSearchParams);
            // Преобразуем в параметры поиска (добавляем префикс для параметров 'search_' и '%value%' в значение)
            $formattedSearchParams = SqlHelper::convertObjectSearchParams($searchObjectParams);
    
            // Часть WHERE строки запроса
            $whereString = SqlHelper::getWhereString($whereParams);
            // Вторая часть WHERE (для поиска 'LIKE')
            $searchString = SqlHelper::getSearchString($searchObjectParams);
    
            $whereParams = SqlHelper::convertToSqlParam($whereParams);
            $formattedSearchParams = SqlHelper::convertToSqlParam($formattedSearchParams);
    
            // Формируем результирующую строку запроса
            $query = sprintf(static::GET_COUNT_WITH_DETAILS, implode(' ', [$whereString, $searchString]));
    
            $statement = \DbContext::getConnection()->prepare($query);
            $statement->execute(array_merge($whereParams, $formattedSearchParams));
    
    
            if (!$data = $statement->fetch())
                return 0;
    
            return $data['count'];
        }
    }
?>