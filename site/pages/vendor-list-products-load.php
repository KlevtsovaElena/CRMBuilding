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
<?php 

    function changeData($countPages, $limit, $filters, $vendor_id) {
        // определим какой offset нам взять 

        // 1. сделаем текущей страницей последнюю 
        $currentPage = $countPages;

        // 3. определим новый offset
        $offset = ($currentPage - 1) * $limit;

        $limitParams = "&limit=" . $limit;
        $offsetParams = "&offset=" . $offset;
        // 4. перепишем параметры
        $params = $filters . $limitParams . $offsetParams;

        // 5. делаем снова запрос на получение другого куска данных
        echo "<script>document.location.href = 'http://localhost/pages/vendor-list-products.php?vendor_id=" . $vendor_id . $params ."'; </script>";

    }
?>

<!-- подключим хэдер -->
<?php include('./../components/header.php'); ?>   


<p class="page-title">СПИСОК ТОВАРОВ</p>

<a href="./../pages/vendor-add-product.php" class="btn btn-ok d-iblock">+ Добавить товар</a>

<!-- соберём данные для отображения в форме -->

<?php
    $brandsJson = file_get_contents("http://nginx/api/brands.php");
    $brands = json_decode($brandsJson, true);
    $brands_table = [];
    foreach($brands as $brand) {
        $brands_table += [$brand['id'] => $brand['brand_name']];
    }

    $categoriesJson = file_get_contents("http://nginx/api/categories.php");
    $categories = json_decode($categoriesJson, true);
    $categories_table = [];
    foreach($categories as $category) {
        $categories_table += [$category['id'] => $category['category_name']];
    }

    $unitsJson = file_get_contents("http://nginx/api/units.php");
    $units = json_decode($unitsJson, true);
    $units_table = [];
    foreach($units as $unit) {
        $units_table += [$unit['id'] => $unit['name_short']];
    }

    // дефолтные значения
    $limit = 10;
    $offset = 0;
    $currentPage = 1;
    $tableInfo = "";
    $url ='http://nginx/api/products/products-with-count.php?vendor_id=' . $vendor_id;
    $params = "";
    $limitParams = "";
    $filters = "";
    $offsetParams = "";

?>

<!-- если параметры get пустые -->
<!-- отрисовываем страницу по дефолту -->
        <?php 
        if (count($_GET) == 0)  {

            $productsJson = file_get_contents($url . "&offset=0&limit=" . $limit);
            $products = json_decode($productsJson, true);

            // сколько всего записей в базе
            $countProducts = $products['count'];
            $tableInfo = "Всего записей: <span class='total-product'>" . $countProducts . "</span>";

            // рассчёт кол-ва страниц
            if ($countProducts == 0) {
                $countPages = 1;
            } else {
                $countPages = ceil($countProducts/$limit);
            }


        ?>

            <!-- Выбор фильтров -->
            <section class="form-filters">

                <!-- здесь храним id поставщика -->
                <input type="hidden" id="vendor_id" name="vendor_id" value="<?= $vendor_id; ?>">
                
                <div class="form-elements-container">

                    <!-- выбор категории -->
                    <label>Категория
                        <select id="category_id" name="category_id" value="">
                            
                            <option value="">Все</option>
                            <?php foreach($categories as $category) { ?>
                                <option value="<?= $category['id']; ?>"><?= $category['category_name']; ?></option>
                            <?php }; ?>

                        </select>
                    </label>

                    <!-- выбор бренда -->
                    <label>Бренд
                        <select id="brand_id" name="brand_id" value="">

                            <option value="">Все</option>
                            <?php foreach($brands as $brand) { ?>
                                <option value="<?= $brand['id']; ?>"><?= $brand['brand_name']; ?></option>
                            <?php }; ?>

                        </select>
                    </label>

                    <!-- выбор кол-во записей на листе -->
                    <div class="d-iblock">Показывать по
                        <select id="limit" name="limit" value="" required>
                            
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>

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

                            <th data-id="name" data-sort="">Наименование</th>
                            <th data-id="category_id" data-sort="">Категория</th>
                            <th data-id="brand_id" data-sort="">Бренд</th>
                            <th data-id="quantity_available" data-sort="">Остаток</th>
                            <th data-id="price" data-sort="">Цена</th>
                            <th data-id="max_price" data-sort="">Цена среднерыночная</th>
                            
                        </tr>
                    </thead>

<?php 
} ?>


<!-- если есть параметры get -->
<!-- Разберём строку get для отрисовки фильтрации -->

