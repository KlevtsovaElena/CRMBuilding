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

    const GET_BY_CATEGORY_ID = 'SELECT DISTINCT b.`id`, b.`brand_name`, b.`deleted`
                                FROM brands b
                                    INNER JOIN `products` p ON
                                    p.`brand_id` = b.`id`
                                WHERE p.`category_id` = :category_id
                                    AND b.`deleted` = 0 
                                    AND p.`deleted` = 0';


    public function getTableName(): string
    {
        return static::TABLE_NAME;
    }

    public function getObjectClassName(): string
    {
        return static::CLASS_NAME;
    }

    public function map(array $row): Brand
    {
        $item = new Brand();

        foreach (SqlHelper::filterParamsByNames($this->entityFields, $row) as $key => $value)
            $item->$key = $value;

        return $item;
    }

    public function getByCategoryId(int $categoryId): array
    {
        // Формируем результирующую строку запроса
        $query = sprintf(static::GET_BY_CATEGORY_ID, $this->getTableName());

        $statement = \DbContext::getConnection()->prepare($query);
        $statement->execute([
            'category_id' => $categoryId
        ]);

        return array_map([$this, 'map'], $statement->fetchAll());
    }
}
?>