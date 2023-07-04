<?php 
    // собираем массив из подключаемых файлов css и js
    $styleSrc = [
        "<link rel='stylesheet' href='./../assets/css/base.css'>",
        "<link rel='stylesheet' href='./../assets/css/add-product.css'>"
    ];
    $scriptsSrc = [
        "<script src='./../assets/js/main.js'></script>",
        "<script src='./../assets/js/add-product.js'></script>",
        "<script src='./../assets/js/edit-product.js'></script>"
    ];
?>
<?php include('./../components/header.php'); ?>
       
<?php $id = $_GET['id']; ?>

    <p class="page-title">Редактировать товар</p>

        <!-- соберём данные для отображения в форме -->
        <?php
        $brandsJson = file_get_contents("http://nginx/api/brands.php");
        $brands = json_decode($brandsJson, true);

        $categoriesJson = file_get_contents("http://nginx/api/categories.php");
        $categories = json_decode($categoriesJson, true);
    ?>
                        
    <!-- Форма редактирования товара -->
    <section>
        <form id="form-add-product" action="#" method="post" enctype="multipart/form-data" product-id="<?= $id; ?>">
            
            <!-- здесь храним id поставщика -->
            <input type="hidden" id="vendor_id" name="vendor_id" value="<?= $vendor_id; ?>">
            
        
            <!-- пропишем в форму данные товара по id -->
            <?php 
                $productJson = file_get_contents("http://nginx/api/products.php?id=" . $id);
                $product = json_decode($productJson, true);
            ?>
            <div class="form-add-product__elements form-elements-container">

                <!-- наименование -->
                <div class="form-add-product__elements-item">
                    <p>Наименование</p><input type="text" id="name" name="name" value="<?= $product['name']; ?>" required>
                    <div class="error-info d-none"></div>
                </div>

                <!-- фото -->
                <div class="form-add-product__elements-item">
                    <p>Изображениe для карточки</p> 
                    <p>(Рекомендованные пропорции 3:2)</p>
                    <p>(Допустимые форматы: .jpg, .jpeg, .png)</p>
                    <div class="form-add-product__elements-item__img">
                        
                        <div class="form-add-product__elements-item__img-prew"><img src="<?= $product['photo']; ?>" alt="изображение товара"></div>
                        <input type="hidden"  id="photo" name="photo" value="<?= $product['photo']; ?>">
                        <input type="file"  id="new_photo" name="new_photo" accept="image/png, image/jpg, image/jpeg" onchange="loadFile()">  
                        <div><progress id="progress" max="100" value="0" class="d-none"></progress></div>
                                
                    </div>
                    <div class="error-info d-none"></div> 
                </div>

                <!-- категория -->
                <div class="form-add-product__elements-item">
                    <p>Категория</p>
                    <select id="category_id" name="category_id" value="<?= $product['category_id']; ?>" required>

                        <?php foreach($categories as $category) { 
                            if ($category['id'] === $product['category_id']) {
                            ?>
                                <option value="<?= $category['id']; ?>" selected><?= $category['category_name']; ?></option>
                            <?php } else { ?>
                                <option value="<?= $category['id']; ?>"><?= $category['category_name']; ?></option>
                            <?php } 
                        }; ?>

                    </select>
                    <div class="error-info d-none"></div> 
                </div>

                <!-- бренд -->
                <div class="form-add-product__elements-item">
                    <p>Бренд</p>
                    <select id="brand_id" name="brand_id" value="<?= $product['brand_id']; ?>" required>


                        <?php foreach($brands as $brand) { 
                            if ($brand['id'] === $product['brand_id']) {
                            ?>
                                <option value="<?= $brand['id']; ?>" selected><?= $brand['brand_name']; ?></option>
                            <?php } else { ?>
                                <option value="<?= $brand['id']; ?>"><?= $brand['brand_name']; ?></option>
                            <?php } 
                        }; ?>

                    </select>
                    <div class="error-info d-none"></div> 
                </div>

                <!-- описание -->
                <div class="form-add-product__elements-item">
                    <p>Описание</p><textarea id="description" name="description" required><?= $product['description']; ?></textarea>
                    <div class="error-info d-none"></div> 
                </div>

                <!-- артикул -->
                <div class="form-add-product__elements-item">
                    <p>Артикул (число)</p><input type="number" id="article" name="article" min="0" value="<?= $product['article']; ?>" required placeholder="0">
                    <div class="error-info d-none"></div>
                </div> 

                <!-- количество остатков -->
                <div class="form-add-product__elements-item">
                    <p>Количество остатков</p><input type="number" id="quantity_available" name="quantity_available" min="0" value="<?= $product['quantity_available']; ?>" required placeholder="0">
                    <div class="error-info d-none"></div> 
                </div>

                <!-- цена поставщика -->
                <div class="form-add-product__elements-item">
                    <p>Цена </p><input type="number" id="price" name="price" min="0" value="<?= $product['price']; ?>" required placeholder="0">
                    <div class="error-info d-none"></div> 
                </div>

                <!-- среднерыночная цена -->
                <div class="form-add-product__elements-item">
                    <p>Цена среднерыночная </p><input type="number" id="max_price" name="max_price" min="0" value="<?= $product['max_price']; ?>" required placeholder="0">
                    <div class="error-info d-none"></div> 
                </div> 

            </div>

            <div class="btn-group-3">
                <div>
                    <button class="btn btn-ok" onclick="editProduct()">Сохранить</button>
                    <a href="vendor-edit-product.php?id=<?= $id; ?>" class="btn btn-neutral">Сбросить изменения</a> 
                </div>
            
                <div class="btn btn-delete" onclick="deleteProductFromEditForm(<?= $id; ?>)">Удалить товар</div>
            </div>
        </form>
    </section>


<?php include('./../components/footer.php'); ?>