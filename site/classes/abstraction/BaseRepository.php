<?php
namespace abstraction;

abstract class BaseRepository
{
    const GET_BY_PREDICATE_QUERY = 'SELECT * FROM `%s` WHERE %s';
    const ADD_QUERY = 'INSERT INTO `%s`(%s) VALUES (%s)';
    const REMOVE_BY_ID = 'DELETE FROM `%s` WHERE `id`=:id';
    const UPDATE_QUERY = 'UPDATE `%s` SET %s WHERE `id`=:id';

    public abstract function getTableName(): string;
    public abstract function getObjectClassName(): string;
    public abstract function map(array $row): object;

    protected function getAssociatePropertiesWithClass(array $inputProperties, string $prefix = null): array
    {
        $classProperties = get_class_vars(static::getObjectClassName());

        $properties = [];
        foreach ($classProperties as $property => $value) {
            if (array_key_exists($property, $inputProperties))
                $properties[$prefix . $property] = $inputProperties[$prefix . $property];
        }
        return $properties;
    }

    protected final function getQueryParameterAssociate(array $inputProperties, string $prefix = null): array
    {
        $queryParameters = [];

        foreach (array_keys($inputProperties) as $property)
            $queryParameters[$prefix . $property] = $prefix . $property . '=:' . $prefix . $property;

        return $queryParameters;
    }

    protected final function getQuerySearchParameterAssociate(array $inputProperties, string $prefix): array
    {
        $queryParameters = [];

        foreach (array_keys($inputProperties) as $property)
            $queryParameters[] = 'CONVERT(' . $property . ', CHAR) LIKE :' . $prefix . $property;

        return $queryParameters;
    }

    protected final function getQuerySearchParameterValues(array $inputProperties, string $prefix = null): array
    {
        $params = [];

        foreach ($inputProperties as $property => $value)
            $params[':' . $prefix . $property] = '%' . $value . '%';

        return $params;
    }

    protected final function getQueryParameterValues(array $inputProperties, string $prefix = null): array
    {
        $params = [];

        foreach ($inputProperties as $property => $value)
        {
            if (is_object($value) || is_array($value))
                 $value = json_encode($value);

            $params[':' . $prefix . $property] = $value;
        }

        return $params;
    }

    protected function getCustomParameters(string $paramString, string $splitter)
    {
        $queryParams = [];
        $keyValuePairs = explode($splitter, $paramString);
        $lastKey = null;


        foreach ($keyValuePairs as $keyValuePair) {
            $paramIndex = strpos($keyValuePair, ':');
            if ($paramIndex > 0) {
                $key = substr($keyValuePair, 0, $paramIndex);
                $value = substr($keyValuePair, $paramIndex + 1);

                $lastKey = $key;
                $queryParams[$lastKey] = $value;
                continue;
            }

            if (isset($lastKey)) {
                $queryParams[$lastKey] = $queryParams[$lastKey] . $splitter . $keyValuePair;
            }
        }

        return $queryParams;
    }

    protected function getOrderybyQueryString($inputParams): string
    {
        $resultString = null;
        $customParams = $this->getCustomParameters($inputParams, ';');
        $orderByParams = $this->getAssociatePropertiesWithClass($customParams);

        if (count($orderByParams) > 0) {
            $result = [];
            foreach ($orderByParams as $key => $value) {
                if ($value == 'asc' || $value == 'desc')
                    $result[$key] = $value;
            }

            if (count($result) > 0) {
                $resultString = ' ORDER BY ';

                $items = [];
                foreach ($result as $key => $value)
                    $items[] = $key . ' ' . $value;

                $resultString = $resultString . implode(', ', $items);
            }
        }

        return $resultString;
    }

    public function add(array $inputParams) : int
    {
        $params = $this->getAssociatePropertiesWithClass($inputParams);
        $queryValueParams = $this->getQueryParameterValues($params);

        $columns = implode(', ', array_keys($params));
        $parameters = implode(', ', array_keys($queryValueParams));
        $query = sprintf(static::ADD_QUERY, $this->getTableName(), $columns, $parameters);

        $statement = \DbContext::getConnection()->prepare($query);
        $statement->execute($queryValueParams);

        return \DbContext::getConnection()->lastInsertId();
    }

    public function get(array $inputParams): array|object|null
    {
        $validParams = $this->getAssociatePropertiesWithClass($inputParams);

        $queryParams = $this->getQueryParameterAssociate($validParams);
        $queryParamValues = $this->getQueryParameterValues($validParams);
        $whereParams = count($queryParams) > 0 ? implode(' AND ', $queryParams) : '1';

        if (isset($inputParams['search'])) {
            $customParams = $this->getCustomParameters($inputParams['search'], ';');
            $searchParams = $this->getAssociatePropertiesWithClass($customParams);
            $searchQueryParams = $this->getQuerySearchParameterAssociate($searchParams, 'ss1_');
            $searchQueryParamValues = $this->getQuerySearchParameterValues($searchParams, 'ss1_');

            if (count($searchQueryParams) > 0) {
                $whereParams = $whereParams . ' AND ' . implode(' AND ', $searchQueryParams);
                $queryParamValues = array_merge($queryParamValues, $searchQueryParamValues);
            }
        }
        $query = sprintf(static::GET_BY_PREDICATE_QUERY, $this->getTableName(), $whereParams);

        if (isset($inputParams['orderby']))
            $query = $query . $this->getOrderybyQueryString($inputParams['orderby']);

        if (isset($inputParams['limit']))
        {
            $query = $query.' LIMIT :ss_limit';
            $queryParamValues[':ss_limit'] = $inputParams['limit'];

            if (isset($inputParams['offset']))
            {
                $query = $query.' OFFSET :ss_offset';
                $queryParamValues[':ss_offset'] = $inputParams['offset'];
            }
        }

        $statement = \DbContext::getConnection()->prepare($query);
        $statement->execute($queryParamValues);

        if (!array_key_exists('id', $inputParams))
            return array_map([$this, 'map'], $statement->fetchAll());

        if (!$data = $statement->fetch())
            return null;

        return $this->map($data);
    }

    public function updateById(array $inputParams)
    {
        $params = $this->getAssociatePropertiesWithClass($inputParams);
        $queryColmParams = $this->getQueryParameterAssociate($params);
        $queryValueParams = $this->getQueryParameterValues($params);

        if (array_key_exists('id', $queryColmParams))
            unset($queryColmParams['id']);

        $stringParams = implode(', ', $queryColmParams);
        $query = sprintf(static::UPDATE_QUERY, $this->getTableName(), $stringParams);
        $statement = \DbContext::getConnection()->prepare($query);
        $statement->execute($queryValueParams);
    }

    public function removeById(array $inputParams)
    {
        $query = sprintf(static::REMOVE_BY_ID, $this->getTableName());
        $statement = \DbContext::getConnection()->prepare($query);
        $queryValueParams = [
            'id' => $inputParams['id']
        ];
        $statement->execute($queryValueParams);
    }
}

?>