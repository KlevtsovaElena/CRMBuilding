<?php
    namespace models;
    
    class OrderVendor
    {
        public int $id;
        public int $order_id;
        public int $vendor_id;
        public ?array $products;
        public int $status;
        public int $archive;
        public int $total_price;
        public float $distance;
        public int $notification_count;
        public bool $debt_accrued;
    }
?>