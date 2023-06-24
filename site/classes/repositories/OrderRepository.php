<?php
namespace repositories;
use models\Coordinate;

require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

use models\Order;

class OrderRepository
{
    const TABLE_NAME = 'orders';
    const CLASS_NAME = 'models\Order';
    const GET_BY_PREDICATE_QUERY = 'SELECT * FROM `%s` WHERE %s';
    const ADD_QUERY = 'INSERT INTO `%s`(%s) VALUES (%s)';
    const REMOVE_BY_ID = 'DELETE FROM `%s` WHERE `id`=:id';
    const UPDATE_QUERY = 'UPDATE `%s` SET %s WHERE `id`=:id';

    private CoordinateRepository $coordinateRepository;

    public function __construct()
    {
        $this->coordinateRepository = new CoordinateRepository();
    }

    private function getParams($inputArray)
    {
        $items = get_class_vars(static::CLASS_NAME);

        $result = [];
        foreach ($items as $key => $value) {
            if (array_key_exists($key, $inputArray))
                $result[$key] = $inputArray[$key];
        }
        return $result;
    }

    public function get(array $inputParams): array|Order
    {
        $params = $this->getParams($inputParams);
        $queryColmParams = [];
        $queryValueParams = [];

        foreach ($params as $key => $value) {
            $queryColmParams[] = $key . '=:' . $key;
            $queryValueParams[':' . $key] = $value;
        }

        $stringParams = count($queryColmParams) > 0 ? implode(' AND ', $queryColmParams) : '1';
        $query = sprintf(static::GET_BY_PREDICATE_QUERY, static::TABLE_NAME, $stringParams);
        $statement = \DbContext::getConnection()->prepare($query);
        $statement->execute($queryValueParams);

        if (!array_key_exists('id', $params))
            return array_map([$this, 'map'], $statement->fetchAll());

        if (!$data = $statement->fetch())
            return new Order();

        return $this->map($data);
    }

    public function add(array $inputParams)
    {
        $params = $this->getParams($inputParams);
        $queryValueParams = [];

        foreach ($params as $key => $value)
        {
            if (is_object($value) || is_array($value))
                $value = json_encode($value);
            
            $queryValueParams[':' . $key] = $value;
        }

        $columns = implode(', ', array_keys($params));
        $parameters = implode(', ', array_keys($queryValueParams));

        $query = sprintf(static::ADD_QUERY, static::TABLE_NAME, $columns, $parameters);

        $statement = \DbContext::getConnection()->prepare($query);
        $statement->execute($queryValueParams);
    }

    public function update(array $inputParams)
    {
        $params = $this->getParams($inputParams);
        $queryColmParams = [];
        $queryValueParams = [];

        foreach ($params as $key => $value) {
            if ($key != 'id')
                $queryColmParams[] = $key . '=:' . $key;

            if (is_object($value) || is_array($value))
                $value = json_encode($value);
                
            $queryValueParams[':' . $key] = $value;
        }

        $stringParams = implode(', ', $queryColmParams);
        $query = sprintf(static::UPDATE_QUERY, static::TABLE_NAME, $stringParams);
        $statement = \DbContext::getConnection()->prepare($query);
        $statement->execute($queryValueParams);
    }

    public function removeById(array $inputParams)
    {
        $query = sprintf(static::REMOVE_BY_ID, static::TABLE_NAME);
        $statement = \DbContext::getConnection()->prepare($query);
        $queryValueParams = [
            'id' => $inputParams['id']
        ];
        $statement->execute($queryValueParams);
    }

    public function map(array $row): Order
    {
        $item = new Order();
        foreach ($this->getParams($row) as $key => $value)
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
}
?>