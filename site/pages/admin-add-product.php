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
        "<script src='./../assets/js/add-product.js'></script>"
    ];
?>

<!-- подключим хэдер -->
<?php include('./../components/header.php'); ?>
                
    <p class="page-title">Добавить товар</p>

    <!-- соберём данные для отображения в форме -->
    <?php
        $brandsJson = file_get_contents($nginxUrl . "/api/brands.php?deleted=0");
        $brands = json_decode($brandsJson, true);

        $categoriesJson = file_get_contents($nginxUrl . "/api/categories.php?deleted=0");
        $categories = json_decode($categoriesJson, true);

        $unitsJson = file_get_contents($nginxUrl . "/api/units.php?deleted=0");
        $units = json_decode($unitsJson, true);

        $vendorsJson = file_get_contents($nginxUrl . "/api/vendors.php?role=2&deleted=0");
        $vendors = json_decode($vendorsJson, true);
    ?>
                        
    <!-- Форма добавления товаров -->
    <form id="form-add-product" action="#" method="post" enctype="multipart/form-data">        
       
        <div class="form-add-product__elements form-elements-container">

             <!-- поставщик -->
             <div class="form-add-product__elements-item">
                <p>Поставщик</p>
                <select id="vendor_id" name="vendor_id" value="" required onchange="renderPriceBlock()">
                    <option value="" selected hidden>Выберите поставщика...</option>

                    <?php foreach($vendors as $vendor) { ?>
                        <option value="<?= $vendor['id']; ?>" currency="<?= $vendor['currency_dollar']; ?>" rate="<?= $vendor['rate']; ?>"><?= $vendor['name']; ?></option>
                    <?php }; ?>

                </select>
                <div class="error-info d-none"></div> 
            </div>

            <!-- наименование-->
            <div class="form-add-product__elements-item">
                <p>Наименование</p>
                <div class="name-container">        
                    <!-- наименование Русский-->
                    <p>Русский (обязательно)</p><input type="text" id="name" name="name" value="" required>
                    <div class="error-info d-none"></div>

                    <!-- наименование Оʻzbekcha-->
                    <p>Оʻzbekcha</p><input type="text" id="name2" name="name2" value="">
                    <!-- наименование, Ўзбекча -->
                    <p>Ўзбекча</p><input type="text" id="name3" name="name3" value="">
                </div>
            </div>            

            <!-- описание-->
            <div class="form-add-product__elements-item">
                <p>Описание</p>
                <div class="description-container">    
                    <!-- описание Русский -->
                    <p>Русский (обязательно)</p><textarea id="description" name="description" required></textarea>
                    <div class="error-info d-none"></div> 

                    <!-- описание Оʻzbekcha-->
                    <p>Оʻzbekcha</p><textarea id="description2" name="description2"></textarea>
                    <!-- описание Ўзбекча-->
                    <p>Ўзбекча</p><textarea id="description3" name="description3"></textarea> 
                </div> 
            </div>  

<!--
             
             <div class="form-add-product__elements-item">
                <p>Наименование, Русский (обязательно)</p><input type="text" id="name" name="name" value="" required>
                <div class="error-info d-none"></div>
            </div>
           
            <div class="form-add-product__elements-item">
                <p>Наименование, Оʻzbekcha</p><input type="text" id="name2" name="name2" value="">
                <div class="error-info d-none"></div>
            </div>
           
            <div class="form-add-product__elements-item">
                <p>Наименование, Ўзбекча</p><input type="text" id="name3" name="name3" value="">
                <div class="error-info d-none"></div>
            </div>
-->
<!-- описание Русский
            <div class="form-add-product__elements-item">
                <p>Описание, Русский (обязательно)</p><textarea id="description" name="description"  required></textarea>
                <div class="error-info d-none"></div> 
            </div>
            
            <div class="form-add-product__elements-item">
                <p>Описание, Оʻzbekcha</p><textarea id="description2" name="description2"></textarea>
                <div class="error-info d-none"></div> 
            </div>
            
            <div class="form-add-product__elements-item">
                <p>Описание, Ўзбекча</p><textarea id="description3" name="description3"></textarea>
                <div class="error-info d-none"></div> 
            </div>                        
-->
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

            <!-- статус -->
            <div class="form-add-product__elements-item">
                <p>Статус</p>
                <select id="is_active" name="is_active" value="" required>
                    <option value="1">Активен</option>
                    <option value="0">Не активен</option>
                </select>
                <div class="error-info d-none"></div>
            </div> 

            <!-- статус -->
            <div class="form-add-product__elements-item">
                
                <select id="is_confirm" name="is_confirm" value="" required>
                    <option value="1">Утверждён</option>
                    <option value="0">Не утверждён</option>
                </select>
                <div class="error-info d-none"></div>
            </div> 

            <!-- здесь будут инпуты для цен из шаблона, по умолчанию в сум -->
            <div id="block-price">

            </div>

        </div>

        <div>
            <button class="btn btn-ok" onclick="addProduct(1)">Сохранить</button>
            <a href="admin-add-product.php" class="btn btn-neutral">Сбросить изменения</a> 
        </div>
    </form>

    <!-- шаблон цен в сумах -->
    <template id="tmpl-price-uzs">
        <!-- цена поставщика сум-->
        <div class="form-add-product__elements-item">
            <p>Цена, Сум </p><input type="number" id="price" name="price" min="0" value="" required placeholder="0" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
            <input type="hidden" id="price_dollar" name="price_dollar" min="0" value="0">
            <div class="error-info d-none"></div> 
        </div>

        <!-- среднерыночная цена сум-->
        <div class="form-add-product__elements-item">
            <p>Цена рынок, Сум </p><input type="number" id="max_price" name="max_price" min="0" value="" required placeholder="0"  onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
            <input type="hidden" id="max_price_dollar" name="max_price_dollar" min="0" value="0">
            <div class="error-info d-none"></div> 
        </div> 
    </template>

    <!-- шаблон цен в долларах -->
    <template id="tmpl-price-usd">
        <!-- цена поставщика $-->
        <div class="form-add-product__elements-item">
            <p>Цена, $ </p>
            <input type="number" id="price_dollar" name="price_dollar" min="0" value="" required placeholder="0" class="price-dollar-add" rate="${rate}" onchange="calcPriceUzs()"  onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
            <input type="hidden" id="price" name="price" min="0" value="" class="price-value">
            <span>$ = </span><span class="price-uzs"><b>0</b></span><span> Сум</span>
            <div class="error-info d-none"></div> 
        </div>

        <!-- среднерыночная цена $-->
        <div class="form-add-product__elements-item">
            <p>Цена рынок, $ </p>
            <input type="hidden" id="max_price" name="max_price" min="0" value="" class="price-value">
            <input type="number" id="max_price_dollar" name="max_price_dollar" min="0" value="" required placeholder="0" class="max_price-dollar-add" rate="${rate}" onchange="calcPriceUzs()"  onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
            <span>$ = </span><span class="price-uzs"><b>0</b></span><span> Сум</span>
            <div class="error-info d-none"></div> 
        </div> 
    </template>

<!-- подключим футер -->
<?php include('./../components/footer.php'); ?>