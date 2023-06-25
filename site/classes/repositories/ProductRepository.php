<?php 
    namespace repositories;

    require_once($_SERVER['DOCUMENT_ROOT'].'/classes/autoloader.php');
    
    use models\Product;
    use abstraction\BaseRepository as BaseRepository;

    class ProductRepository extends BaseRepository
    {
        const TABLE_NAME = 'products';
        const CLASS_NAME = 'models\Product';    

        public function getTableName() : string
        {
            return static::TABLE_NAME;
        }

        public function getObjectClassName() : string
        {
            return static::CLASS_NAME;
        }

        public function map(array $row): Product
        {
            $item = new Product();
            foreach ($this->getAssociatePropertiesWithClass($row) as $key => $value)   
                $item->$key = $value;
    
            return $item;
        }

        public function getById(int $id) : Product|null
        {
            return $this->get(["id" => $id]);
        }
    }
?>