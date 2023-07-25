<?php
namespace repositories;

require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

use models\Category;
use abstraction\BaseRepository;
use utils\SqlHelper;

class CategoryRepository extends BaseRepository
{
    const TABLE_NAME = 'categories';
    const CLASS_NAME = 'models\Category';

    const GET_ALL_BY_EXIST_PRODUCTS = 'SELECT DISTINCT c.`id`, c.`category_name`, c.`deleted`
                                FROM categories c
                                    INNER JOIN `products` p ON
                                    p.`category_id` = c.`id`
                                WHERE c.`deleted` = 0
                                    AND p.`deleted` = 0';

    public function getTableName(): string
    {
        return static::TABLE_NAME;
    }

    public function getObjectClassName(): string
    {
        return static::CLASS_NAME;
    }

    public function map(array $row): Category
    {
        $item = new Category();

        foreach (SqlHelper::filterParamsByNames($this->entityFields, $row) as $key => $value)
            $item->$key = $value;

        return $item;
    }

    public function getAllByExistProducts(): array
    {
        // Формируем результирующую строку запроса
        $query = sprintf(static::GET_ALL_BY_EXIST_PRODUCTS, $this->getTableName());

        $statement = \DbContext::getConnection()->prepare($query);
        $statement->execute();

        return array_map([$this, 'map'], $statement->fetchAll());
    }
}
?>