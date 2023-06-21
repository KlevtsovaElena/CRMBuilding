<?php 
    namespace repositories;
    require_once($_SERVER['DOCUMENT_ROOT'].'/classes/autoloader.php');
    
    use models\Category;

    class CategoryRepository
    {
        const ADD_QUERY = 'INSERT INTO `categories`(`category_name`) VALUES (:category_name)';
        const GET_BY_ID_QUERY = 'SELECT `id`, `category_name` FROM `categories` WHERE `id`=:id';
        const REMOVE_BY_ID = 'DELETE FROM `categories` WHERE `id`=:id';
        const GET_ALL_QUERY = 'SELECT `id`, `category_name` FROM `categories`';
        const UPDATE_QUERY = 'UPDATE `categories` SET `category_name`=:category_name WHERE `id`=:id';

        public function map(array $row) : Category
        {
            $newCategory = new Category();
            $newCategory->id = $row['id'];
            $newCategory->categoryName = $row['category_name'];

            return $newCategory;
        }

        public function add(Category $category)
        {
            $statement = \DbContext::getConnection()->prepare(static::ADD_QUERY);
            $params = [
                ':category_name' => $category->categoryName
            ];
            $statement->execute($params);
        }

        public function getById(int $id) : ?Category
        {
            $statement = \DbContext::getConnection()->prepare(static::GET_BY_ID_QUERY);
            $statement->execute(array(':id' => $id));
            
            $result = null;
            if ($data = $statement->fetch())
                $result = $this->map($data);

            return $result;        
        }

        public function getAll() : Array
        {
            $statement = \DbContext::getConnection()->prepare(static::GET_ALL_QUERY);
            $statement->execute();

            $result = array_map([$this, 'map'], $statement->fetchAll());

            return $result;        
        }

        public function removeById(int $id)
        {
            $statement = \DbContext::getConnection()->prepare(static::REMOVE_BY_ID);
            $statement->execute(array(':id' => $id));
        }

        public function update(Category $category)
        {
            $statement = \DbContext::getConnection()->prepare(static::UPDATE_QUERY);
            $statement->execute(array(':category_name' => $category->categoryName, ':id' => $category->id));
        }
    }
?>