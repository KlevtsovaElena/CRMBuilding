<?php
namespace repositories;
use abstraction\BaseRepository;

require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

use models\Brand;

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
        foreach ($this->getAssociatePropertiesWithClass($row) as $key => $value)
            $item->$key = $value;

        return $item;
    }
}
?>