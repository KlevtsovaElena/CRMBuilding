<?php require('../handler/check-profile.php'); 
if($role !== 1) {
    setcookie('profile', '', -1, '/');
    header('Location: ' . $mainUrl . '/pages/login.php');
    exit(0);
};
?>

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
        $brandsJson = file_get_contents($nginxUrl . "/api/brands.php?deleted=0&orderby=brand_name:asc");
        $brands = json_decode($brandsJson, true);

        $categoriesJson = file_get_contents($nginxUrl . "/api/categories.php?deleted=0&orderby=category_name:asc");
        $categories = json_decode($categoriesJson, true);

        $unitsJson = file_get_contents($nginxUrl . "/api/units.php?deleted=0&orderby=name:asc");
        $units = json_decode($unitsJson, true);

    ?>
                        
    <!-- Форма редактирования товара -->
    <section>
        <form id="form-add-product" action="#" method="post" enctype="multipart/form-data" product-id="<?= $id; ?>">
            
            <!-- здесь храним id поставщика -->
            <input type="hidden" id="vendor_id" name="vendor_id" value="">
            
        
            <!-- пропишем в форму данные товара по id -->
            <?php 
                $productJson = file_get_contents($nginxUrl . "/api/products/get-with-details.php?deleted=0&id=" . $id);
                $product = json_decode($productJson, true);
            ?>
            <div class="form-add-product__elements form-elements-container">
                <!-- имя поставщика -->

                <p>Поставщик: 
                    <b>
                    <?= $product[0]['vendor_name']; ?>
                    </b>
                </p>
                <br>

                <!-- наименование-->
                <div class="form-add-product__elements-item">
                    <p>Наименование (заполните хотя бы 1 вариант)</p>
                    <div class="name-container"> 
                        <?php 
                            if($product[0]['name'] == NULL || $product[0]['name'] == '') {$name='';} else {$name=$product[0]['name'];}
                            if($product[0]['name2'] == NULL || $product[0]['name2'] == '') {$name2='';} else {$name2=$product[0]['name2'];}
                            if($product[0]['name3'] == NULL || $product[0]['name3'] == '') {$name3='';} else {$name3=$product[0]['name3'];}
                        ?>       
                        <!-- наименование Русский-->
                        <p>Русский</p><input type="text" id="name" name="name" value="<?= $name; ?>" required>
                        <!-- наименование Оʻzbekcha-->
                        <p>Оʻzbekcha</p><input type="text" id="name2" name="name2" value="<?= $name2; ?>">
                        <!-- наименование, Ўзбекча -->
                        <p>Ўзбекча</p><input type="text" id="name3" name="name3" value="<?= $name3; ?>">
                    </div>
                </div>            

                <!-- описание-->
                <div class="form-add-product__elements-item">
                    <p>Описание (заполните хотя бы 1 вариант)</p>
                    <div class="description-container"> 
                        <?php 
                            if($product[0]['description'] == NULL || $product[0]['description'] == '') {$description='';} else {$description=$product[0]['description'];}
                            if($product[0]['description2'] == NULL || $product[0]['description2'] == '') {$description2='';} else {$description2=$product[0]['description2'];}
                            if($product[0]['description3'] == NULL || $product[0]['description3'] == '') {$description3='';} else {$description3=$product[0]['description3'];}
                        ?>   
                        <!-- описание Русский -->
                        <p>Русский</p><textarea id="description" name="description" required><?= $description; ?></textarea>
                        <!-- описание Оʻzbekcha-->
                        <p>Оʻzbekcha</p><textarea id="description2" name="description2"><?= $description2; ?></textarea>
                        <!-- описание Ўзбекча-->
                        <p>Ўзбекча</p><textarea id="description3" name="description3"><?= $description3; ?></textarea> 
                    </div> 
                </div>

                <!-- фото -->
                <div class="form-add-product__elements-item">
                    <p>Изображениe для карточки</p> 
                    <p>(Рекомендованные пропорции 3:2)</p>
                    <p>(Допустимые форматы: .jpg, .jpeg, .png)</p>
                    <div class="form-add-product__elements-item__img">
                        
                        <div class="form-add-product__elements-item__img-prew"><img src="<?= $product[0]['photo']; ?>" alt="изображение товара"></div>
                        <input type="hidden"  id="photo" name="photo" value="<?= $product[0]['photo']; ?>">
                        <input type="file"  id="new_photo" name="new_photo" accept="image/png, image/jpg, image/jpeg" onchange="loadFile()">  
                        <div><progress id="progress" max="100" value="0" class="d-none"></progress></div>
                                
                    </div>
                    <div class="error-info d-none"></div> 
                </div>

                <!-- категория -->
                <div class="form-add-product__elements-item">
                    <p>Категория</p>
                    <select id="category_id" name="category_id" value="" required>
                        <option value="<?= $product[0]['category_id']; ?>" selected hidden><?= $product[0]['category_name']; ?></option>
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
                        <option value="<?= $product[0]['brand_id']; ?>" selected hidden><?= $product[0]['brand_name']; ?></option>
                        <?php foreach($brands as $brand) { ?>
                            <option value="<?= $brand['id']; ?>"><?= $brand['brand_name']; ?></option>
                        <?php }; ?>
                    </select>
                    <div class="error-info d-none"></div> 
                </div>

                <!-- артикул -->
                <div class="form-add-product__elements-item d-none">
                    <p>Артикул (число)</p><input type="number" id="article" name="article" min="0" value="<?= $product[0]['article']; ?>" placeholder="0">
                    <div class="error-info d-none"></div>
                </div> 

                <!-- количество остатков -->
                <div class="form-add-product__elements-item">
                    <p>Количество остатков</p><input type="number" id="quantity_available" name="quantity_available" min="0" value="<?= $product[0]['quantity_available']; ?>" required placeholder="0">
                    <div class="error-info d-none"></div> 
                </div>

                <!-- единица измерения -->
                <div class="form-add-product__elements-item">
                    <p>Единица измерения</p>
                    <select id="unit_id" name="unit_id" value="" required>
                        <option value="<?= $product[0]['unit_id']; ?>" selected hidden><?= $product[0]['unit_name']; ?></option>
                        <?php foreach($units as $unit) { ?>
                            <option value="<?= $unit['id']; ?>"><?= $unit['name']; ?></option>
                        <?php }; ?>
                    </select>
                    <div class="error-info d-none"></div> 
                </div>

                <!-- статус -->
                <div class="form-add-product__elements-item">
                    <p>Статус</p>
                    <select id="is_active" name="is_active" value="" required>
                        <option value="1" <?php if($product[0]['is_active'] == 1) {echo 'selected';} ?>>Активен</option>
                        <option value="0" <?php if($product[0]['is_active'] == 0) {echo 'selected';} ?>>Не активен</option>
                    </select>
                    <div class="error-info d-none"></div>
                </div> 


                <!-- статус -->
                <div class="form-add-product__elements-item">
                    
                    <select id="is_confirm" name="is_confirm" value="" required>
                        <option value="1" <?php if($product[0]['is_confirm'] == 1) {echo 'selected';} ?>>Утверждён</option>
                        <option value="0" <?php if($product[0]['is_confirm'] == 0) {echo 'selected';} ?>>Не утверждён</option>
                    </select>
                    <div class="error-info d-none"></div>
                </div> 

            <!-- в зависимости от валюты поставщика -->
            <?php if ($product[0]['vendor_currency_dollar'] == "0") { ?>
                
                <!-- цена поставщика сум-->
                <div class="form-add-product__elements-item">
                    <p>Цена, Сум </p><input type="number" id="price" name="price" min="0" value="<?= $product[0]['price']; ?>" required placeholder="0"  onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                    <input type="hidden" id="price_dollar" name="price_dollar" min="0" value="<?= $product[0]['price_dollar']; ?>">
                    <div class="error-info d-none"></div> 
                </div>

                <!-- среднерыночная цена сум-->
                <div class="form-add-product__elements-item">
                    <p>Цена рынок, Сум </p><input type="number" id="max_price" name="max_price" min="0" value="<?= $product[0]['max_price']; ?>" required placeholder="0"  onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                    <input type="hidden" id="max_price_dollar" name="max_price_dollar" min="0" value="<?= $product[0]['max_price_dollar']; ?>">
                    <div class="error-info d-none"></div> 
                </div> 
            <?php } else { ?>
 
                <!-- цена поставщика $-->
                <div class="form-add-product__elements-item">
                    <p>Цена, $ </p>
                    <input type="number" id="price_dollar" name="price_dollar" min="0" value="<?php if($product[0]['price_dollar'] !== 0) {echo $product[0]['price_dollar'];} ?>" required placeholder="0" class="price-dollar-add" rate="<?= $product[0]['vendor_rate']; ?>" onchange="calcPriceUzs()"  onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                    <input type="hidden" id="price" name="price" min="0" value="<?= $product[0]['price']; ?>" class="price-value">
                    <span>$ = </span><span class="price-uzs"><b><?= $product[0]['price']; ?></b></span><span> Сум</span>
                    <div class="error-info d-none"></div> 
                </div>

                <!-- среднерыночная цена $-->
                <div class="form-add-product__elements-item">
                    <p>Цена рынок, $ </p>
                    <input type="hidden" id="max_price" name="max_price" min="0" value="<?= $product[0]['max_price']; ?>" class="price-value">
                    <input type="number" id="max_price_dollar" name="max_price_dollar" min="0" value="<?php if($product[0]['max_price_dollar'] !== 0) {echo $product[0]['max_price_dollar'];} ?>" required placeholder="0" class="max_price-dollar-add" rate="<?= $product[0]['vendor_rate']; ?>" onchange="calcPriceUzs()"  onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                    <span>$ = </span><span class="price-uzs"><b><?= $product[0]['max_price']; ?></b></span><span> Сум</span>
                    <div class="error-info d-none"></div> 
                </div> 
                 
            <?php }  ?>               

            </div>

            <div class="btn-group-3">
                <div>
                    <button class="btn btn-ok" onclick="editProduct(1)">Сохранить</button>
                    <a href="admin-edit-product.php?id=<?= $id; ?>" class="btn btn-neutral">Сбросить изменения</a> 
                </div>
            
                <div class="btn btn-delete" onclick="deleteProductFromEditForm(<?= $id; ?>)">Удалить товар</div>
            </div>
        </form>
    </section>


<?php include('./../components/footer.php'); ?>