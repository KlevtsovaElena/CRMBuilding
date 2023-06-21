<?php 
    namespace repositories;
    require_once($_SERVER['DOCUMENT_ROOT'].'/classes/autoloader.php');
    
    use models\Brand;

    class BrandRepository
    {
        const ADD_QUERY = 'INSERT INTO `brands`(`brand_name`) VALUES (:brand_name)';
        const GET_BY_ID_QUERY = 'SELECT `id`, `brand_name` FROM `brands` WHERE `id`=:id';
        const REMOVE_BY_ID = 'DELETE FROM `brands` WHERE `id`=:id';
        const GET_ALL_QUERY = 'SELECT `id`, `brand_name` FROM `brands`';
        const UPDATE_QUERY = 'UPDATE `brands` SET `brand_name`=:brand_name WHERE `id`=:id';

        public function map(array $row) : Brand
        {
            $newBrand = new Brand();
            $newBrand->id = $row['id'];
            $newBrand->brandName = $row['brand_name'];

            return $newBrand;
        }

        public function add(Brand $brand)
        {
            $statement = \DbContext::getConnection()->prepare(static::ADD_QUERY);
            $params = [
                ':brand_name' => $brand->brandName
            ];
            $statement->execute($params);
        }

        public function getById(int $id) : ?Brand
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

        public function update(Brand $brand)
        {
            $statement = \DbContext::getConnection()->prepare(static::UPDATE_QUERY);
            $statement->execute(array(':brand_name' => $brand->brandName, ':id' => $brand->id));
        }
    }
?>