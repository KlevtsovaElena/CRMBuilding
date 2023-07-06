<?php
    namespace repositories;
    require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use models\Vendor;
    use abstraction\BaseRepository;
    use utils\SqlHelper;

    class VendorRepository extends BaseRepository
    {
        const TABLE_NAME = 'vendors';
        const CLASS_NAME = 'models\Vendor';
        
        const UPDATE_QUERY_HASH = 'UPDATE `%s` SET %s WHERE `hash_string`=:hash_string';
    
        public function getTableName() : string
        {
            return static::TABLE_NAME;
        }

        public function getObjectClassName() : string
        {
            return static::CLASS_NAME;
        }

        public function map(array $row): Vendor
        {
            $item = new Vendor();
            
            foreach(SqlHelper::filterParamsByNames($this->entityFields, $row) as $key => $value)
                $item->$key = $value;

            return $item;
        }

        public function updateByHash(array $inputParams)
        {
            $objectParams = SqlHelper::filterParamsByNames($this->entityFields, $inputParams);
            $equalParams = SqlHelper::getEqualParams(array_keys($objectParams));

            if (array_key_exists('hash_string', $equalParams))
                unset($equalParams['hash_string']);
// где-то здесь вместе с обновлением записи по hash_string, нужно ещё очистить само поле hash_string
            $stringParams = implode(', ', $equalParams);
echo $stringParams;
            $query = sprintf(static::UPDATE_QUERY_HASH, $this->getTableName(), $stringParams);
echo $query;
            $statement = \DbContext::getConnection()->prepare($query);
            $statement->execute($objectParams);
        }

    }
?>