<?php 
    namespace repositories;
    require_once($_SERVER['DOCUMENT_ROOT'].'/classes/autoloader.php');
    
    use models\Product;

    class ProductRepository
    {
        const ADD_QUERY = 'INSERT INTO products(`name`, `description`, `photo`, `article`, `category_id`, `brand_id`, `vendor_id`, `quantity_available`, `price`, `max_price`) VALUES '.
                            '(:name, :description, :photo, :article, :category_id, :brand_id, :vendor_id, :quantity_available, :price, :max_price)';
        const GET_BY_ID_QUERY = 'SELECT `id`, `name`, `description`, `photo`, `article`, `category_id`, `brand_id`,
                                 `vendor_id`, `quantity_available`, `price`, `max_price`
                                 FROM `products` WHERE `id`=:id';
        const REMOVE_BY_ID = 'DELETE FROM `products` WHERE `id`=:id';
        const GET_ALL_QUERY = 'SELECT `id`, `name`, `description`, `photo`, `article`, `category_id`, `brand_id`, `vendor_id`, `quantity_available`, `price`, `max_price` FROM `products`';
        const UPDATE_QUERY = 'UPDATE `products` 
                                SET `name`=:name,
                                    `description`=:description,
                                    `photo`=:photo,
                                    `article`=:article,
                                    `category_id`=:category_id,
                                    `brand_id`=:brand_id,
                                    `vendor_id`=:vendor_id,
                                    `quantity_available`=:quantity_available,
                                    `price`=:price,
                                    `max_price`=:max_price 
                              WHERE `id`=:id';

        public function map(array $row) : Product
        {
            $newProduct = new Product();
            $newProduct->id = $row['id'];
            $newProduct->name = $row['name'];
            $newProduct->description = $row['description'];
            $newProduct->photo = $row['photo'];
            $newProduct->article = $row['article'];
            $newProduct->categoryId = $row['category_id'];
            $newProduct->brandId = $row['brand_id'];
            $newProduct->vendorId = $row['vendor_id'];
            $newProduct->quantityAvailable = $row['quantity_available'];
            $newProduct->price = $row['price'];
            $newProduct->maxPrice = $row['max_price'];

            return $newProduct;
        }

        public function add(Product $product)
        {
            $statement = \DbContext::getConnection()->prepare(static::ADD_QUERY);
            $params = [
                ':name' => $product->name,
                ':description' => $product->description,
                ':photo' => $product->photo,
                ':article' => $product->article,                
                ':category_id' => $product->categoryId,
                ':brand_id' => $product->brandId, 
                ':vendor_id' => $product->vendorId,
                ':quantity_available' => $product->quantityAvailable,
                ':price' => $product->price, 
                ':max_price' => $product->maxPrice
            ];
            $statement->execute($params);
        }

        public function getById(int $id) : ?Product
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

        public function update(Product $product)
        {
            $statement = \DbContext::getConnection()->prepare(static::UPDATE_QUERY);
            $params = [
                ':name' => $product->name,
                ':description' => $product->description,
                ':photo' => $product->photo,
                ':article' => $product->article,
                ':category_id' => $product->categoryId,
                ':brand_id' => $product->brandId,
                ':vendor_id' => $product->vendorId,
                ':quantity_available' => $product->quantityAvailable,
                ':price' => $product->price,
                ':max_price' => $product->maxPrice,
                ':id' => $product->id,
            ];
            $statement->execute($params);
        }
    }
?>