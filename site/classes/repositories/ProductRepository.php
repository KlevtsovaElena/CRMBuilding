<?php
namespace repositories;

use utils\SqlHelper;

require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

use models\Product;
use abstraction\BaseRepository as BaseRepository;

class ProductRepository extends BaseRepository
{
    const TABLE_NAME = 'products';
    const CLASS_NAME = 'models\Product';

    const GET_WITH_DETAILS = 'SELECT p.`id` as `id`,
                                        p.`name` as `name`,
                                        p.`description` as `description`,
                                        p.`photo` as `photo`,
                                        p.`article` as `article`,
                                        p.`category_id` as `category_id`,
                                        c.`category_name` as `category_name`,
                                        p.`brand_id` as `brand_id`,
                                        b.`brand_name` as `brand_name`,
                                        p.`vendor_id` as `vendor_id`,
                                        v.`name` as `vendor_name`,
                                        p.`quantity_available` as `quantity_available`,
                                        p.`price` as `price`,
                                        p.`max_price` as `max_price`,
                                        p.`unit_id` as `unit_id`,
                                        u.`name` as `unit_name`,
                                        u.`name_short`as `unit_name_short`,
                                        p.`deleted` as `deleted`,
                                        c.`deleted` as `category_deleted`,
                                        b.`deleted` as `brand_deleted`,
                                        v.`deleted` as `vendor_deleted`
                                    FROM products p
                                        INNER JOIN categories c ON
                                            c.`id` = p.`category_id`
                                        INNER JOIN brands b ON 
                                            b.`id` = p.`brand_id`
                                        INNER JOIN vendors v ON
                                            v.`id` = p.`vendor_id`
                                        INNER JOIN units u ON
                                            u.`id` = p.`unit_id`
                                        %s';

    private static array $productDetailsAccosiations = [
        'id' => 'p.id',
        'name' => 'p.name',
        'description' => 'p.description',
        'photo' => 'p.photo',
        'article' => 'p.article',
        'category_id' => 'p.category_id',
        'category_name' => 'c.category_name',
        'brand_id' => 'p.brand_id',
        'brand_name' => 'b.brand_name',
        'vendor_id' => 'p.vendor_id',
        'vendor_name' => 'v.name',
        'quantity_available' => 'p.quantity_available',
        'price' => 'p.price',
        'max_price' => 'p.max_price',
        'unit_id' => 'p.unit_id',
        'unit_name' => 'u.name',
        'unit_name_short' => 'u.name_short',
        'deleted' => 'p.deleted',
        'category_deleted' => 'c.deleted',
        'brand_deleted' => 'b.deleted',
        'vendor_deleted' => 'v.deleted'
    ];

    public function getTableName(): string
    {
        return static::TABLE_NAME;
    }

    public function getObjectClassName(): string
    {
        return static::CLASS_NAME;
    }

    public function map(array $row): Product
    {
        $item = new Product();

        foreach (SqlHelper::filterParamsByNames($this->entityFields, $row) as $key => $value)
            $item->$key = $value;

        return $item;
    }

    public function mapWithDetails(array $row): array
    {
        $item = [];
        foreach ($row as $key => $value) {
            $item[$key] = $value;
        }

        return $item;
    }

    public function getWithDetails(array $inputParams): array
    {
       // Параметры однозначного совпадения (WHERE)
       $whereParams = SqlHelper::filterParamsWithReplace(static::$productDetailsAccosiations, $inputParams);

       // Все переданные параметры для поиска (не зависимо от полей объекта)
       $allSearchParams = SqlHelper::getAllSearchParams($inputParams);
       // Параметры подходящие к нашему объекту
       $searchObjectParams = SqlHelper::filterParamsWithReplace(static::$productDetailsAccosiations, $allSearchParams);
       // Преобразуем в параметры поиска (добавляем префикс для параметров 'search_' и '%value%' в значение)
       $formattedSearchParams = SqlHelper::convertObjectSearchParams($searchObjectParams);

       // Часть WHERE строки запроса
       $whereString = SqlHelper::getWhereString($whereParams);
       // Вторая часть WHERE (для поиска 'LIKE')
       $searchString = SqlHelper::getSearchString($searchObjectParams);

       // Получаем все параметры для сортировки
       $allOrderByParams = SqlHelper::getAllOrderByParams($inputParams);
       // Отбираем только подходящие для объекта
       $orderByObjectParams = SqlHelper::filterParamsWithReplace(static::$productDetailsAccosiations, $allOrderByParams);
       // Получаем часть строки запроса с сортировкой
       $orderByString = SqlHelper::getOrderByString($orderByObjectParams);

       // Получаем часть строки запроса с лимитом и оффсетом
       $limitString = SqlHelper::getLimitString($inputParams);

       $query = sprintf(static::GET_WITH_DETAILS, implode(' ', [$whereString, $searchString, $orderByString, $limitString]));

       $whereParams = SqlHelper::convertToSqlParam($whereParams);
       $formattedSearchParams = SqlHelper::convertToSqlParam($formattedSearchParams);

       $statement = \DbContext::getConnection()->prepare($query);
       $statement->execute(array_merge($whereParams, $formattedSearchParams));

       return array_map([$this, 'mapWithDetails'], $statement->fetchAll());
    }

    public function getById(int $id): Product|null
    {
        return $this->get(["id" => $id]);
    }

    public function getAllByIds(array $ids): array
    {
        if (is_null($ids) || count($ids) == 0)
            return [];

        $queryParams = $this->getQueryIdsArrayParams($ids);
        $query = sprintf(static::GET_BY_PREDICATE_QUERY, $this->getTableName(), 'WHERE `id` IN (' . implode(',', array_keys($queryParams)) . ')');

        $statement = \DbContext::getConnection()->prepare($query);
        $statement->execute($queryParams);

        return array_map([$this, 'map'], $statement->fetchAll());
    }

    private function getQueryIdsArrayParams(array $ids)
    {
        $resultArray = [];
        foreach ($ids as $key => $value)
            $resultArray[':id' . $key] = $value;

        return $resultArray;
    }
}
?>