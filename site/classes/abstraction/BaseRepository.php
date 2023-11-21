<?php
    namespace abstraction;

    use utils\SqlHelper as SqlHelper;

    abstract class BaseRepository
    {
        const GET_BY_PREDICATE_QUERY = 'SELECT * FROM `%s` %s';
        const GET_COUNT_BY_PREDICATE_QUERY = 'SELECT COUNT(*) as `count` FROM `%s` %s';
        const ADD_QUERY = 'INSERT INTO `%s`(%s) VALUES (%s)';
        const REMOVE_BY_ID = 'DELETE FROM `%s` WHERE `id`=:id';
        const UPDATE_QUERY = 'UPDATE `%s` SET %s WHERE `id`=:id';

        protected array $entityFields;

        public abstract function getTableName(): string;
        public abstract function getObjectClassName(): string;
        public abstract function map(array $row): object;

        public function __construct()
        {
            $this->entityFields = array_keys(get_class_vars(static::getObjectClassName()));
        }

        public function add(array $inputParams): int
        {
            // Получаем параметры совпадающие с наименованием полей объекта
            $objectParams = SqlHelper::filterParamsByNames($this->entityFields, $inputParams);
            $sqlParams = SqlHelper::getSqlParams($objectParams);

            $columns = implode(', ', array_keys($sqlParams));
            $parameters = implode(', ', array_values($sqlParams));

            $query = sprintf(static::ADD_QUERY, $this->getTableName(), $columns, $parameters);

            $statement = \DbContext::getConnection()->prepare($query);
            $statement->execute($objectParams);

            return \DbContext::getConnection()->lastInsertId();
        }

        public function get(array $inputParams): array|object|null
        {
            // Параметры однозначного совпадения (WHERE)
            $whereParams = SqlHelper::filterParamsByNames($this->entityFields, $inputParams);
            // Все переданные параметры для поиска (не зависимо от полей объекта)
            $allSearchParams = SqlHelper::getAllSearchParams($inputParams);
            // Параметры подходящие к нашему объекту
            $searchObjectParams = SqlHelper::filterParamsByNames($this->entityFields, $allSearchParams);
            // Преобразуем в параметры поиска (добавляем префикс для параметров 'search_' и '%value%' в значение)
            $formattedSearchParams = SqlHelper::convertObjectSearchParams($searchObjectParams);

            // Часть WHERE строки запроса
            $whereString = SqlHelper::getWhereString($whereParams);
            // Вторая часть WHERE (для поиска 'LIKE')
            $searchString = SqlHelper::getSearchString($searchObjectParams);

            // Получаем все параметры для сортировки
            $allOrderByParams = SqlHelper::getAllOrderByParams($inputParams);
            // Отбираем только подходящие для объекта
            $orderByObjectParams = SqlHelper::filterParamsByNames($this->entityFields, $allOrderByParams);
            // Получаем часть строки запроса с сортировкой
            $orderByString = SqlHelper::getOrderByString($orderByObjectParams);

            // Получаем часть строки запроса с лимитом и оффсетом
            $limitString = SqlHelper::getLimitString($inputParams);

            $whereParams = SqlHelper::convertToSqlParam($whereParams);
            $formattedSearchParams = SqlHelper::convertToSqlParam($formattedSearchParams);

            // Формируем результирующую строку запроса
            $query = sprintf(static::GET_BY_PREDICATE_QUERY, $this->getTableName(), implode(' ', [$whereString, $searchString, $orderByString, $limitString]));

            $statement = \DbContext::getConnection()->prepare($query);
            $statement->execute(array_merge($whereParams, $formattedSearchParams));

            if (!array_key_exists('id', $inputParams))
                return array_map([$this, 'map'], $statement->fetchAll());

            if (!$data = $statement->fetch())
                return null;

            return $this->map($data);
        }

        public function getCountWithoutLimit(array $inputParams): int
        {
            // Параметры однозначного совпадения (WHERE)
            $whereParams = SqlHelper::filterParamsByNames($this->entityFields, $inputParams);
            // Все переданные параметры для поиска (не зависимо от полей объекта)
            $allSearchParams = SqlHelper::getAllSearchParams($inputParams);
            // Параметры подходящие к нашему объекту
            $searchObjectParams = SqlHelper::filterParamsByNames($this->entityFields, $allSearchParams);
            // Преобразуем в параметры поиска (добавляем префикс для параметров 'search_' и '%value%' в значение)
            $formattedSearchParams = SqlHelper::convertObjectSearchParams($searchObjectParams);

            // Часть WHERE строки запроса
            $whereString = SqlHelper::getWhereString($whereParams);
            // Вторая часть WHERE (для поиска 'LIKE')
            $searchString = SqlHelper::getSearchString($searchObjectParams);

            // Формируем результирующую строку запроса
            $query = sprintf(static::GET_COUNT_BY_PREDICATE_QUERY, $this->getTableName(), implode(' ', [$whereString, $searchString]));

            $statement = \DbContext::getConnection()->prepare($query);
            $statement->execute(array_merge($whereParams, $formattedSearchParams));

            if (!$data = $statement->fetch())
                return 0;

            return $data['count'];
        }

        public function updateById(array $inputParams)
        {
            $objectParams = SqlHelper::filterParamsByNames($this->entityFields, $inputParams);
            $equalParams = SqlHelper::getEqualParams(array_keys($objectParams));

            if (array_key_exists('id', $equalParams))
                unset($equalParams['id']);

            $stringParams = implode(', ', $equalParams);
            $query = sprintf(static::UPDATE_QUERY, $this->getTableName(), $stringParams);
            $statement = \DbContext::getConnection()->prepare($query);
            $statement->execute($objectParams);
        }

        public function removeById(array $inputParams)
        {
            $query = sprintf(static::REMOVE_BY_ID, $this->getTableName());
            $statement = \DbContext::getConnection()->prepare($query);
            $queryValueParams = [
                'id' => $inputParams['id']
            ];
            $statement->execute($queryValueParams);
        }
    }
?>