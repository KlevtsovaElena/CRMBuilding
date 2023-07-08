<?php 
    // собираем массив из подключаемых файлов css и js
    $styleSrc = [
        "<link rel='stylesheet' href='./../assets/css/base.css'>",
        "<link rel='stylesheet' href='./../assets/css/add-product.css'>"
    ];
    $scriptsSrc = [
        "<script src='./../assets/js/main.js'></script>",
        "<script src='./../assets/js/add-product.js'></script>"
    ];
?>

<!-- подключим хэдер -->
<?php include('./../components/header.php'); ?>
                
    <p class="page-title">Добавить товар</p>

    <!-- соберём данные для отображения в форме -->
    <?php
        $brandsJson = file_get_contents("http://nginx/api/brands.php");
        $brands = json_decode($brandsJson, true);

        $categoriesJson = file_get_contents("http://nginx/api/categories.php");
        $categories = json_decode($categoriesJson, true);

        $unitsJson = file_get_contents("http://nginx/api/units.php");
        $units = json_decode($unitsJson, true);
    ?>
                        
    <!-- Форма добавления товаров -->
    <form id="form-add-product" action="#" method="post" enctype="multipart/form-data">
        
        <!-- здесь храним id поставщика -->
        <input type="hidden" id="vendor_id" name="vendor_id" value="<?= $vendor_id; ?>">
        
       
        <div class="form-add-product__elements form-elements-container">

             <!-- наименование -->
            <div class="form-add-product__elements-item">
                <p>Наименование</p><input type="text" id="name" name="name" value="" required>
                <div class="error-info d-none"></div>
            </div>

            <!-- фото -->
            <div class="form-add-product__elements-item">
                <p>Изображениe для карточки</p> 
                <p>(Рекомендованные пропорции 3:2)</p>
                <p>(Допустимые форматы: .jpg, .jpeg, .png)</p>
                <div class="form-add-product__elements-item__img">
                    
                    <div class="form-add-product__elements-item__img-prew"><img></div>
                    <input type="file"  id="new_photo" name="new_photo" accept="image/png, image/jpg, image/jpeg" required onchange="loadFile()">  
                    <div><progress id="progress" max="100" value="0" class="d-none"></progress></div>
                            
                </div>
                <div class="error-info d-none"></div> 
            </div>

            <!-- категория -->
            <div class="form-add-product__elements-item">
                <p>Категория</p>
                <select id="category_id" name="category_id" value="" required>
                    <option value="" selected hidden>Выберите категорию...</option>

                    <?php foreach($categories as $category) { ?>
                        <option value="<?= $category['id']; ?>"><?= $category['category_name']; ?></option>
                    <?php }; ?>

                </select>
                <div class="error-info d-none"></div> 
            </div>

            <!-- бренд -->
            <div class="form-add-product__elements-item">
                <p>Бренд</p>
                <select id="brand_id" name="brand_id" value="" required>
                    <option value="" selected hidden>Выберите бренд...</option>

                    <?php foreach($brands as $brand) { ?>
                        <option value="<?= $brand['id']; ?>"><?= $brand['brand_name']; ?></option>
                    <?php }; ?>

                </select>
                <div class="error-info d-none"></div> 
            </div>

            <!-- описание -->
            <div class="form-add-product__elements-item">
                <p>Описание</p><textarea id="description" name="description"></textarea>
                <div class="error-info d-none"></div> 
            </div>

            <!-- артикул -->
            <div class="form-add-product__elements-item d-none">
                <p>Артикул (число)</p><input type="number" id="article" name="article" min="0" value="0" placeholder="0">
                <div class="error-info d-none"></div>
            </div> 

            <!-- количество остатков -->
            <div class="form-add-product__elements-item">
                <p>Количество остатков</p><input type="number" id="quantity_available" name="quantity_available" min="0" value="" required placeholder="0">
                <div class="error-info d-none"></div> 
            </div>

            <!-- единица измерения -->
            <div class="form-add-product__elements-item">
                <p>Единица измерения</p>
                <select id="unit_id" name="unit_id" value="" required>
                    <option value="" selected hidden>Выберите ед. измерения...</option>

                    <?php foreach($units as $unit) { ?>
                        <option value="<?= $unit['id']; ?>"><?= $unit['name']; ?></option>
                    <?php }; ?>

                </select>
                <div class="error-info d-none"></div> 
            </div>

            <!-- цена поставщика -->
            <div class="form-add-product__elements-item">
                <p>Цена </p><input type="number" id="price" name="price" min="0" value="" required placeholder="0">
                <div class="error-info d-none"></div> 
            </div>

            <!-- среднерыночная цена -->
            <div class="form-add-product__elements-item">
                <p>Цена среднерыночная </p><input type="number" id="max_price" name="max_price" min="0" value="" required placeholder="0">
                <div class="error-info d-none"></div> 
            </div> 

        </div>

        <div>
            <button class="btn btn-ok" onclick="addProduct()">Сохранить</button>
            <a href="vendor-add-product.php" class="btn btn-neutral">Сбросить изменения</a> 
        </div>
    </form>

<!-- подключим футер -->
<?php include('./../components/footer.php'); ?>