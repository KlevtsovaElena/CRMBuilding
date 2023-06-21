<?php
    namespace models;

    class Customer
    {
        public int $id;
        public string $firstName;
        public ?string $lastName;
        public string $tgUsername;
        public int $tgId;
        public ?string $phone;
        public int $cityId;
        public $coordinates;  // JSON
    }
?>