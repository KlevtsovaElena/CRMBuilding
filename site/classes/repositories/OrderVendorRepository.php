<?php
namespace repositories;
use abstraction\BaseRepository;

require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

use models\OrderVendor;

class OrderVendorRepository extends BaseRepository
{
    const TABLE_NAME = 'order_vendors';
    const CLASS_NAME = 'models\OrderVendor';

    public function getTableName() : string
    {
        return static::TABLE_NAME;
    }

    public function getObjectClassName() : string
    {
        return static::CLASS_NAME;
    }

    public function map(array $row): OrderVendor
    {
        $item = new OrderVendor();
        foreach ($this->getAssociatePropertiesWithClass($row) as $key => $value)
        {
            if ($key == 'products')
            {
                $item->$key = isset($value) ? json_decode($value, true) : [];
                continue;
            }

            $item->$key = $value;
        }

        return $item;
    }
}
?>