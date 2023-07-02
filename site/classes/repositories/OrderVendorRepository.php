<?php
    namespace repositories;
    require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use models\OrderVendor;
    use abstraction\BaseRepository;
    use utils\SqlHelper;

    class OrderVendorRepository extends BaseRepository
    {
        const TABLE_NAME = 'order_vendors';
        const CLASS_NAME = 'models\OrderVendor';

        const GET_WITH_DETAILS = 'SELECT ov.`id` as `id`,
                                        ov.`order_id` as `order_id`,
                                        ov.`vendor_id` as `vendor_id`,
                                        v.`coordinates` as `vendor_location`,
                                        o.`order_date` as `order_date`,
                                        ov.`status` as `status`,
                                        c.`phone` as `customer_phone`,
                                        o.`location` as `order_location`,
                                        ov.`products` as `products`
                                    FROM order_vendors ov
                                    INNER JOIN vendors v
                                    ON v.id = ov.vendor_id
                                    INNER JOIN orders o
                                    ON o.id = ov.order_id
                                    INNER JOIN customers c
                                    ON c.id = o.customer_id';

        public function getTableName() : string
        {
            return static::TABLE_NAME;
        }

        public function getObjectClassName() : string
        {
            return static::CLASS_NAME;
        }

        public function map(array $row): OrderVendor
        {
            $item = new OrderVendor();
            
            foreach(SqlHelper::filterParamsByNames($this->entityFields, $row) as $key => $value)
            {
                if ($key == 'products')
                {
                    $item->$key = isset($value) ? json_decode($value, true) : [];
                    continue;
                }

                $item->$key = $value;
            }

            return $item;
        }

        public function mapWithDetails(array $row): array
        {
            $item = [];
            foreach ($row as $key => $value)
            {
                if ($key == 'products' || $key == 'order_location' || $key == 'vendor_location')
                {
                    $item[$key] = isset($value) ? json_decode($value, true) : [];
                    continue;
                }

                $item[$key] = $value;
            }

            return $item;
        }

        public function getWithDetails() : array
        {
            $query = sprintf(static::GET_WITH_DETAILS);

            $statement = \DbContext::getConnection()->prepare($query);
            $statement->execute();

            return array_map([$this, 'mapWithDetails'], $statement->fetchAll());
        }
    }
?>