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
    }
?>