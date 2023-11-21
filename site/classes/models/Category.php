<?php
    namespace models;

    class Category
    {
        public int $id;
        public string $category_name;
        public int $deleted; //Настя: добавила для нового поля
    }
?>