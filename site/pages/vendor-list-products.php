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
        <input type="hidden" id="vendor_id" name="vendor_id" value="<?= $vendor_id; ?>">
        
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
                    <option value="100">100</option>
                    <option value="40">40</option>
                    <option value="3">3</option>
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
                <!-- контент таблицы из шаблона -->
            </tbody>

        </table>
        <div class="info-table"></div>
    </section>

    <section class="pagination-wrapper"><!-- пагинация --></section>

    <!-- ШАБЛОНЫ -->
    <!-- шаблон таблицы -->
    <template id="template-body-table">
        
        <tr role="row" class="list-products__row" product-id="${id}">
            <td><a href="vendor-edit-product.php?id=${id}"><strong>${article}</strong></a></td>
            <td class="list-products_name"><a href="vendor-edit-product.php?id=${id}"><img src="${photo}" /><strong>${name}</strong></td>
            <td>${brand_id}</td>
            <td>${category_id}</td>
            <td>${quantity_available}</td>
            <td>${price}</td>
            <td>${max_price}

                <svg class="garbage" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20px" height="20px" viewBox="0 0 32 32" id="icons" version="1.0" xml:space="preserve" fill="#000000"><g id="SVGRepo_iconCarrier"><rect class="garbage-svg" height="22" id="XMLID_14_" width="16" x="8" y="7"/><line class="garbage-svg" id="XMLID_4_" x1="16" x2="16" y1="10" y2="26"/><line class="garbage-svg" id="XMLID_118_" x1="20" x2="20" y1="10" y2="26"/><line class="garbage-svg" id="XMLID_3_" x1="12" x2="12" y1="10" y2="26"/><line class="garbage-svg" id="XMLID_5_" x1="5" x2="27" y1="7" y2="7"/><rect class="garbage-svg" height="4" id="XMLID_6_" width="6" x="13" y="3"/><g id="XMLID_386_"/></g></svg>
 
            </td>
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

        