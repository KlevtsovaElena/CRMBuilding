<?php
    namespace repositories;
    require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use models\Settings;
    use abstraction\BaseRepository;
    use utils\SqlHelper;

    class SettingsRepository extends BaseRepository
    {
        const TABLE_NAME = 'settings';
        const CLASS_NAME = 'models\Settings';
        
        public function getTableName() : string
        {
            return static::TABLE_NAME;
        }

        public function getObjectClassName() : string
        {
            return static::CLASS_NAME;
        }

        public function map(array $row): Settings
        {
            $item = new Settings();
            
            foreach(SqlHelper::filterParamsByNames($this->entityFields, $row) as $key => $value)
                $item->$key = $value;

            return $item;
        }
    }
?>