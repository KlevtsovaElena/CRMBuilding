<?php
    namespace repositories;
    require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use models\Brand;
    use abstraction\BaseRepository;
    use utils\SqlHelper;

    class BrandRepository extends BaseRepository
    {
        const TABLE_NAME = 'brands';
        const CLASS_NAME = 'models\Brand';

        public function getTableName() : string
        {
            return static::TABLE_NAME;
        }

        public function getObjectClassName() : string
        {
            return static::CLASS_NAME;
        }

        public function map(array $row): Brand
        {
            $item = new Brand();

            foreach(SqlHelper::filterParamsByNames($this->entityFields, $row) as $key => $value)
                $item->$key = $value;

            return $item;
        }
    }
?>