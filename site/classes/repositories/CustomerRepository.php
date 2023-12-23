<?php
namespace repositories;
require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

use models\Customer;
use abstraction\BaseRepository;
use utils\SqlHelper;

class CustomerRepository extends BaseRepository
{
    const UPDATE_BY_TG_ID_QUERY = 'UPDATE `%s` SET %s WHERE `tg_id`=:tg_id';
    const GET_BY_TG_ID_QUERY = 'SELECT * FROM %s WHERE `tg_id`=:tg_id';

    const TABLE_NAME = 'customers';
    const CLASS_NAME = 'models\Customer';

    const GET_WITH_DETAILS = 'SELECT c.`id` as `id`,
                            c.`first_name` as `first_name`,
                            c.`last_name` as `last_name`,
                            c.`phone` as `phone`,
                            c.`city_id` as `city_id`,
                            cit.`name` as `city_name`,
                            c.`tg_id` as `tg_id`,
                            c.`tg_username` as `tg_username`,
                            c.`is_blocked` as `is_blocked`
                        FROM customers c
                        LEFT OUTER JOIN cities cit
                        ON cit.id = c.city_id
                        %s';

    const GET_WITH_ORDER_VENDORS = 'SELECT DISTINCT c.`id` as `id`,
                                        c.`first_name` as `first_name`,
                                        c.`last_name` as `last_name`,
                                        c.`phone` as `customer_phone`,
                                        c.`tg_id` as `customer_tg_id`,
                                        c.`city_id` as `customer_city_id`,
                                        cities.`name` as `customer_city_name`,
                                        c.`is_blocked` as `is_blocked`,
                                        o.`location` as `order_location`,
                                        o.`order_date` as `order_date`,
                                        ov.`order_id` as `order_id`,
                                        ov.`vendor_id` as `vendor_id`,
                                        ov.`products` as `products`,
                                        ov.`total_price` as `total_price`,
                                        ov.`distance` as `distance`,
                                        ov.`status` as `status`,
                                        ov.`archive` as `archive`,
                                        v.`name` as `vendor_name`,
                                        v.`city_id` as `vendor_city_id`,
                                        v.`coordinates` as `vendor_location`,
                                        v.`deleted` as `vendor_deleted`,
                                        cit.`name` as `vendor_city`
                                        FROM customers c
                                                LEFT JOIN cities cities
                                                ON cities.id = c.city_id
                                                JOIN orders o
                                                ON o.customer_id = c.id
                                                JOIN order_vendors ov
                                                ON ov.order_id = o.id
                                                JOIN vendors v
                                                ON v.id = ov.vendor_id
                                                JOIN cities cit
                                                ON cit.id = v.city_id
                                                %s';

    private static array $customersDetailsAssociation = [
        'id' => 'c.id',
        'first_name' => 'c.first_name',
        'last_name' => 'c.last_name',
        'phone' => 'c.phone',
        'city_id' => 'c.city_id',
        'city_name' => 'cit.name',
        'tg_id' => 'c.tg_id',
        'tg_username' => 'c.tg_username',
        'is_blocked' => 'c.is_blocked'
    ];

    private static array $customersWithOrderVendorsAssociation = [
        'id' => 'c.id',
        'first_name' => 'c.first_name',
        'last_name' => 'c.last_name',
        'phone' => 'c.phone',
        'city_id' => 'c.city_id',
        'city_name' => 'cities.name',
        'tg_id' => 'c.tg_id',
        'tg_username' => 'c.tg_username',
        'is_blocked' => 'c.is_blocked',
        'order_location' => 'o.order_location',
        'order_date' => 'o.order_date',
        'order_id' => 'ov.order_id',
        'vendor_id' => 'ov.vendor_id',
        'products' => 'ov.products',
        'total_price' => 'ov.total_price',
        'distance' => 'ov.distance',
        'status' => 'ov.status',
        'archive' => 'ov.archive',
        'vendor_name' => 'v.vendor_name',
        'vendor_city_id' => 'v.vendor_city_id',
        'vendor_location' => 'v.vendor_location',
        'vendor_deleted' => 'v.vendor_deleted',
        'vendor_city_name' => 'cit.vendor_city'

    ];

    private CoordinateRepository $coordinateRepository;

