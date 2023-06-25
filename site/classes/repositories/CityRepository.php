<?php
namespace repositories;

require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

use models\City;
use abstraction\BaseRepository;

class CityRepository extends BaseRepository
{
    const TABLE_NAME = 'cities';
    const CLASS_NAME = 'models\City';
    
    public function getTableName() : string
    {
        return static::TABLE_NAME;
    }

    public function getObjectClassName() : string
    {
        return static::CLASS_NAME;
    }

    public function map(array $row): City
    {
        $item = new City();
        foreach ($this->getAssociatePropertiesWithClass($row) as $key => $value)
            $item->$key = $value;

        return $item;
    }
}
?>