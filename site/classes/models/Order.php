<?php
    namespace models;

    class Order
    {
        public int $id;
        public int $customer_id;
        public int $order_date;
        public ?array $products; // JSON
        public ?Coordinate $location;  // JSON
    }
?>