    public function __construct()
    {
        parent::__construct();
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

    public function map(array $row): Customer
    {
        $item = new Customer();

        foreach(SqlHelper::filterParamsByNames($this->entityFields, $row) as $key => $value)
            $item->$key = $value;

        return $item;
    }

    public function updateByTgId(array $inputParams)
    {
        $objectParams = SqlHelper::filterParamsByNames($this->entityFields, $inputParams);
        $equalParams = SqlHelper::getEqualParams(array_keys($objectParams));

        if (array_key_exists('tg_id', $equalParams))
            unset($equalParams['tg_id']);

        $stringParams = implode(', ', $equalParams);
        $query = sprintf(static::UPDATE_BY_TG_ID_QUERY, $this->getTableName(), $stringParams);
        $statement = \DbContext::getConnection()->prepare($query);
        $statement->execute($objectParams);
    }

    public function getByTgId(int $tgId) : Customer|null
    {
        $query = sprintf(static::GET_BY_TG_ID_QUERY, $this->getTableName());
        $statement = \DbContext::getConnection()->prepare($query);
        $params = [
            ":tg_id" => $tgId
        ];
        $statement->execute($params);

        if (!$data = $statement->fetch())
            return null;

        return $this->map($data);
    }

    public function getWithDetails(array $inputParams): array
    {
        // Параметры однозначного совпадения (WHERE)
        $whereParams = SqlHelper::filterParamsWithReplace(static::$customersDetailsAssociation, $inputParams);

        // Все переданные параметры для поиска (не зависимо от полей объекта)
        $allSearchParams = SqlHelper::getAllSearchParams($inputParams);
        // Параметры подходящие к нашему объекту
        $searchObjectParams = SqlHelper::filterParamsWithReplace(static::$customersDetailsAssociation, $allSearchParams);
        // Преобразуем в параметры поиска (добавляем префикс для параметров 'search_' и '%value%' в значение)
        $formattedSearchParams = SqlHelper::convertObjectSearchParams($searchObjectParams);

        // Часть WHERE строки запроса
        $whereString = SqlHelper::getWhereString($whereParams);
        // Вторая часть WHERE (для поиска 'LIKE')
        $searchString = SqlHelper::getSearchString($searchObjectParams);

        // Получаем все параметры для сортировки
        $allOrderByParams = SqlHelper::getAllOrderByParams($inputParams);
        // Отбираем только подходящие для объекта
        $orderByObjectParams = SqlHelper::filterParamsWithReplace(static::$customersDetailsAssociation, $allOrderByParams);
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

    public function getWithOrderVendors(array $inputParams): array
    {
        // Параметры однозначного совпадения (WHERE)
        $whereParams = SqlHelper::filterParamsWithReplace(static::$customersWithOrderVendorsAssociation, $inputParams);

        // Все переданные параметры для поиска (не зависимо от полей объекта)
        $allSearchParams = SqlHelper::getAllSearchParams($inputParams);
        // Параметры подходящие к нашему объекту
        $searchObjectParams = SqlHelper::filterParamsWithReplace(static::$customersWithOrderVendorsAssociation, $allSearchParams);
        // Преобразуем в параметры поиска (добавляем префикс для параметров 'search_' и '%value%' в значение)
        $formattedSearchParams = SqlHelper::convertObjectSearchParams($searchObjectParams);

        // Часть WHERE строки запроса
        $whereString = SqlHelper::getWhereString($whereParams);
        // Вторая часть WHERE (для поиска 'LIKE')
        $searchString = SqlHelper::getSearchString($searchObjectParams);

        // Получаем все параметры для сортировки
        $allOrderByParams = SqlHelper::getAllOrderByParams($inputParams);
        // Отбираем только подходящие для объекта
        $orderByObjectParams = SqlHelper::filterParamsWithReplace(static::$customersWithOrderVendorsAssociation, $allOrderByParams);
        // Получаем часть строки запроса с сортировкой
        $orderByString = SqlHelper::getOrderByString($orderByObjectParams);

        // Получаем часть строки запроса с лимитом и оффсетом
        $limitString = SqlHelper::getLimitString($inputParams);

        $query = sprintf(static::GET_WITH_ORDER_VENDORS, implode(' ', [$whereString, $searchString, $orderByString, $limitString]));

        $whereParams = SqlHelper::convertToSqlParam($whereParams);
        $formattedSearchParams = SqlHelper::convertToSqlParam($formattedSearchParams);

        $statement = \DbContext::getConnection()->prepare($query);
        $statement->execute(array_merge($whereParams, $formattedSearchParams));

        return array_map([$this, 'mapWithDetails'], $statement->fetchAll());
    }

    public function mapWithDetails(array $row): array
    {
        $item = [];
        foreach ($row as $key => $value) {
            $item[$key] = $value;
        }

        return $item;
    }
}
?>