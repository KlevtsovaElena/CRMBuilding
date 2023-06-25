<?php
namespace repositories;
use abstraction\BaseRepository;
use models\Coordinate;

require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

use models\Order;

class OrderRepository extends BaseRepository
{
    const TABLE_NAME = 'orders';
    const CLASS_NAME = 'models\Order';

    private CoordinateRepository $coordinateRepository;

    public function __construct()
    {
        $this->coordinateRepository = new CoordinateRepository();
    }

    public function getTableName() : string
    {
        return static::TABLE_NAME;
    }

    public function getObjectClassName() : string
    {
        return static::CLASS_NAME;
    }

    public function map(array $row): Order
    {
        $item = new Order();
        foreach ($this->getAssociatePropertiesWithClass($row) as $key => $value)
        {
            if ($key == 'location')
            {
                $item->$key = isset($value) ? $this->coordinateRepository->map(json_decode($value, true)) : new Coordinate();
                continue;
            }

            if ($key == 'products')
            {
                $item->$key = isset($value) ? json_decode($value, true) : [];
                continue;
            }

            $item->$key = $value;
        }

        return $item;
    }

    public function getById(int $id) : Order|null
    {
        return $this->get(["id" => $id]);
    }
}
?>