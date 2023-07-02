<?php
    namespace repositories;
    require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use models\Coordinate;

    class CoordinateRepository
    {
        const CLASS_NAME = 'models\Coordinate';
        private function getParams($inputArray)
        {
            $items = get_class_vars(static::CLASS_NAME);

            $result = [];
            foreach ($items as $key => $value) {
                if (array_key_exists($key, $inputArray))
                    $result[$key] = $inputArray[$key];
            }
            return $result;
        }

        public function map(array $row): Coordinate
        {
            $item = new Coordinate();
            foreach ($this->getParams($row) as $key => $value)
                $item->$key = $value;

            return $item;
        }
    }
?>