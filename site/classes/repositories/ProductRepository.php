<?php 
    namespace repositories;
    use utils\SqlHelper;

    require_once($_SERVER['DOCUMENT_ROOT'].'/classes/autoloader.php');
    
    use models\Product;
    use abstraction\BaseRepository as BaseRepository;

    class ProductRepository extends BaseRepository
    {
        const TABLE_NAME = 'products';
        const CLASS_NAME = 'models\Product';    

        public function getTableName() : string
        {
            return static::TABLE_NAME;
        }

        public function getObjectClassName() : string
        {
            return static::CLASS_NAME;
        }

        public function map(array $row): Product
        {
            $item = new Product();

            foreach(SqlHelper::filterParamsByNames($this->entityFields, $row) as $key => $value)
                $item->$key = $value;
    
            return $item;
        }

        public function getById(int $id) : Product|null
        {
            return $this->get(["id" => $id]);
        }

        public function getAllByIds(array $ids) : array 
        {
            if (is_null($ids) || count($ids) == 0)
                return [];

            $queryParams = $this->getQueryIdsArrayParams($ids);
            $query = sprintf(static::GET_BY_PREDICATE_QUERY, $this->getTableName(), 'WHERE `id` IN ('.implode(',', array_keys($queryParams)) .')');

            $statement = \DbContext::getConnection()->prepare($query);
            $statement->execute($queryParams);
    
            return array_map([$this, 'map'], $statement->fetchAll());
        }

        private function getQueryIdsArrayParams(array $ids)
        {
            $resultArray = [];
            foreach($ids as $key => $value)
                $resultArray[':id'.$key] = $value;

            return $resultArray;
        }
    }
?>