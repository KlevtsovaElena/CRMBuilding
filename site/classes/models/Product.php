<?php
    namespace models;

    class Product
    {
        public int $id;
        public string $name;
        public ?string $description;
        public string $photo;
        public ?int $article;
        public int $category_id;
        public int $brand_id;
        public int $vendor_id;
        public int $quantity_available;
        public int $price;
        public int $max_price;
        public int $unit_id;
        public int $deleted;
    }
?>