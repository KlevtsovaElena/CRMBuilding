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

    const GET_BY_PREDICATE_QUERY_WITH_NAME_FRONT = 'SELECT *, 
                                    CASE WHEN `name` <> "" THEN `name`
                                        WHEN `name2` <> "" THEN `name2` 
                                        WHEN `name3` <> "" THEN `name3`
                                        ELSE "Наименование"
                                    END AS `name_front`  FROM `%s` %s';

    const GET_COUNT_WITH_DETAILS = 'SELECT COUNT(*) as `count`
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

    const GET_WITH_DETAILS = 'SELECT p.`id` as `id`,
                                        p.`name` as `name`,
                                        p.`name2` as `name2`,
                                        p.`name3` as `name3`,
                                        p.`description` as `description`,
                                        p.`description2` as `description2`,
                                        p.`description3` as `description3`,
                                        p.`photo` as `photo`,
                                        p.`article` as `article`,
                                        p.`category_id` as `category_id`,
                                        c.`category_name` as `category_name`,
                                        p.`brand_id` as `brand_id`,
                                        b.`brand_name` as `brand_name`,
                                        p.`vendor_id` as `vendor_id`,
                                        v.`name` as `vendor_name`,
                                        v.`currency_dollar` as `vendor_currency_dollar`,
                                        v.`rate` as `vendor_rate`,
                                        p.`quantity_available` as `quantity_available`,
                                        p.`price` as `price`,
                                        p.`price_dollar` as `price_dollar`,
                                        p.`max_price` as `max_price`,
                                        p.`max_price_dollar` as `max_price_dollar`,
                                        p.`unit_id` as `unit_id`,
                                        u.`name` as `unit_name`,
                                        u.`name_short`as `unit_name_short`,
                                        p.`deleted` as `deleted`,
                                        c.`deleted` as `category_deleted`,
                                        b.`deleted` as `brand_deleted`,
                                        v.`deleted` as `vendor_deleted`,
                                        cit.`is_active` as `city_active`,
                                        v.`is_active` as `vendor_active`,
                                        v.`city_id` as `city_id`,
                                        cit.`name` as `city_name`,
                                        v.`price_confirmed` as `price_confirmed`,
                                        p.`is_active` as `is_active`,
                                        p.`is_confirm` as `is_confirm`,
                                    CASE 
                                        WHEN p.`name` <> ""  THEN p.`name`
                                        WHEN p.`name2` <> "" THEN p.`name2`
                                        WHEN p.`name3` <> "" THEN p.`name3`
                                        ELSE "Наименование"
                                    END  AS `name_front`
                                    FROM products p
                                        INNER JOIN categories c ON
                                            c.`id` = p.`category_id`
                                        INNER JOIN brands b ON 
                                            b.`id` = p.`brand_id`
                                        INNER JOIN vendors v ON
                                            v.`id` = p.`vendor_id`
                                        INNER JOIN cities cit ON
                                            cit.`id` = v.`city_id`
                                        INNER JOIN units u ON
                                            u.`id` = p.`unit_id`
                                        %s';

    const UPDATE_PRICE_BY_VENDOR = 'UPDATE products p
                                        INNER JOIN vendors v ON
                                        p.`vendor_id` = v.`id`
                                        INNER JOIN products p1 ON
                                        p1.`id` = p.`id`
                                    SET p.price = p1.`price_dollar` * v.`rate`,
                                        p.max_price = p1.`max_price_dollar` * v.`rate`,
                                        p.is_confirm = 0 
                                    WHERE v.`id` = :vendor_id 
                                        AND v.`currency_dollar` = 1'; // Только если у вендора установлена валюта в долларах

    const UPDATE_CONFIRM_PRODUCTS_BY_VENDOR = 'UPDATE products p
                                               SET p.is_confirm = 1
                                               WHERE p.`vendor_id` = %s
                                                AND p.`deleted`=0
                                                AND p.`is_active`=1
                                                AND p.`price`<p.`max_price`';                     

    const UPDATE_PRICE_MASS_BY_VENDOR = 'UPDATE products p
                                        SET p.is_confirm = 0, %s
                                         %s';

    const COUNT_NOT_CONFIRM_PRODUCT_BY_VENDOR = 'SELECT COUNT(*) as `count`
                                                    FROM products p   
                                                    WHERE p.`vendor_id` = %s
                                                        AND p.`deleted`=0
                                                        AND p.`is_active`=1 
                                                        AND p.`is_confirm` = 0';                             

    // const GET_BY_CATEGORY = 'SELECT p.`id` as `id`,
    //                                     p.`name` as `name`,
    //                                     p.`name2` as `name2`,
    //                                     p.`name3` as `name3`,
    //                                     p.`description` as `description`,
    //                                     p.`description2` as `description2`,
    //                                     p.`description3` as `description3`,
    //                                     p.`photo` as `photo`,
    //                                     p.`article` as `article`,
    //                                     p.`category_id` as `category_id`,
    //                                     c.`category_name` as `category_name`,
    //                                     p.`brand_id` as `brand_id`,
    //                                     b.`brand_name` as `brand_name`,
    //                                     p.`vendor_id` as `vendor_id`,
    //                                     v.`name` as `vendor_name`,
    //                                     v.`currency_dollar` as `vendor_currency_dollar`,
    //                                     v.`rate` as `vendor_rate`,
    //                                     p.`quantity_available` as `quantity_available`,
    //                                     p.`price` as `price`,
    //                                     p.`price_dollar` as `price_dollar`,
    //                                     p.`max_price` as `max_price`,
    //                                     p.`max_price_dollar` as `max_price_dollar`,
    //                                     p.`unit_id` as `unit_id`,
    //                                     u.`name` as `unit_name`,
    //                                     u.`name_short`as `unit_name_short`,
    //                                     p.`deleted` as `deleted`,
    //                                     c.`deleted` as `category_deleted`,
    //                                     b.`deleted` as `brand_deleted`,
    //                                     v.`deleted` as `vendor_deleted`,
    //                                     v.`is_active` as `vendor_active`,
    //                                     v.`city_id` as `city_id`,
    //                                     cit.`name` as `city_name`,
    //                                     v.`price_confirmed` as `price_confirmed`,
    //                                     p.`is_active` as `is_active`,
    //                                     p.`is_confirm` as `is_confirm`
    //                                 FROM products p
    //                                     INNER JOIN categories c ON
    //                                         c.`id` = p.`category_id`
    //                                     INNER JOIN brands b ON 
    //                                         b.`id` = p.`brand_id`
    //                                     INNER JOIN vendors v ON
    //                                         v.`id` = p.`vendor_id`
    //                                     INNER JOIN cities cit ON
    //                                         cit.`id` = v.`city_id`
    //                                     INNER JOIN units u ON
    //                                         u.`id` = p.`unit_id`
    //                                     WHERE p.`category_id` IN (' . $category_arr . ')
    //                                     %s';

    private static array $productDetailsAccosiations = [
        'id' => 'p.id',
        'name' => 'p.name',
        'name2' => 'p.name2',
        'name3' => 'p.name3',
        'description' => 'p.description',
        'description2' => 'p.description2',
        'description3' => 'p.description3',
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
        'vendor_deleted' => 'v.deleted',
        'vendor_currency_dollar' => 'v.currency_dollar',
        'vendor_rate' => 'v.rate',
        'vendor_active' => 'v.is_active',
        'city_id' => 'v.city_id',
        'city_name' => 'cit.name', 
        'city_active' => 'cit.is_active',
        'price_confirmed' => 'v.price_confirmed',
        'is_active' => 'p.is_active',
        'is_confirm' => 'p.is_confirm',
        'name_front' => 'p.name_front'
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

    public function getAllByIdsWithNameFront(array $ids): array
    {
        if (is_null($ids) || count($ids) == 0)
            return [];

        $queryParams = $this->getQueryIdsArrayParams($ids);
        $query = sprintf(static::GET_BY_PREDICATE_QUERY_WITH_NAME_FRONT, $this->getTableName(), 'WHERE `id` IN (' . implode(',', array_keys($queryParams)) . ')');

        $statement = \DbContext::getConnection()->prepare($query);
        $statement->execute($queryParams);

        $result = $statement->fetchAll();
        
        return $result;
    }

    private function getQueryIdsArrayParams(array $ids)
    {
        $resultArray = [];
        foreach ($ids as $key => $value)
            $resultArray[':id' . $key] = $value;

        return $resultArray;
    }

    
    // ЛЕНА добавила только этот метод сюда
    public function getCountWithDetails(array $inputParams): int
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

        $whereParams = SqlHelper::convertToSqlParam($whereParams);
        $formattedSearchParams = SqlHelper::convertToSqlParam($formattedSearchParams);

        // Формируем результирующую строку запроса
        $query = sprintf(static::GET_COUNT_WITH_DETAILS, implode(' ', [$whereString, $searchString]));

        $statement = \DbContext::getConnection()->prepare($query);
        $statement->execute(array_merge($whereParams, $formattedSearchParams));


        if (!$data = $statement->fetch())
            return 0;

        return $data['count'];
    }

    public function updatePriceByVendor(int $vendorId) 
    {
        $statement = \DbContext::getConnection()->prepare(static::UPDATE_PRICE_BY_VENDOR);
        $statement->execute([
            'vendor_id' => $vendorId
        ]);
    }

    public function updatePriceMassByVendor(array $inputParams) 
    {
        $query = sprintf(static::UPDATE_PRICE_MASS_BY_VENDOR, $inputParams['set_string'], $inputParams['where_string']);
        $statement = \DbContext::getConnection()->prepare($query);
        $statement->execute();
    }

    public function updateConfirmProductByVendor(array $inputParams) 
    {
        $query = sprintf(static::UPDATE_CONFIRM_PRODUCTS_BY_VENDOR, $inputParams['vendor_id']);
        $statement = \DbContext::getConnection()->prepare($query);
        $statement->execute();
    }

    public function countNotConfirmProductByVendor(array $inputParams): int
    {
        $query = sprintf(static::COUNT_NOT_CONFIRM_PRODUCT_BY_VENDOR, $inputParams['vendor_id']);
        $statement = \DbContext::getConnection()->prepare($query);
        $statement->execute();
        if (!$data = $statement->fetch())
            return 0;

        return $data['count'];
    }
    
}
?>