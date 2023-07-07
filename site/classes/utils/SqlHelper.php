<?php
    namespace utils;

    class SqlHelper
    {
        private const STRING_PARAMS_SPLITTER = ';';
        private const SEARCH_PARAM_NAME = 'search';
        private const SEARCH_SQL_PARAM_PREFIX = 'search_';
        private const ORDER_BY_PARAM_NAME = 'orderby';
        private const LIMIT_PARAM_NAME = 'limit';
        private const OFFSET_PARAM_NAME = 'offset';

        public static function getLimitString(array $inputParams)
        {
            if (!isset($inputParams[static::LIMIT_PARAM_NAME]) || !is_numeric($inputParams[static::LIMIT_PARAM_NAME]))
                return '';
            
            $limitString = 'LIMIT '.intval($inputParams[static::LIMIT_PARAM_NAME]);

            if (isset($inputParams[static::OFFSET_PARAM_NAME]) && is_numeric($inputParams[static::OFFSET_PARAM_NAME]))
                $limitString .= ' OFFSET '.intval($inputParams[static::OFFSET_PARAM_NAME]);
            
            return $limitString;
        }

        public static function getAllOrderByParams(array $inputParams)
        {
            $allOrderByParams = [];

            if (!isset($inputParams[static::ORDER_BY_PARAM_NAME]))
                return $allOrderByParams;

            $orderByParams = static::getFromStringParameters($inputParams[static::ORDER_BY_PARAM_NAME]);

            foreach($orderByParams as $paramName => $paramValue)
            {
                $value = strtolower($paramValue);

                if ($value == 'asc' || $value == 'desc')
                    $allOrderByParams[$paramName] = $value;
            }

            return $allOrderByParams;
        }

        private static function getOrderByPredicatesArray(array $orderByParams)
        {
            $orderByPredicates = [];

            foreach($orderByParams as $orderByParamName => $orderByParamValue)
                $orderByPredicates[$orderByParamName] = $orderByParamName.' '.$orderByParamValue;

            return $orderByPredicates;
        }

        public static function getOrderByString(array $orderByObjectParams) : string
        {
            if ($orderByObjectParams == null || count($orderByObjectParams) == 0)
                return '';

            return 'ORDER BY '.implode(', ', static::getOrderByPredicatesArray($orderByObjectParams));
        }

        public static function getAllSearchParams(array $inputParams) : array
        {
            $searchParams = [];
            if (!isset($inputParams[static::SEARCH_PARAM_NAME]))
                return $searchParams;
            
            return static::getFromStringParameters($inputParams[static::SEARCH_PARAM_NAME]);            
        }

        public static function convertObjectSearchParams(array $objectSearchParams) : array
        {
            $searchParams = [];

            foreach($objectSearchParams as $objectSearchParamName => $objectSearchParamValue) 
                $searchParams[static::SEARCH_SQL_PARAM_PREFIX.$objectSearchParamName] = '%'.$objectSearchParamValue.'%';
            
            return $searchParams;
        }

        private static function getFromStringParameters(string $string): array
        {
            $queryParams = [];
            $keyValuePairs = explode(static::STRING_PARAMS_SPLITTER, $string);
            $lastKey = null;
    
    
            foreach ($keyValuePairs as $keyValuePair) 
            {
                $paramIndex = strpos($keyValuePair, ':');
                if ($paramIndex > 0) {
                    $key = substr($keyValuePair, 0, $paramIndex);
                    $value = substr($keyValuePair, $paramIndex + 1);
    
                    $lastKey = $key;
                    $queryParams[$lastKey] = $value;
                    continue;
                }
    
                if (isset($lastKey)) {
                    $queryParams[$lastKey] = $queryParams[$lastKey] . static::STRING_PARAMS_SPLITTER . $keyValuePair;
                }
            }
    
            return $queryParams;
        }

        public static function getSearchString(array $searchObjectParams) : string
        {
            if ($searchObjectParams == null || count($searchObjectParams) == 0)
                return '';

            $searchString = 'AND (';

            $predicates = static::getSearchPredicatesArray(array_keys($searchObjectParams));

            $searchString .= implode(' OR ', $predicates).')';

            return $searchString;
        }

        private static function getSearchPredicatesArray(array $paramNames)
        {
            $params = [];

            foreach($paramNames as $paramName)
                $params[$paramName] = static::getSearchPredicate($paramName);
            
            return $params;
        }

        private static function getSearchPredicate(string $paramName)
        {
            return 'CONVERT(' . $paramName . ', CHAR) LIKE ' . static::getSqlParamByName(static::SEARCH_SQL_PARAM_PREFIX.$paramName);
        }

        public static function getWhereString(array $whereParams) : string
        {
            $whereString = 'WHERE ';
            $wherePredicates = static::getEqualParams(array_keys($whereParams));

            if (is_null($wherePredicates) || count($wherePredicates) == 0)
                return $whereString.'1';
            
            return $whereString.implode(' AND ', $wherePredicates);
        }

        public static function getEqualParams(array $paramNames) : array
        {
            $params = [];
    
            foreach ($paramNames as $paramName)
                $params[$paramName] = static::getEqualParam($paramName);
    
            return $params;
        }

        private static function getEqualParam(string $paramName) : string 
        {
            return $paramName . '=' .static::getSqlParamByName($paramName);
        }

        private static function getSqlParamByName(string $name) : string
        {
            return ':' . str_replace('.', '_', $name);
        }

        public static function getSqlParams(array $params)
        {
            $sqlParams = [];

            foreach($params as $name => $value)
                $sqlParams[$name] = static::getSqlParamByName($name);
            
            return $sqlParams;
        }

        public static function filterParamsByNames(array $names, array $params): array
        {
            $filteredParams = [];
            foreach ($names as $name) {
                if (array_key_exists($name, $params))
                {
                    $value = $params[$name];
                    if (is_object($value) || is_array($value))
                        $value = json_encode($value);

                    $filteredParams[$name] = $value;
                }
            }
            return $filteredParams;
        }
        
        public static function filterParamsWithReplace(array $replaceFromNameAndToNamePair, array $inputParams)
        {
            $filteredParams = [];

            foreach($inputParams as $originalName => $value)
            {
                if (array_key_exists($originalName, $replaceFromNameAndToNamePair))
                {
                    $value = $inputParams[$originalName];
                    if (is_object($value) || is_array($value))
                        $value = json_encode($value);
                    
                    $filteredParams[$replaceFromNameAndToNamePair[$originalName]] = $value;
                }
            }

            // foreach($replaceFromNameAndToNamePair as $fromName => $toName)
            // {
            //     if (array_key_exists($fromName, $inputParams))
            //     {
            //         $value = $inputParams[$fromName];
            //         if (is_object($value) || is_array($value))
            //             $value = json_encode($value);

            //         $filteredParams[$toName] = $value;
            //     }
            // }

            return $filteredParams;
        }

        public static function convertToSqlParam(array $params)
        {
            $result = [];

            foreach ($params as $key => $value)
                $result[str_replace('.', '_', static::getSqlParamByName($key))] = $value;

            return $result;
        }
    }
?>