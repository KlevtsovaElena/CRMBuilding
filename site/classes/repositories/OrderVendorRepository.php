<?php
namespace repositories;

require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

use models\OrderVendor;
use abstraction\BaseRepository;
use utils\SqlHelper;

class OrderVendorRepository extends BaseRepository
{
    const TABLE_NAME = 'order_vendors';
    const CLASS_NAME = 'models\OrderVendor';

    const GET_COUNT_WITH_DETAILS = 'SELECT COUNT(*) as `count`
                                        FROM order_vendors ov
                                        INNER JOIN vendors v
                                        ON v.id = ov.vendor_id
                                        LEFT JOIN cities cit
                                        ON cit.id = v.city_id
                                        RIGHT JOIN orders o
                                        ON o.id = ov.order_id
                                        INNER JOIN customers c
                                        ON c.id = o.customer_id 
                                        %s';

    const GET_WITH_DETAILS = 'SELECT ov.`id` as `id`,
                                        ov.`order_id` as `order_id`,
                                        ov.`vendor_id` as `vendor_id`,
                                        v.`name` as `vendor_name`,
                                        v.`coordinates` as `vendor_location`,
                                        v.`deleted` as `vendor_deleted`,
                                        cit.`name` as `vendor_city`,
                                        o.`order_date` as `order_date`,
                                        ov.`status` as `status`,
                                        ov.`archive` as `archive`,
                                        c.`phone` as `customer_phone`,
                                        o.`customer_id` as `customer_id`,
                                        o.`location` as `order_location`,
                                        ov.`products` as `products`,
                                        ov.`total_price` as `total_price`,
                                        ov.`distance` as `distance`
                                    FROM order_vendors ov
                                    INNER JOIN vendors v
                                    ON v.id = ov.vendor_id
                                    LEFT JOIN cities cit
                                    ON cit.id = v.city_id
                                    RIGHT JOIN orders o
                                    ON o.id = ov.order_id
                                    INNER JOIN customers c
                                    ON c.id = o.customer_id 
                                    %s';

    private static array $orderVendorsDetailsAssociation = [
        'id' => 'ov.id',
        'order_id' => 'ov.order_id',
        'vendor_id' => 'ov.vendor_id',
        'vendor_name' => 'v.name',
        'vendor_location' => 'v.coordinates',
        'vendor_deleted' => 'v.deleted',
        'vendor_city' => 'cit.name',
        'order_date' => 'o.order_date',
        'status' => 'ov.status',
        'archive' => 'ov.archive',
        'customer_phone' => 'c.phone',
        'customer_id' => 'o.customer_id',
        'order_location' => 'o.location',
        'products' => 'ov.products',
        'total_price' => 'ov.total_price',
        'distance' => 'ov.distance'
    ];

    public function getTableName(): string
    {
        return static::TABLE_NAME;
    }

    public function getObjectClassName(): string
    {
        return static::CLASS_NAME;
    }

    public function map(array $row): OrderVendor
    {
        $item = new OrderVendor();

        foreach (SqlHelper::filterParamsByNames($this->entityFields, $row) as $key => $value) {
            if ($key == 'products') {
                $item->$key = isset($value) ? json_decode($value, true) : [];
                continue;
            }

            $item->$key = $value;
        }

        return $item;
    }

    public function mapWithDetails(array $row): array
    {
        $item = [];
        foreach ($row as $key => $value) {
            if ($key == 'products' || $key == 'order_location' || $key == 'vendor_location') {
                $item[$key] = isset($value) ? json_decode($value, true) : [];
                continue;
            }

            $item[$key] = $value;
        }

        return $item;
    }

