<?php
    namespace models;

    class OrderedProduct
    {
        public int $id;
        public ?int $orderId;
        public int $productId;
        public ?int $vendorId;
        public int $quantity;
        public ?int $status;
    }
?>