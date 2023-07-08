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
    }
?>