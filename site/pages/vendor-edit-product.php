<?php include('./../components/header.php'); ?>
                
    <p class="page-title">Редактировать товар</p>

    <!-- соберём данные для отображения в форме -->

    <?php
        $brandsJson = file_get_contents("http://nginx/api/brands.php");
        $brands = json_decode($brandsJson, true);
        $categoriesJson = file_get_contents("http://nginx/api/categories.php");
        $categories = json_decode($categoriesJson, true);
    ?>
                        
    <!-- Форма добавления товаров -->
    <form id="form-add-product" action="#" method="post" enctype="multipart/form-data">

        <input type="hidden" id="vendor_id" name="vendor_id" value="111">
        
        <div class="form-add-product__elements form-elements-container">
            <div class="form-add-product__elements__item">
                <p>Наименование</p><input type="text" id="name" name="name" value="" required>
                <div class="error-info d-none"></div>
            </div>

        <!-- фото -->
        <div class="form-add-product__elements__item">
            <p>Изображениe для карточки</p> 
            <p>(Рекомендованные параметры такие НА такие)</p>
            <p>(Допустимые форматы: .jpg, .jpeg, .png)</p>
            <div class="img-title-form">
                
                <div class="img-title-prew img-guide"><img></div>
                <input type="file"  id="photo" name="photo" accept="image/png, image/jpg, image/jpeg" required onchange="loadFile()">  
                <div><progress id="progress" max="100" value="0" class="d-none"></progress></div>
                          
            </div>
            <div class="error-info d-none"></div> 
        </div>

            <!-- список -->
            <div class="form-add-product__elements__item">
                <p>Бренд</p>
                <select id="brand_id" name="brand_id" value="" required>
                    <option value="" selected hidden>Выберите бренд...</option>

                    <?php foreach($brands as $brand) { ?>
                        <option value="<?= $brand['id']; ?>"><?= $brand['brand_name']; ?></option>
                    <?php }; ?>

                </select>
                <div class="error-info d-none"></div> 
            </div>

            <!-- список -->
            <div class="form-add-product__elements__item">
                <p>Категория</p>
                <select id="category_id" name="category_id" value="" required>
                    <option value="" selected hidden>Выберите категорию...</option>

                    <?php foreach($categories as $category) { ?>
                        <option value="<?= $category['id']; ?>"><?= $category['category_name']; ?></option>
                    <?php }; ?>

                </select>
                <div class="error-info d-none"></div> 
            </div>

            <div class="form-add-product__elements__item">
                <p>Описание</p><textarea id="description" name="description" required></textarea>
                <div class="error-info d-none"></div> 
            </div>

            <div class="form-add-product__elements__item">
                <p>Артикул (число)</p><input type="number" id="article" name="article" min="0" value="" required placeholder="0">
                <div class="error-info d-none"></div>
            </div> 

            <div class="form-add-product__elements__item">
                <p>Количество</p><input type="number" id="quantity_available" name="quantity_available" min="0" value="" required placeholder="0">
                <div class="error-info d-none"></div> 
            </div>

            <div class="form-add-product__elements__item">
                <p>Цена (руб.)</p><input type="number" id="price" name="price" min="0" value="" required placeholder="0">
                <div class="error-info d-none"></div> 
            </div>

            <div class="form-add-product__elements__item">
                <p>Цена рыночная (руб.)</p><input type="number" id="max_price" name="max_price" min="0" value="" required placeholder="0">
                <div class="error-info d-none"></div> 
            </div>
            
        </div>
        <div>
            <button class="btn btn-ok" onclick="addProduct()">Сохранить</button>
            <a href="vendor-add-product.php" class="btn btn-neutral">Сбросить изменения</a> 
        </div>
    </form>


    </div>
    </section>    
      
        
<script src="./../assets/js/main.js"></script>
<?php include('./../components/footer.php'); ?>