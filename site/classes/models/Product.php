<?php
    namespace models;

    class Product
    {
        public int $id;
        public string $name;
        public string $description;
        public ?string $photo;
        public ?int $article;
        public int $categoryId;
        public ?int $brandId;
        public ?int $vendorId;
        public ?int $quantityAvailable;
        public ?int $price;
        public ?int $maxPrice;
    }
?>