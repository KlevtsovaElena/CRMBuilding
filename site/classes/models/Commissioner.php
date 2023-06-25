<?php
    namespace models;

    class Commissioner
    {
        public int $id;
        public string $name;
        public ?int $phone;
        public ?string $email;
        public ?string $tgUsername;
        public ?int $tgId;
    }
?>