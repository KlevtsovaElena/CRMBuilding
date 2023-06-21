<?php
    namespace models;

    class PriceChange
    {
        public int $id;
        public int $productId;
        public int $dateChange;
        public int $newPrice;
    }
?>