<?php 
if (count($_GET) !== 0) {

    if(isset($_GET['search'])) {
        $searchText = $_GET['search'];
        $search = explode(";description:", $searchText);
        $searchText = $search[1];
        $filters .= "&search=" . $_GET['search'];
    } else {
        $searchText = "";
    }

    if(isset($_GET['orderby'])) {
        $orderBy = explode(":", $_GET['orderby']);
        $sortBy = $orderBy[0];
        $mark = $orderBy[1];
        $filters .= "&orderby=" . $_GET['orderby'];

    } else {
        $sortBy = "";
    }

    if(isset($_GET['brand_id']) && $_GET['brand_id'] !== '') {
        $brand_id = $_GET['brand_id'];
        $filters .= "&brand_id=" . $_GET['brand_id'];
    } 

    if(isset($_GET['category_id']) && $_GET['category_id'] !== '') {
        $category_id = $_GET['category_id'];
        $filters .= "&category_id=" . $_GET['category_id'];
    } 

    if(isset($_GET['offset']) && $_GET['offset'] !== '') {
        $offset = $_GET['offset'];
        $offsetParams = "&offset=" . $_GET['offset'];
    } 

    if(isset($_GET['limit']) && $_GET['limit'] !== '') {
        $limit = $_GET['limit'];
        $limitParams = "&limit=" . $_GET['limit'];
    } 

    // соберём параметры запроса
    $params = $filters . $limitParams . $offsetParams;

    // делаем запрос с данными параметрами
    $productsJson = file_get_contents($url . $params);
    $products = json_decode($productsJson, true);

    // сколько всего в базе записей с такими параметрами
    $countProducts = $products['count'];

    // рассчитаем текущую страницу
    $currentPage = ceil($offset/$limit) + 1;

    // рассчитаем сколько всего страниц
    if ($countProducts == 0) {
        $countPages = 1;
    } else {
        $countPages = ceil($countProducts/$limit);
    }

    // если в базе записи есть, а по указанному офсету выборка = 0
    if ($countProducts > 0 && count($products['products']) === 0) {
        changeData($countPages, $limit, $filters, $vendor_id);
    }

    $tableInfo = "Всего записей: <span class='total-product'>" . $countProducts . "</span>";

?>

    <!-- Выбор фильтров -->
    <section class="form-filters">

        <!-- здесь храним id поставщика -->
        <input type="hidden" id="vendor_id" name="vendor_id" value="<?= $vendor_id; ?>">
        
        <div class="form-elements-container">
            
            <!-- выбор категории -->
            <label>Категория
                <select id="category_id" name="category_id" value="">
                    
                    <option value="">Все</option>
                    <?php foreach($categories as $category) {
                        if (!isset($_GET['category_id'])) {
                        ?>
                            <option value="<?= $category['id']; ?>"><?= $category['category_name']; ?></option>
                        <?php
                        } else if ($_GET['category_id'] == $category['id']) {
                        ?>
                            <option value="<?= $category['id']; ?>" selected><?= $category['category_name']; ?></option>

                        <?php
                        } else {
                        ?>
                            <option value="<?= $category['id']; ?>"><?= $category['category_name']; ?></option>;
                        <?php 
                        }
                    }; ?>

                </select>
            </label>

            <!-- выбор бренда -->
            <label>Бренд
                <select id="brand_id" name="brand_id" value="">

                    <option value="">Все</option>
                    <?php foreach($brands as $brand) {
                        if (!isset($_GET['brand_id'])) {
                        ?>
                            <option value="<?= $brand['id']; ?>"><?= $brand['brand_name']; ?></option>
                        <?php
                        } else if ($_GET['brand_id'] == $brand['id']) {
                        ?>
                            <option value="<?= $brand['id']; ?>" selected><?= $brand['brand_name']; ?></option>

                        <?php
                        } else {
                        ?>
                            <option value="<?= $brand['id']; ?>"><?= $brand['brand_name']; ?></option>;
                        <?php 
                        }
                    }; ?>

                </select>
            </label>

            <!-- выбор кол-во записей на листе -->
            <div class="d-iblock">Показывать по
                <select id="limit" name="limit" value="" required>
                        <?php
                        if (!isset($_GET['limit'])) {
                        ?>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        <?php

                        } else {
                            ?>
                            <option value="10" <?php if ($_GET['limit'] == 10) {echo 'selected';} ?> >10</option>
                            <option value="20" <?php if ($_GET['limit'] == 20) {echo 'selected';} ?> >20</option>
                            <option value="50" <?php if ($_GET['limit'] == 50) {echo 'selected';} ?> >50</option>
                            <option value="100" <?php if ($_GET['limit'] == 100) {echo 'selected';} ?> >100</option>
                        <?php }
                        ?> 
                </select>
            </div>
            
            <br>

            <!-- поле поиска -->
            <input type="search" id="search" name="search" value="<?= $searchText; ?>" placeholder="Поиск">
            
            <button class="btn btn-ok d-iblock">Применить</button>
            
        </div>
    </section>

    <!-- таблица товаров -->
    <section class="products">
        <table id="list-products">

            <thead>
                <tr role="row">

                    <th data-id="name" data-sort="<?php if ($sortBy == 'name')  {echo $mark; } ?>">Наименование</th>
                    <th data-id="category_id" data-sort="<?php if ($sortBy == 'category_id')  {echo $mark; } ?>">Категория</th>
                    <th data-id="brand_id" data-sort="<?php if ($sortBy == 'brand_id')  {echo $mark; } ?>">Бренд</th>
                    <th data-id="quantity_available" data-sort="<?php if ($sortBy == 'quantity_available')  {echo $mark; } ?>">Остаток</th>
                    <th data-id="price" data-sort="<?php if ($sortBy == 'price')  {echo $mark; } ?>">Цена</th>
                    <th data-id="max_price" data-sort="<?php if ($sortBy == 'max_price')  {echo $mark; } ?>">Цена среднерыночная</th>
                    
                </tr>
            </thead>

<?php }?>


    <tbody class="list-products__body">
        <?php foreach ($products['products'] as $product) { ?>

        
        <tr role="row" class="list-products__row" product-id="<?= $product['id']; ?>">

            <td class="list-products_name"><a href="javascript: editProduct(<?= $product['id']; ?>)"><img src="<?= $product['photo']; ?>" /><strong><?= $product['name']; ?></strong></td>
            <td><?= $categories_table[$product['category_id']]; ?></td>
            <td><?= $brands_table[$product['brand_id']]; ?></td>
            <td><?= $product['quantity_available'] . ' ' . $units_table[$product['unit_id']] ?></td>
            <td><?= number_format($product['price'], 0, ',', ' '); ?></td>
            <td><?= number_format($product['max_price'], 0, ',', ' '); ?>
            <svg class="garbage" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20px" height="20px" viewBox="0 0 32 32" id="icons" version="1.0" xml:space="preserve" fill="#000000"><g id="SVGRepo_iconCarrier"><rect class="garbage-svg" height="22" id="XMLID_14_" width="16" x="8" y="7"/><line class="garbage-svg" id="XMLID_4_" x1="16" x2="16" y1="10" y2="26"/><line class="garbage-svg" id="XMLID_118_" x1="20" x2="20" y1="10" y2="26"/><line class="garbage-svg" id="XMLID_3_" x1="12" x2="12" y1="10" y2="26"/><line class="garbage-svg" id="XMLID_5_" x1="5" x2="27" y1="7" y2="7"/><rect class="garbage-svg" height="4" id="XMLID_6_" width="6" x="13" y="3"/><g id="XMLID_386_"/></g></svg>
            </td>
        </tr>

        <?php } ?>
    </tbody>

    </table>
    <div class="info-table"><?= $tableInfo; ?></div>
</section>

<section class="pagination-wrapper" offset="<?= $offset; ?>">
    <div class="page-switch">                
        <button class="page-switch__prev"  onclick="switchPage(-1)" disabled>
            <svg  class="fill" viewBox="0 8 23 16" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>down</title> <path d="M11.125 16.313l7.688-7.688 3.594 3.719-11.094 11.063-11.313-11.313 3.5-3.531z"></path> </g></svg>
        </button>
        <span class="current-page"><?= $currentPage; ?></span>
        <button class="page-switch__next" onclick="switchPage(1)" disabled>
            <svg  class="fill" viewBox="0 8 23 16" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>down</title> <path d="M11.125 16.313l7.688-7.688 3.594 3.719-11.094 11.063-11.313-11.313 3.5-3.531z"></path> </g></svg>
        </button>
    </div>
    <div class="page-status">стр <span class="current-page"><?= $currentPage; ?></span> из <span class="total-page"><?= $countPages; ?></span></div>
</section>
      
<!-- подключим футер -->
<?php include('./../components/footer.php'); ?>

        