    public function getWithDetails(array $inputParams): array
    {
        // Параметры однозначного совпадения (WHERE)
        $whereParams = SqlHelper::filterParamsWithReplace(static::$orderVendorsDetailsAssociation, $inputParams);

        // Все переданные параметры для поиска (не зависимо от полей объекта)
        $allSearchParams = SqlHelper::getAllSearchParams($inputParams);
        // Параметры подходящие к нашему объекту
        $searchObjectParams = SqlHelper::filterParamsWithReplace(static::$orderVendorsDetailsAssociation, $allSearchParams);
        // Преобразуем в параметры поиска (добавляем префикс для параметров 'search_' и '%value%' в значение)
        $formattedSearchParams = SqlHelper::convertObjectSearchParams($searchObjectParams);

        // Часть WHERE строки запроса
        $whereString = SqlHelper::getWhereString($whereParams);
        // Вторая часть WHERE (для поиска 'LIKE')
        $searchString = SqlHelper::getSearchString($searchObjectParams);

        // Получаем все параметры для сортировки
        $allOrderByParams = SqlHelper::getAllOrderByParams($inputParams);
        // Отбираем только подходящие для объекта
        $orderByObjectParams = SqlHelper::filterParamsWithReplace(static::$orderVendorsDetailsAssociation, $allOrderByParams);
        // Получаем часть строки запроса с сортировкой
        $orderByString = SqlHelper::getOrderByString($orderByObjectParams);

        // Получаем часть строки запроса с лимитом и оффсетом
        $limitString = SqlHelper::getLimitString($inputParams);

        if (isset($inputParams['date_from']))
        {
            $whereString .= ' AND (o.order_date >= :date_from)';
            $whereParams['date_from'] = $inputParams['date_from'];
        }

        if (isset($inputParams['date_till']))
        {
            $whereString .= ' AND (o.order_date < :date_till)';
            $whereParams['date_till'] = $inputParams['date_till'];
        }

        $query = sprintf(static::GET_WITH_DETAILS, implode(' ', [$whereString, $searchString, $orderByString, $limitString]));

        $whereParams = SqlHelper::convertToSqlParam($whereParams);
        $formattedSearchParams = SqlHelper::convertToSqlParam($formattedSearchParams);

        $statement = \DbContext::getConnection()->prepare($query);
        $statement->execute(array_merge($whereParams, $formattedSearchParams));

        return array_map([$this, 'mapWithDetails'], $statement->fetchAll());
    }

    // ЛЕНА добавила только этот метод сюда
    public function getCountWithDetails(array $inputParams): int
    {
        // Параметры однозначного совпадения (WHERE)
        $whereParams = SqlHelper::filterParamsWithReplace(static::$orderVendorsDetailsAssociation, $inputParams);

        // Все переданные параметры для поиска (не зависимо от полей объекта)
        $allSearchParams = SqlHelper::getAllSearchParams($inputParams);
        // Параметры подходящие к нашему объекту
        $searchObjectParams = SqlHelper::filterParamsWithReplace(static::$orderVendorsDetailsAssociation, $allSearchParams);
        // Преобразуем в параметры поиска (добавляем префикс для параметров 'search_' и '%value%' в значение)
        $formattedSearchParams = SqlHelper::convertObjectSearchParams($searchObjectParams);

        // Часть WHERE строки запроса
        $whereString = SqlHelper::getWhereString($whereParams);
        // Вторая часть WHERE (для поиска 'LIKE')
        $searchString = SqlHelper::getSearchString($searchObjectParams);
        $whereParams = SqlHelper::convertToSqlParam($whereParams);
        $formattedSearchParams = SqlHelper::convertToSqlParam($formattedSearchParams);

        if (isset($inputParams['date_from']))
        {
            $whereString .= ' AND (o.order_date >= :date_from)';
            $whereParams['date_from'] = $inputParams['date_from'];
        }

        if (isset($inputParams['date_till']))
        {
            $whereString .= ' AND (o.order_date < :date_till)';
            $whereParams['date_till'] = $inputParams['date_till'];
        }

        // Формируем результирующую строку запроса
        $query = sprintf(static::GET_COUNT_WITH_DETAILS, implode(' ', [$whereString, $searchString]));

        $statement = \DbContext::getConnection()->prepare($query);
        $statement->execute(array_merge($whereParams, $formattedSearchParams));

        if (!$data = $statement->fetch())
            return 0;

        return $data['count'];
    }
}
?>