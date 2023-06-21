<?php
    namespace models;

    class Vendor
    {
        public int $id;
        public string $name;
        public int $cityId;
        public ?string $phone;
        public ?string $email;
        public ?string $tgUsername;
        public ?int $tgId;
        public $coordinates; // JSON
    }
?>