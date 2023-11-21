<?php
    namespace models;

    class PriceChange
    {
        public int $id;
        public int $product_id;
        public int $date_change;
        public int $old_price;
        public int $new_price;
    }
?>