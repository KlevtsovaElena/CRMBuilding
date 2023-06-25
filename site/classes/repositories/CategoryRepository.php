<?php 
    namespace repositories;
    require_once($_SERVER['DOCUMENT_ROOT'].'/classes/autoloader.php');
    
    use models\Category;
    use abstraction\BaseRepository;

    class CategoryRepository extends BaseRepository
    {
        const TABLE_NAME = 'categories';
        const CLASS_NAME = 'models\Category';
        
        public function getTableName() : string
        {
            return static::TABLE_NAME;
        }
    
        public function getObjectClassName() : string
        {
            return static::CLASS_NAME;
        }
    
        public function map(array $row): Category
        {
            $item = new Category();
            foreach ($this->getAssociatePropertiesWithClass($row) as $key => $value)
                $item->$key = $value;
    
            return $item;
        }
    }
?>