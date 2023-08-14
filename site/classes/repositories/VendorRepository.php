<?php
namespace repositories;

require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

use models\Vendor;
use abstraction\BaseRepository;
use models\Coordinate; //Настя: добавила строки по координатам
use utils\SqlHelper;

class VendorRepository extends BaseRepository
{
    const TABLE_NAME = 'vendors';
    const CLASS_NAME = 'models\Vendor';

    private CoordinateRepository $coordinateRepository; //Настя: добавила

    const UPDATE_QUERY_HASH = 'UPDATE `%s` SET %s WHERE `hash_string`=:hash_string';

    const GET_WITH_DETAILS = 'SELECT v.`id` as `id`,
                                    v.`name` as `name`,
                                    v.`city_id` as `city_id`,
                                    c.`name` as `city_name`,
                                    v.`phone` as `phone`,
                                    v.`email` as `email`,
                                    v.`tg_username` as `tg_username`,
                                    v.`tg_id` as `tg_id`,
                                    v.`coordinates` as `coordinates`,
                                    v.`role` as `role`,
                                    v.`comment` as `comment`,
                                    v.`date_reg` as `date_reg`,
                                    v.`hash_string` as `hash_string`,
                                    v.`is_active` as `is_active`,
                                    v.`password` as `password`,
                                    v.`token` as `token`,
                                    v.`deleted` as `deleted`,
                                    c.`deleted` as `city_deleted`,
                                    v.`percent` as `percent`,
                                    v.`owns` as `owns`,
                                    v.`price_confirmed` as `price_confirmed`,
                                    v.`currency_dollar` as `currency_dollar`,
                                    v.`rate` as `rate`
                                FROM vendors v
                                    INNER JOIN cities c ON
                                    c.`id` = v.`city_id`
                                    %s';

    private static array $vendorDetailsAccosiations = [
        'id' => 'v.id',
        'name' => 'v.name',
        'city_id' => 'v.city_id',
        'city_name' => 'c.name',
        'phone' => 'v.phone',
        'email' => 'v.email',
        'tg_username' => 'v.tg_username',
        'tg_id' => 'v.tg_id',
        'coordinates' => 'v.coordinates',
        'role' => 'v.role',
        'comment' => 'v.comment',
        'date_reg' => 'v.date_reg',
        'hash_string' => 'v.hash_string',
        'is_active' => 'v.is_active',
        'password' => 'v.password',
        'token' => 'v.token',
        'deleted' => 'v.deleted',
        'percent' => 'v.percent',
        'owns' => 'v.owns',
        'city_deleted' => 'c.deleted',
        'price_confirmed' => 'v.price_confirmed',
        'currency_dollar' => 'v.currency_dollar',
        'rate' => 'v.rate'
    ];

    //Настя: добавила __construct для координат
    public function __construct()
    {
        parent::__construct();
        $this->coordinateRepository = new CoordinateRepository();
    }

    public function getTableName(): string
    {
        return static::TABLE_NAME;
    }

    public function getObjectClassName(): string
    {
        return static::CLASS_NAME;
    }

    public function map(array $row): Vendor
    {
        $item = new Vendor();

        foreach (SqlHelper::filterParamsByNames($this->entityFields, $row) as $key => $value) {
            if ($key == 'coordinates') //Настя: добавила врутреннее условие для coordinates
            {
                $item->$key = isset($value) && strlen($value) > 0 ? $this->coordinateRepository->map(json_decode($value, true)) : new Coordinate();
                continue;
            }

            $item->$key = $value;
        }
        return $item;
    }

    public function updateByHash(array $inputParams)
    {
        $objectParams = SqlHelper::filterParamsByNames($this->entityFields, $inputParams);
        $equalParams = SqlHelper::getEqualParams(array_keys($objectParams));

        if (array_key_exists('hash_string', $equalParams))
            unset($equalParams['hash_string']);
        // где-то здесь вместе с обновлением записи по hash_string, нужно ещё очистить само поле hash_string
        $stringParams = implode(', ', $equalParams);

        $query = sprintf(static::UPDATE_QUERY_HASH, $this->getTableName(), $stringParams);

        $statement = \DbContext::getConnection()->prepare($query);
        $statement->execute($objectParams);
    }

    public function mapWithDetails(array $row): array
    {
        $item = [];
        foreach ($row as $key => $value) {
            if ($key == 'coordinates') {
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
       $whereParams = SqlHelper::filterParamsWithReplace(static::$vendorDetailsAccosiations, $inputParams);

       // Все переданные параметры для поиска (не зависимо от полей объекта)
       $allSearchParams = SqlHelper::getAllSearchParams($inputParams);
       // Параметры подходящие к нашему объекту
       $searchObjectParams = SqlHelper::filterParamsWithReplace(static::$vendorDetailsAccosiations, $allSearchParams);
       // Преобразуем в параметры поиска (добавляем префикс для параметров 'search_' и '%value%' в значение)
       $formattedSearchParams = SqlHelper::convertObjectSearchParams($searchObjectParams);

       // Часть WHERE строки запроса
       $whereString = SqlHelper::getWhereString($whereParams);
       // Вторая часть WHERE (для поиска 'LIKE')
       $searchString = SqlHelper::getSearchString($searchObjectParams);

       // Получаем все параметры для сортировки
       $allOrderByParams = SqlHelper::getAllOrderByParams($inputParams);
       // Отбираем только подходящие для объекта
       $orderByObjectParams = SqlHelper::filterParamsWithReplace(static::$vendorDetailsAccosiations, $allOrderByParams);
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
}
?>