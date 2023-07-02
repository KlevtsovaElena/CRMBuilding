<?php
namespace utils;

class ParameterHelper
{
    public static function getFilteredParameters(array $filteredNames, array $parameters): array
    {
        $filteredProperties = [];
        foreach ($filteredNames as $name) {
            if (array_key_exists($name, $parameters))
                $filteredProperties[$name] = $parameters[$name];
        }
        return $filteredProperties;
    }

    public static function getFromStringParameters(string $string, string $splitter): array
    {
        $queryParams = [];
        $keyValuePairs = explode($splitter, $string);
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

    public static function getSearchParameters(array $inputParams) : array
    {
        if (!isset($inputParams['search']))
            return [];

        return static::getFromStringParameters($inputParams['search'], ';');
    }

    public static function getOrderByParameters(array $inputParams) : array
    {
        if (!isset($inputParams['orderby']))
            return [];

            return static::getFromStringParameters($inputParams['orderby'], ';');
    }

    public static function getParametersWithSuffix(array $inputParams, string $suffix)
    {
        $result = [];

        foreach($inputParams as $key => $value)
            $result[$suffix.$key] = $value;
        
        return $result;
    }
}
?>