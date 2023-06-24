<?php 
    // собираем массив из подключаемых файлов css и js
    $styleSrc = [
        "<link rel='stylesheet' href='./../assets/css/base.css'>",
        "<link rel='stylesheet' href='./../assets/css/list-products.css'>"
    ];
    $scriptsSrc = [
        "<script src='./../assets/js/main.js'></script>",
        "<script src='./../assets/js/list-products.js'></script>"
    ];
?>

<!-- подключим хэдер -->
<?php include('./../components/header.php'); ?>   

    <p class="page-title">СПИСОК ТОВАРОВ</p>

    <a href="./../pages/vendor-add-product.php" class="btn btn-ok d-iblock">+ Добавить товар</a>

    <!-- соберём данные для отображения в форме -->

    <?php
        $brandsJson = file_get_contents("http://nginx/api/brands.php");
        $brands = json_decode($brandsJson, true);
        // $brands_table = [];
        // foreach($brands as $brand) {
        //     $brands_table += [$brand['id'] => $brand['brand_name']];
        // }

        $categoriesJson = file_get_contents("http://nginx/api/categories.php");
        $categories = json_decode($categoriesJson, true);
        // $categories_table = [];
        // foreach($categories as $category) {
        //     $categories_table += [$category['id'] => $category['category_name']];
        // }
    ?>

    <!-- Выбор фильтров -->
    <section class="form-filters">

        <!-- здесь храним id поставщика -->
        <input type="hidden" id="vendor_id" name="vendor_id" value="111">
        
        <div class="form-elements-container">
            
            <!-- выбор бренда -->
            <label>Бренд
                <select id="brand_id" name="brand_id" value="">
                    <option value="">Все</option>
                    <?php foreach($brands as $brand) { ?>
                        <option value="<?= $brand['id']; ?>"><?= $brand['brand_name']; ?></option>
                    <?php }; ?>
                </select>
            </label>

            <!-- выбор категории -->
            <label>Категория
                <select id="category_id" name="category_id" value="">
                    <!-- <option value="" class="select-default" selected>Выберите категорию...</option> -->
                    <option value="">Все</option>
                    <?php foreach($categories as $category) { ?>
                        <option value="<?= $category['id']; ?>"><?= $category['category_name']; ?></option>
                    <?php }; ?>
                </select>
            </label>

            <!-- выбор кол-во записей на листе -->
            <div class="d-iblock">Показывать по
                <select id="limit" name="limit" value="" required>
                    <!-- <option value="" class="select-default" selected>Выберите категорию...</option> -->
                    <option value="5">5</option>
                    <option value="40">40</option>
                    <option value="100">100</option>
                    <option value="">все</option>
                </select>
            </div>
            
            <br>

            <!-- поле поиска -->
            <input type="search" id="search" name="search" value="" placeholder="Поиск">
            
            <button class="btn btn-ok d-iblock">Применить</button>
            
        </div>
    </section>

    <!-- таблица товаров -->
    <section class="products">
        <table id="list-products">

            <thead>
                <tr role="row">

                    <th data-id="article" data-sort="">Артикул</th>
                    <th data-id="name" data-sort="">Наименование</th>
                    <th data-id="brand_id" data-sort="">Бренд</th>
                    <th data-id="category_id" data-sort="">Категория</th>
                    <th data-id="quantity_available" data-sort="">Остаток</th>
                    <th data-id="price" data-sort="">Цена</th>
                    <th data-id="max_price" data-sort="">Цена рыночная</th>
                    
                </tr>
            </thead>

            <tbody class="list-products__body">

                <!-- <?php
                    $productsJson = file_get_contents("http://nginx/api/products.php?vendor_id=111");
                    $products = json_decode($productsJson, true);

                    for ($i = 0; $i < $limitRows; $i++) {
                ?>

                <tr role="row" class="list-products__row">
                    
                    <td><a href="#"><strong><?= $products[$i]['article']; ?></strong></a></td>
                    <td  class="list-products_name"><a href="#"><img src="<?= $products[$i]['photo']; ?>" /><strong><?= $products[$i]['name']; ?></strong></a></td>
                    <td><?= $brands_table[$products[$i]['brand_id']]; ?></td>
                    <td><?= $categories_table[$products[$i]['category_id']]; ?></td>
                    <td><?= $products[$i]['quantity_available']; ?></td>
                    <td><?= $products[$i]['price']; ?></td>
                    <td><?= $products[$i]['max_price']; ?></td>
                </tr>

                <?php } ?> -->
            </tbody>
        </table>
        <div class="info-table"></div>
    </section>

    <section class="pagination-wrapper"></section>

    <!-- ШАБЛОНЫ -->
    <!-- шаблон таблицы -->
    <template id="template-body-table">
        
        <tr role="row" class="list-products__row">
            <td><a href="#"><strong>${article}</strong></a></td>
            <td class="list-products_name"><a href="#"><img src="${photo}" /><strong>${name}</strong></td>
            <td>${brand_id}</td>
            <td>${category_id}</td>
            <td>${quantity_available}</td>
            <td>${price}</td>
            <td>${max_price}</td>
        </tr>

    </template>

    <!-- шаблон пагинации -->
    <template id="template-pagination">
        <div class="page-switch">                
            <button class="page-switch__prev"  onclick="switchPage(-1)" disabled>
                <svg  class="fill" viewBox="0 8 23 16" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>down</title> <path d="M11.125 16.313l7.688-7.688 3.594 3.719-11.094 11.063-11.313-11.313 3.5-3.531z"></path> </g></svg>
            </button>
            <span class="current-page">${currentPage}</span>
            <button class="page-switch__next" onclick="switchPage(1)" disabled>
                <svg  class="fill" viewBox="0 8 23 16" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>down</title> <path d="M11.125 16.313l7.688-7.688 3.594 3.719-11.094 11.063-11.313-11.313 3.5-3.531z"></path> </g></svg>
            </button>
        </div>
        <div class="page-status">стр <span class="current-page">${currentPage}</span> из <span class="total-page">${totalPages}</span></div>
    </template>
      
<!-- подключим футер -->
<?php include('./../components/footer.php'); ?>

        