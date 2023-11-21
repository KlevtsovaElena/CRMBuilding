<?php
    namespace repositories;
    require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use models\Order;
    use abstraction\BaseRepository;
    use models\Coordinate;
    use utils\SqlHelper;

    class OrderRepository extends BaseRepository
    {
        const TABLE_NAME = 'orders';
        const CLASS_NAME = 'models\Order';

        private CoordinateRepository $coordinateRepository;

        public function __construct()
        {
            parent::__construct();
            $this->coordinateRepository = new CoordinateRepository();
        }

        public function getTableName() : string
        {
            return static::TABLE_NAME;
        }

        public function getObjectClassName() : string
        {
            return static::CLASS_NAME;
        }

        public function map(array $row): Order
        {
            $item = new Order();
            
            foreach(SqlHelper::filterParamsByNames($this->entityFields, $row) as $key => $value)
            {
                if ($key == 'location')
                {
                    $item->$key = isset($value) && strlen($value) > 0 ? $this->coordinateRepository->map(json_decode($value, true)) : new Coordinate();
                    continue;
                }

                if ($key == 'products')
                {
                    $item->$key = isset($value) ? json_decode($value, true) : [];
                    continue;
                }

                $item->$key = $value;
            }

            return $item;
        }

        public function getById(int $id) : Order|null
        {
            return $this->get(["id" => $id]);
        }
    }
?>