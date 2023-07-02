<?php
    namespace utils;

    // Вспомогательный класс для преобразования данных в формат запросов
    class DatabaseHelper
    {
        // Получить параметризованное представление входного параметра по имени
        // Пример: ":id"
        private static function getSqlParamByName(string $name) : string
        {
            return ':' . $name;
        }

        // Получить массив параметризованных представлений входных параметров по списку имен
        // Ключ - исходное имя, значение - параметризованное представление
        // Пример: ["id" => ":id"]
        public static function getSqlParamArrayByNames(array $names) : array
        {
            $params = [];
    
            foreach ($names as $name)
                $params[$name] = static::getSqlParamByName($name);
    
            return $params;
        }

        // Получить параметризованное представление входного параметра для условия однозначного соответствия
        // Пример: "id=:id"
        public static function getSqlWhereParamByParamName(string $paramName) : string 
        {
            return $paramName . '=' .static::getSqlParamByName($paramName);
        }

        // Получить массив параметризованных представлений входных параметров для условия однозначного соответствия
        // Пример: [ "id" => "id=:id" ]
        public static function getSqlWhereParamArrayByParamNames(array $paramNames) : array
        {
            $params = [];
    
            foreach ($paramNames as $paramName)
                $params[$paramName] = static::getSqlWhereParamByParamName($paramName);
    
            return $params;
        }

        // Получить часть строки запроса с фильтром по горизонтали (WHERE) по входным параметрам с однозначным соответствием, если параметров нет - WHERE 1
        // splitOperation: разделитель параметров (OR, AND)
        // Примеры: "WHERE id=:id AND id_second=:id_second", "WHERE 1", "WHERE id=:id OR id_second=:id_second"
        public static function getSqlWhereQueryStringByParamNames(array $paramNames, string $splitOperation) : string 
        {
            $whereParams = static::getSqlWhereParamArrayByParamNames($paramNames);

            if (is_null($whereParams) || count($whereParams) == 0 || !static::isValidSplitOperation($splitOperation))
                return '1';
            
            return implode(' '. $splitOperation .' ', $whereParams);
        }

        // Получить часть строки запроса с параметризованным представление входного параметра для условия вхождения однозначного или частичного по имени
        // Пример: "CONVERT('id' CHAR) LIKE %:id%"
        public static function getSqlWhereLikeQueryStringByParamName(string $paramName, string $paramSuffix = null) : string
        {
            return 'CONVERT(' . $paramName . ', CHAR) LIKE ' . static::getSqlParamByName($paramSuffix.$paramName);
        }

        // Получить массив частей строк запроса с параметризованным представлением входных параметров для условия вхождения однозначного или частичного по именам
        // Пример: [ "id" => "CONVERT('id' CHAR) LIKE %:id%" ]
        public static function getSqlWhereLikeQueryStringArrayByParamNames(array $paramNames, string $paramSuffix = null) : array
        {
            $stringArray = [];

            foreach($paramNames as $paramName)
                $stringArray[$paramName] = static::getSqlWhereLikeQueryStringByParamName($paramName, $paramSuffix);

            return $stringArray;
        }

        // Получить часть строки запроса с параметризованным представлением входных параметров для условия вхождения однозначного или частичного по именам с разделяющей операцией
        // splitOperation: разделитель параметров (OR, AND)
        // Пример: "(CONVERT('id' CHAR) LIKE %:id% AND CONVERT('id_second' CHAR) LIKE %:id_second%)" 
        public static function getSqlWhereLikeQueryStringByParamNames(array $paramNames, string $splitOperation, string $paramSuffix = null) : string|null
        {
            $splitOperation = strtoupper($splitOperation);

            if (is_null($splitOperation) || !static::isValidSplitOperation($splitOperation))
                return null;

            $queryStrings = static::getSqlWhereLikeQueryStringArrayByParamNames($paramNames, $paramSuffix);
            $str = implode(' '. $splitOperation .' ', $queryStrings);

            if (is_null($str) || strlen($str) == 0)
                return null;

            return '(' . $str . ')';
        }

        // Получить строкое представление значения (для объектов и массивов - json)
        private static function getFormatedValue($value)
        {
            if (is_object($value) || is_array($value))
                $value = json_encode($value);

            return $value;
        }

        // Получить массив параметров со значениями
        // Ключ - имя параметра, значение - значение параметра
        // Пример: [ "id" = 1 ]
        public static function getParamArrayWithValues(array $nameValues): array
        {
            $params = [];
    
            foreach ($nameValues as $name => $value)
                $params[$name] = static::getFormatedValue($value);
    
            return $params;
        }

        // Получить часть строки для сортировки по имени и значению параметра
        // Примеры: "id asc", "id_second desc"
        private static function getOrderbyStringValue(string $name, $value) : string|null
        {
            if ($value != 'asc' && $value != 'desc')
                return null;

            return $name . ' ' .$value;
        }

        // Получить строку с перечислением параметров сортировки по массиву входных параметров (имя, значение)
        // Пример: 'ORDER BY id asc, id_second desc'
        public static function getOrderbyQueryString(array $nameValues): string
        {   
            $queryStrings = [];

            foreach($nameValues as $name => $value)
                $queryStrings[] = static::getOrderbyStringValue($name, $value);
            
            $queryStrings = array_filter($queryStrings, fn($value) => !is_null($value));

            return count($queryStrings) > 0 ? 'ORDER BY ' . implode(', ', $queryStrings) : '';
        }

        // Валидировать входной параметр разделения параметров запроса (AND, OR)
        private static function isValidSplitOperation(string $splitOperation) : bool 
        {
            return $splitOperation == 'AND' || $splitOperation == 'OR';
        }

        // Получить параметры запроса, после конвертации входящих параметров в SQL формат
        public static function convertToSqlParamsWithValues(array $inputParams)
        {
            $result = [];

            foreach($inputParams as $name => $value) 
                $result[static::getSqlParamByName($name)] = $value;

            return $result;
        }

        public static function convertToSqlLikeParamsWithValues(array $inputParams)
        {
            $result = [];

            foreach($inputParams as $name => $value) 
                $result[$name] = '%'.$value.'%';

            return $result;
        }
    }
?>