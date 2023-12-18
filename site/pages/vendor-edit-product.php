<?php require('../handler/check-profile.php'); 
if($role !== 2) {
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
            <input type="hidden" id="vendor_id" name="vendor_id" value="<?= $vendor_id; ?>">
            
        
            <!-- пропишем в форму данные товара по id -->
            <?php 
                $productJson = file_get_contents($nginxUrl . "/api/products/get-with-details.php?deleted=0&id=" . $id);
                $product = json_decode($productJson, true);
            ?>
            <div class="form-add-product__elements form-elements-container">

                <div  class="confirm-container">
                    <?php if($product[0]['is_confirm'] == '1') { ?>
                        <svg fill="#009933" width="20px" height="20px" viewBox="0 0 32.00 32.00" enable-background="new 0 0 32 32" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" stroke="#009933" transform="rotate(0)matrix(1, 0, 0, 1, 0, 0)"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#66FF99" stroke-width="0.256"></g><g id="SVGRepo_iconCarrier"> <g id="Approved"></g> <g id="Approved_1_"> <g> <path d="M30,1H2C1.448,1,1,1.448,1,2v28c0,0.552,0.448,1,1,1h28c0.552,0,1-0.448,1-1V2C31,1.448,30.552,1,30,1z M29,29H3V3h26V29z "></path> <path d="M12.629,21.73c0.192,0.18,0.438,0.27,0.683,0.27s0.491-0.09,0.683-0.27l10.688-10c0.403-0.377,0.424-1.01,0.047-1.413 c-0.377-0.404-1.01-0.425-1.413-0.047l-10.004,9.36l-4.629-4.332c-0.402-0.377-1.035-0.356-1.413,0.047 c-0.377,0.403-0.356,1.036,0.047,1.413L12.629,21.73z"></path> </g> </g> <g id="File_Approve"></g> <g id="Folder_Approved"></g> <g id="Security_Approved"></g> <g id="Certificate_Approved"></g> <g id="User_Approved"></g> <g id="ID_Card_Approved"></g> <g id="Android_Approved"></g> <g id="Privacy_Approved"></g> <g id="Approved_2_"></g> <g id="Message_Approved"></g> <g id="Upload_Approved"></g> <g id="Download_Approved"></g> <g id="Email_Approved"></g> <g id="Data_Approved"></g> </g></svg>
                        <span class="price-confirm">Товар утверждён Администратором</span>
                    <?php } else { ?>
                        <svg fill="#d2323d" width="20px" height="20px" viewBox="0 0 128 128" id="Layer_1" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <polygon points="82.4,40 64,58.3 45.6,40 40,45.6 58.3,64 40,82.4 45.6,88 64,69.7 82.4,88 88,82.4 69.7,64 88,45.6 "></polygon> <path d="M1,127h126V1H1V127z M9,9h110v110H9V9z"></path> </g> </g></svg>
                        <span class="price-not-confirm">Товар на рассмотрении Администратором</span>
                    <?php } ?>   
                </div>

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

                <!-- описание -->
                <!-- <div class="form-add-product__elements-item d-none">
                    <p>Описание</p><textarea id="description" name="description"><?= $product[0]['description']; ?></textarea>
                    <div class="error-info d-none"></div> 
                </div> -->

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


            <!-- в зависимости от валюты поставщика -->
            <?php if ($profile['currency_dollar'] == "0") { ?>

                <!-- цена поставщика сум-->
                <div class="form-add-product__elements-item">
                    <p>Цена, Сум </p><input type="number" id="price" name="price" min="0" value="<?= $product[0]['price']; ?>" required placeholder="0"  price-old="<?= $product[0]['price']; ?>" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                    <input type="hidden" id="price_dollar" name="price_dollar" min="0" value="<?= $product[0]['price_dollar']; ?>">
                    <div class="error-info d-none"></div> 
                </div>

                <!-- среднерыночная цена сум-->
                <div class="form-add-product__elements-item">
                    <p>Цена рынок, Сум </p><input type="number" id="max_price" name="max_price" min="0" value="<?= $product[0]['max_price']; ?>" required placeholder="0"  max-price-old="<?= $product[0]['max_price']; ?>" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                    <input type="hidden" id="max_price_dollar" name="max_price_dollar" min="0" value="<?= $product[0]['max_price_dollar']; ?>">
                    <div class="error-info d-none"></div> 
                </div> 
            <?php } else { ?>
 
                <!-- цена поставщика $-->
                <div class="form-add-product__elements-item">
                    <p>Цена, $ </p>
                    <input type="number" id="price_dollar" name="price_dollar" min="0" value="<?php if($product[0]['price_dollar'] !== 0) {echo $product[0]['price_dollar'];} ?>" required placeholder="0" class="price-dollar-add" rate="<?= $product[0]['vendor_rate']; ?>" onchange="calcPriceUzs()"  onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                    <input type="hidden" id="price" name="price" min="0" value="<?= $product[0]['price']; ?>" class="price-value" price-old="<?= $product[0]['price']; ?>">
                    <span>$ = </span><span class="price-uzs"><b><?= $product[0]['price']; ?></b></span><span> Сум</span>
                    <div class="error-info d-none"></div> 
                </div>

                <!-- среднерыночная цена $-->
                <div class="form-add-product__elements-item">
                    <p>Цена рынок, $ </p>
                    <input type="hidden" id="max_price" name="max_price" min="0" value="<?= $product[0]['max_price']; ?>" class="price-value" max-price-old="<?= $product[0]['max_price']; ?>">
                    <input type="number" id="max_price_dollar" name="max_price_dollar" min="0" value="<?php if($product[0]['max_price_dollar'] !== 0) {echo $product[0]['max_price_dollar'];} ?>" required placeholder="0" class="max_price-dollar-add" rate="<?= $product[0]['vendor_rate']; ?>" onchange="calcPriceUzs()"  onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                    <span>$ = </span><span class="price-uzs"><b><?= $product[0]['max_price']; ?></b></span><span> Сум</span>
                    <div class="error-info d-none"></div> 
                </div> 
                 
            <?php }  ?>

            </div>

            <div class="btn-group-3">
                <div>
                    <button class="btn btn-ok" onclick="editProduct(2)">Сохранить</button>
                    <a href="vendor-edit-product.php?id=<?= $id; ?>" class="btn btn-neutral">Сбросить изменения</a> 
                </div>
            
                <div class="btn btn-delete" onclick="deleteProductFromEditForm(<?= $id; ?>)">Удалить товар</div>
            </div>
        </form>
    </section>


<?php include('./../components/footer.php'); ?>