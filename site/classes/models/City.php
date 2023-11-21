<?php
    namespace models;

    class City
    {
        public int $id;
        public string $name;
        public int $is_active; //Настя: добавила для нового поля
        public int $deleted; //Настя: добавила для нового поля
    }
?>