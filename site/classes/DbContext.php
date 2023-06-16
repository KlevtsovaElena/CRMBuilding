<?php
    class DbContext
    {
        private static $connection;

        public static function GetConnection() : PDO
        {
            if (static::$connection == null)
                static::$connection = static::CreateConnection();

            return static::$connection;
        }

        private static function CreateConnection() : PDO 
        {
            $host = $_ENV['MYSQL_HOST'];
            $database = $_ENV['MYSQL_DB'];
            $username = $_ENV['MYSQL_USER'];
            $password = $_ENV['MYSQL_PASSWORD'];
            $charset = $_ENV['MYSQL_CHARSET'];

            $connectionString = "mysql:host=$host;dbname=$database;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            $con = new PDO($connectionString, $username, $password, $options);
            return $con;
        }
    }
?>