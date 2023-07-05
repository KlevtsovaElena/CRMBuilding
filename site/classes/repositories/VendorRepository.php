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
    }
?>