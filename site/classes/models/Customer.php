<?php
    namespace models;

    class Customer
    {
        public int $id;
        public string $first_name;
        public ?string $last_name;
        public ?string $phone;
        public int $city_id;
        public int $tg_id;
        public string $tg_username;
    }
?>