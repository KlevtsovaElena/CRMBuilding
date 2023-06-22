<?php include('./../components/header.php'); ?>
    <!-- <div>ПРИМЕР ЭЛЕМЕНТОВ
        <input type="text" placeholder="test">
        <textarea name="" id="" cols="30" rows="10" placeholder="test"></textarea>
        <button class="btn btn-ok">Сохранить товар</button>
        <button class="btn btn-neutral">Сбросить</button>
        <button class="btn btn-delete">Удалить товар</button>
    </div> -->

                
    <p class="page-title">Добавить товар</p>

    <!-- соберём данные для отображения в форме -->

    <?php
        $brandsJson = file_get_contents("http://nginx/api/brands.php");
        $brands = json_decode($brandsJson, true);
        $categoriesJson = file_get_contents("http://nginx/api/categories.php");
        $categories = json_decode($categoriesJson, true);
    ?>
                        
    <!-- Форма добавления товаров -->
    <form id="form-add-product" action="#" method="post" enctype="multipart/form-data">

        <input type="hidden" id="vendorId" name="vendorId" value="111">
        
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
                <div><progress id="progress" max="100" value="0"></progress></div>
                          
            </div>
            <div class="error-info d-none"></div> 
        </div>

            <!-- список -->
            <div class="form-add-product__elements__item">
                <p>Бренд</p>
                <select id="brandId" name="brandId" value="" required>
                    <option value="" selected hidden>Выберите бренд...</option>

                    <?php foreach($brands as $brand) { ?>
                        <option value="<?= $brand['id']; ?>"><?= $brand['brandName']; ?></option>
                    <?php }; ?>

                </select>
                <div class="error-info d-none"></div> 
            </div>

            <!-- список -->
            <div class="form-add-product__elements__item">
                <p>Категория</p>
                <select id="categoryId" name="categoryId" value="" required>
                    <option value="" selected hidden>Выберите категорию...</option>

                    <?php foreach($categories as $category) { ?>
                        <option value="<?= $category['id']; ?>"><?= $category['categoryName']; ?></option>
                    <?php }; ?>

                </select>
                <div class="error-info d-none"></div> 
            </div>

            <div class="form-add-product__elements__item">
                <p>Описание</p><textarea id="description" name="description" required></textarea>
                <div class="error-info d-none"></div> 
            </div>

            <div class="form-add-product__elements__item">
                <p>Артикул</p><input type="text" id="article" name="article" value="" required>
                <div class="error-info d-none"></div>
            </div> 

            <div class="form-add-product__elements__item">
                <p>Количество</p><input type="number" id="quantityAvailable" name="quantityAvailable" min="0" value="" required>
                <div class="error-info d-none"></div> 
            </div>

            <div class="form-add-product__elements__item">
                <p>Цена (руб.)</p><input type="number" id="price" name="price" min="0" value="" required>
                <div class="error-info d-none"></div> 
            </div>

            <div class="form-add-product__elements__item">
                <p>Цена рыночная (руб.)</p><input type="number" id="maxPrice" name="maxPrice" min="0" value="" required>
                <div class="error-info d-none"></div> 
            </div>
            
        </div>
        <div>
            <button class="btn btn-ok" onclick="addProduct()">Сохранить</button>
            <a href="#" class="btn btn-neutral">Сбросить изменения</a> 
        </div>
    </form>

<?php include('./../components/footer.php'); ?>