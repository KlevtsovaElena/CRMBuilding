<?php
    namespace models;

    class Vendor
    {
        public int $id;
        public string $name;
        public int $city_id;
        public ?string $phone;
        public string $email;
        public ?string $tg_username;
        public ?int $tg_id;
        public ?Coordinate $coordinates;
        public int $role;
        public ?string $comment;
        public int $date_reg;
        public ?string $hash_string;
        public int $is_active;
        public string $password;
        public int $deleted; //Настя: добавила для нового поля
    }
?>