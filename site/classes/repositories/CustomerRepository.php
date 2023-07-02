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

    public function map(array $row): Customer
    {
        $item = new Customer();

        foreach(SqlHelper::filterParamsByNames($this->entityFields, $row) as $key => $value)
            $item->$key = $value;

        return $item;
    }

    public function updateByTgId(array $inputParams)
    {
        $params = $this->getAssociatePropertiesWithClass($inputParams);
        $queryColmParams = $this->getQueryParameterAssociate($params);
        $queryValueParams = $this->getQueryParameterValues($params);

        if (array_key_exists('tg_id', $queryColmParams))
            unset($queryColmParams['tg_id']);

        $stringParams = implode(', ', $queryColmParams);
        $query = sprintf(static::UPDATE_BY_TG_ID_QUERY, $this->getTableName(), $stringParams);
        $statement = \DbContext::getConnection()->prepare($query);
        $statement->execute($queryValueParams);
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
}
?>