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
        "<link rel='stylesheet' href='./../assets/css/list-products.css'>"
    ];
    $scriptsSrc = [
        "<script src='./../assets/js/main.js'></script>",
        "<script src='./../assets/js/admin-list-products.js'></script>"
    ];
?>

<!-- подключим хэдер -->
<?php include('./../components/header.php'); ?>   


<p class="page-title">СПИСОК ТОВАРОВ</p>

<a href="javascript: addProduct()" class="btn btn-ok d-iblock">+ Добавить товар</a>

<!-- если параметры get пустые -->
<!-- отрисовываем страницу по дефолту -->
        <?php 
        if (count($_GET) == 0)  {
        ?>

            <!-- данные для отображения в форме (без гет параметров не нужны, тк при отрисовке таблицы будут перерисованы фильтры) -->
        
            <!-- Выбор фильтров -->
            <section class="form-filters">

            <div class="form-elements-container filters-container-flex">

                <!-- выбор города -->
                <div class="d-iblock">
                    <div>Город</div>
                    <select id="city_id" name="city_id" value="">  
                        <option value="" selected>Все</option>
                    </select>
                </div>
                <!-- выбор поставщика -->
                <div class="d-iblock">
                    <div>Поставщик</div>
                    <select id="vendor_id" name="vendor_id" value="" class="vendor-filter">
                        <option value="" selected>Все</option>
                    </select>
                </div>
                <!-- выбор категории -->
                <div class="d-iblock">
                    <div>Категория</div>
                    <select id="category_id" name="category_id" value="">
                        <option value="" selected>Все</option>
                    </select>
                </div>

                <!-- выбор бренда -->
                <div class="d-iblock">
                    <div>Бренд</div>
                    <select id="brand_id" name="brand_id" value="">
                        <option value="" selected>Все</option>
                    </select>
                </div>

                <!-- выбор кол-во записей на листе -->
                <div class="d-iblock">
                    <div>Показывать по</div>
                    <select id="limit" name="limit" value="" required>
                        
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>

                    </select>
                </div>

                <!-- поле поиска -->
                <div class="d-iblock">
                    <div>Поиск</div>
                    <input type="search" id="search" name="search" value="" placeholder="Поиск">
                </div>
              

                <!-- утверждённые/неутверждённые -->
                <div class="d-iblock">
                    <div>Утверждены</div>
                    <select id="is_confirm" name="is_confirm" value="">
                        
                        <option value="" selected>Все</option>
                        <option value="is_confirm=1">Утверждены</option>
                        <option value="is_confirm=0">Не утверждены</option>
                        
                    </select>
                </div>


                <!-- активные/неактивные -->
                <div class="d-iblock">
                    <div>Активные</div>
                    <select id="is_active" name="is_active" value="">
                        <!-- неактивные - это все товары, которые отключены поставщиком, 
                        у которых поставщик отключен или
                        у которых город отключен -->
                        <option value="off_product=0" selected>Активные</option>
                        <option title="отключен сам товар, поставщик или город" value="off_product=1">Отключены</option>
                        <option value="">Все</option>
 
                    </select>
                </div>

                <button class="btn btn-ok d-iblock">Применить</button>

            </div>

            </section>

            <!-- таблица товаров -->
            <section class="products">
                <table id="list-products">

                    <thead>
                        <tr role="row">

                            <th data-id="city_name" data-sort="">Город</th>
                            <th data-id="vendor_name" data-sort="">Поставщик</th>
                            <th data-id="name_front" data-sort="">Наименование</th>
                            <th data-id="category_name" data-sort="">Категория</th>
                            <th data-id="brand_name" data-sort="">Бренд</th>
                            <th data-id="quantity_available" data-sort="">Остаток</th>
                            <th data-id="price" data-sort="">Цена</th>
                            <th data-id="max_price" data-sort="">Цена рынок</th>
                            <!-- <th data-id="is_active" data-sort="">Активен</th> -->
                            <th data-id="is_confirm" data-sort="">Утверждён</th>
                            
                        </tr>
                    </thead>

                    <tbody class="list-products__body">
                        <!-- контент таблицы из шаблона -->
                    </tbody>

                </table>
                <div class="info-table"></div>
            </section>

            <section class="pagination-wrapper" offset="0"><!-- пагинация --></section>

<?php 
} ?>


<!-- если есть параметры get -->
<!-- Разберём строку get для отрисовки фильтрации -->

<?php 
if (count($_GET) !== 0) {
    if(isset($_GET['search'])) {
        $searchText = $_GET['search'];
        $search = explode(";description_front:", $searchText);
        $searchText = $search[1];
    } else {
        $searchText = "";
    }

    if(isset($_GET['orderby'])) {
        $orderBy = explode(":", $_GET['orderby']);
        $sortBy = $orderBy[0];
        $mark = $orderBy[1];
    } else {
        $sortBy = "";
    }

    if(isset($_GET['offset']) && $_GET['offset'] !== '') {
        $offset = $_GET['offset'];
    } else {
        $offset = 0;
    }

    if(isset($_GET['off_product'])) {
        $offProduct = "&off_product=" . $_GET['off_product'];
    } else {
        $offProduct = "";
    }


?>

    <!--запрашиваем  данные для отображения в форме толкьо в том случае если есть гет-параметр этого элемента -->
    <!--и рисуем его <option value="значение" selected>имя</option> -->

    <!-- Выбор фильтров -->
    <section class="form-filters">

        <div class="form-elements-container filters-container-flex">

            <!-- выбор города -->
            <div class="d-iblock">
                <div>Город</div>
                <select id="city_id" name="city_id" value="">
                    
                    <?php if (isset($_GET['city_id']) && $_GET['city_id'] !== "" ) {    
                        $cityJson = file_get_contents($nginxUrl . "/api/cities.php?deleted=0&id=" . $_GET['city_id']);
                        $city = json_decode($cityJson, true);
                    ?>
                        <option value="<?= $_GET['city_id']; ?>" selected><?= $city['name']; ?></option>
                        <option value="">Все</option>
                    <?php } else { ?> 
                        <option value="" selected>Все</option>
                    <?php } ?> 

                </select>
            </div>

            <!-- выбор поставщика -->
            <div class="d-iblock">
                <div>Поставщик</div>
                <select id="vendor_id" name="vendor_id" value=""  class="vendor-filter">

                    <?php if (isset($_GET['vendor_id'])  && $_GET['vendor_id'] !== "" ) {  
                        $vendorJson = file_get_contents($nginxUrl . "/api/vendors.php?deleted=0&id=" . $_GET['vendor_id']);
                        $vendor = json_decode($vendorJson, true);  
                    ?>
                        <option value="<?= $_GET['vendor_id']; ?>" selected><?= $vendor['name']; ?></option>
                        <option value="">Все</option>
                    <?php } else { ?> 
                        <option value="" selected>Все</option>
                    <?php } ?> 

                </select>
            </div>
            <!-- выбор категории -->
            <div class="d-iblock">
                <div>Категория</div>
                <select id="category_id" name="category_id" value="">

                    <?php if (isset($_GET['category_id']) && $_GET['category_id'] !== "" ) {    
                        $categoryJson = file_get_contents($nginxUrl . "/api/categories.php?deleted=0&id=" . $_GET['category_id']);
                        $category = json_decode($categoryJson, true);  
                    ?>
                        <option value="<?= $_GET['category_id']; ?>" selected><?= $category['category_name']; ?></option>
                        <option value="">Все</option>
                    <?php } else { ?> 
                        <option value="" selected>Все</option>
                    <?php } ?> 

                </select>
            </div>

            <!-- выбор бренда -->
            <div class="d-iblock">
                <div>Бренд</div>
                <select id="brand_id" name="brand_id" value="">

                    <?php if (isset($_GET['brand_id']) && $_GET['brand_id'] !== "" ) {    
                        $brandJson = file_get_contents($nginxUrl . "/api/brands.php?deleted=0&id=" . $_GET['brand_id']);
                        $brand = json_decode($brandJson, true);                          
                    ?>
                        <option value="<?= $_GET['brand_id']; ?>" selected><?= $brand['brand_name']; ?></option>
                        <option value="">Все</option>
                    <?php } else { ?> 
                        <option value="" selected>Все</option>
                    <?php } ?> 

                </select>
            </div>

            <!-- выбор кол-во записей на листе -->
            <div class="d-iblock">
                <div>Показывать по</div>
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

            <!-- поле поиска -->
            <div class="d-iblock">
                <div>Поиск</div>
                <input type="search" id="search" name="search" value="<?= $searchText; ?>" placeholder="Поиск">
            </div>
            
            <!-- утверждённые/неутверждённые -->
            <div class="d-iblock">
                <div>Утверждены</div>
                <select id="is_confirm" name="is_confirm" value="">
                    
                    <option value="" <?php if(!isset($_GET['is_confirm'])) {echo 'selected';} ?>>Все</option>
                    <option value="is_confirm=1" <?php if(isset($_GET['is_confirm']) && $_GET['is_confirm'] == '1') {echo 'selected';} ?>>Утверждены</option>
                    <option value="is_confirm=0" <?php if(isset($_GET['is_confirm']) && $_GET['is_confirm'] == '0') {echo 'selected';} ?>>Не утверждены</option>
                    
                </select>
            </div>
            
            <!-- активные/неактивные -->
            <div class="d-iblock">
                <div>Активные</div>
                <select id="is_active" name="is_active" value="">

                    <option value="off_product=0" <?php if(isset($_GET['off_product']) && $_GET['off_product'] == '0') {echo 'selected';} ?>>Активные</option>
                    <option title="отключен сам товар, поставщик или город" value="off_product=1" <?php if(isset($_GET['off_product']) && $_GET['off_product'] == '1') {echo 'selected';} ?>>Отключены</option>
                    <option value="" <?php if(!isset($_GET['off_product'])) {echo 'selected';} ?>>Все</option>

                </select>
            </div>


            <button class="btn btn-ok d-iblock">Применить</button>
            
        </div>
    </section>

    <!-- таблица товаров -->
    <section class="products">
        <table id="list-products">

            <thead>
                <tr role="row">

                    <th data-id="city_name" data-sort="<?php if ($sortBy == 'city_name')  {echo $mark; } ?>">Город</th>
                    <th data-id="vendor_name" data-sort="<?php if ($sortBy == 'vendor_name')  {echo $mark; } ?>">Поставщик</th>
                    <th data-id="name_front" data-sort="<?php if ($sortBy == 'name_front')  {echo $mark; } ?>">Наименование</th>
                    <th data-id="category_name" data-sort="<?php if ($sortBy == 'category_name')  {echo $mark; } ?>">Категория</th>
                    <th data-id="brand_name" data-sort="<?php if ($sortBy == 'brand_name')  {echo $mark; } ?>">Бренд</th>
                    <th data-id="quantity_available" data-sort="<?php if ($sortBy == 'quantity_available')  {echo $mark; } ?>">Остаток</th>
                    <th data-id="price" data-sort="<?php if ($sortBy == 'price')  {echo $mark; } ?>">Цена</th>
                    <th data-id="max_price" data-sort="<?php if ($sortBy == 'max_price')  {echo $mark; } ?>">Цена рынок</th>
                    <!-- <th data-id="is_active" data-sort="<?php //if ($sortBy == 'is_active')  {echo $mark; } ?>">Активен</th> -->
                    <th data-id="is_confirm" data-sort="<?php if ($sortBy == 'is_confirm')  {echo $mark; } ?>">Утверждён</th>
                    
                </tr>
            </thead>

            <tbody class="list-products__body">
                <!-- контент таблицы из шаблона -->
            </tbody>

        </table>
        <div class="info-table"></div>
    </section>

    <section class="pagination-wrapper" offset="<?= $offset; ?>"><!-- пагинация --></section>

<?php }?>


    <!-- ШАБЛОНЫ -->
    <!-- шаблон строки таблицы Сум -->
    <template id="template-body-table-uzs">
        
        <tr ${is_title} role="row" class="list-products__row" product-id="${id}" is-active="${is_active}" is-confirm="${is_confirm}"  off-in-bot=${offInBot}>
            
            <td>${city_name}</td>
            <td><a href="admin-edit-vendor.php?id=${vendor_id}">${vendor_name}</a></td>
            <td class="list-products_name"><a href="javascript: editProduct(${id})"><img src="${photo}" /><strong>${name}</strong></td>
            <td>${category_id}</td>
            <td>${brand_id}</td>
            <td>${quantity_available} ${unit}</td>

            <td>
                <input type="text" name="price" class="change-price-el change-price-input d-none" placeholder="${price_format}" value="" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                <div class="change-price price-mark" title="Изменить цены"  data-price-num="${price}">${price_format} Сум</div>
            </td>

            <td>
                <div class="change-price-el  d-none">
                    <input type="text" name="max_price" class="change-price-input" placeholder="${max_price_format}" value="" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                    <span class="save-price" title="Сохранить цены" onclick="saveChangePrice(0, 0)"><svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke=""><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M18.1716 1C18.702 1 19.2107 1.21071 19.5858 1.58579L22.4142 4.41421C22.7893 4.78929 23 5.29799 23 5.82843V20C23 21.6569 21.6569 23 20 23H4C2.34315 23 1 21.6569 1 20V4C1 2.34315 2.34315 1 4 1H18.1716ZM4 3C3.44772 3 3 3.44772 3 4V20C3 20.5523 3.44772 21 4 21L5 21L5 15C5 13.3431 6.34315 12 8 12L16 12C17.6569 12 19 13.3431 19 15V21H20C20.5523 21 21 20.5523 21 20V6.82843C21 6.29799 20.7893 5.78929 20.4142 5.41421L18.5858 3.58579C18.2107 3.21071 17.702 3 17.1716 3H17V5C17 6.65685 15.6569 8 14 8H10C8.34315 8 7 6.65685 7 5V3H4ZM17 21V15C17 14.4477 16.5523 14 16 14L8 14C7.44772 14 7 14.4477 7 15L7 21L17 21ZM9 3H15V5C15 5.55228 14.5523 6 14 6H10C9.44772 6 9 5.55228 9 5V3Z" fill="#009900"></path> </g></svg></span>
                    <span class="reset-price" title="Сбросить изменения" onclick="resetChangePrice(0)"><svg width="25px" height="25px" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M4.56189 13.5L4.14285 13.9294L4.5724 14.3486L4.99144 13.9189L4.56189 13.5ZM9.92427 15.9243L15.9243 9.92427L15.0757 9.07574L9.07574 15.0757L9.92427 15.9243ZM9.07574 9.92426L15.0757 15.9243L15.9243 15.0757L9.92426 9.07574L9.07574 9.92426ZM19.9 12.5C19.9 16.5869 16.5869 19.9 12.5 19.9V21.1C17.2496 21.1 21.1 17.2496 21.1 12.5H19.9ZM5.1 12.5C5.1 8.41309 8.41309 5.1 12.5 5.1V3.9C7.75035 3.9 3.9 7.75035 3.9 12.5H5.1ZM12.5 5.1C16.5869 5.1 19.9 8.41309 19.9 12.5H21.1C21.1 7.75035 17.2496 3.9 12.5 3.9V5.1ZM5.15728 13.4258C5.1195 13.1227 5.1 12.8138 5.1 12.5H3.9C3.9 12.8635 3.92259 13.2221 3.9665 13.5742L5.15728 13.4258ZM12.5 19.9C9.9571 19.9 7.71347 18.6179 6.38048 16.6621L5.38888 17.3379C6.93584 19.6076 9.54355 21.1 12.5 21.1V19.9ZM4.99144 13.9189L7.42955 11.4189L6.57045 10.5811L4.13235 13.0811L4.99144 13.9189ZM4.98094 13.0706L2.41905 10.5706L1.58095 11.4294L4.14285 13.9294L4.98094 13.0706Z" fill="#444444"></path> </g></svg></span>
                </div>
                <div class="change-price price-mark" title="Изменить цены" data-price-num="${max_price}">${max_price_format} Сум</div>
            </td>

            <td>
                <!-- <div class="ta-center"><input type="checkbox" class="checkbox-product" onclick="checkboxChangedProductActive(${id})" ${checked}></div>              -->
                <div class="ta-center"><input type="checkbox" class="checkbox-product" onclick="checkboxChangedProductConfirm(${id})" ${checked-confirm}></div>             
                <span title="Удалить товар"><svg class="garbage" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20px" height="20px" viewBox="0 0 32 32" id="icons" version="1.0" xml:space="preserve" fill="#000000"><g id="SVGRepo_iconCarrier"><rect class="garbage-svg" height="22" id="XMLID_14_" width="16" x="8" y="7"/><line class="garbage-svg" id="XMLID_4_" x1="16" x2="16" y1="10" y2="26"/><line class="garbage-svg" id="XMLID_118_" x1="20" x2="20" y1="10" y2="26"/><line class="garbage-svg" id="XMLID_3_" x1="12" x2="12" y1="10" y2="26"/><line class="garbage-svg" id="XMLID_5_" x1="5" x2="27" y1="7" y2="7"/><rect class="garbage-svg" height="4" id="XMLID_6_" width="6" x="13" y="3"/><g id="XMLID_386_"/></g></svg></span>          
            </td>
        </tr>

    </template>

     <!-- шаблон строки таблицы Доллар -->
    <template id="template-body-table-dollar">
        
        <tr ${is_title} role="row" class="list-products__row" product-id="${id}" is-active="${is_active}" is-confirm="${is_confirm}">
            
            <td>${city_name}</td>
            <td><a href="admin-edit-vendor.php?id=${vendor_id}">${vendor_name}</a></td>
            <td class="list-products_name"><a href="javascript: editProduct(${id})"><img src="${photo}" /><strong>${name}</strong></td>
            <td>${category_id}</td>
            <td>${brand_id}</td>
            <td>${quantity_available} ${unit}</td>

            <td>    
                <input type="text" name="price_dollar" class="change-price-el change-price-input d-none" onchange="calcPriceUzs(${rate})" placeholder="${price_dollar_format} $" value="" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                <div class="price-uzs change-price-el d-none" data-price-dollar="${price_dollar}" style="font-size: 0.65rem" data-price-num="${price}">(${price_format} Сум)</div>
                    
                <div class="change-price" title="Изменить цены">${price_dollar_format} $ </div>
                <div title="Изменить цены" class="change-price price-mark" data-price-num="${price}" style="font-size: 0.65rem">(${price_format} Сум)</div>
            </td>

            <td>
                <div class="change-price-el  d-none">
                    <input type="text" name="max_price_dollar" class="change-price-input" onchange="calcPriceUzs(${rate})" placeholder="${max_price_dollar_format} $" value="" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                    <span class="save-price" title="Сохранить цены" onclick="saveChangePrice(1, ${rate})"><svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke=""><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M18.1716 1C18.702 1 19.2107 1.21071 19.5858 1.58579L22.4142 4.41421C22.7893 4.78929 23 5.29799 23 5.82843V20C23 21.6569 21.6569 23 20 23H4C2.34315 23 1 21.6569 1 20V4C1 2.34315 2.34315 1 4 1H18.1716ZM4 3C3.44772 3 3 3.44772 3 4V20C3 20.5523 3.44772 21 4 21L5 21L5 15C5 13.3431 6.34315 12 8 12L16 12C17.6569 12 19 13.3431 19 15V21H20C20.5523 21 21 20.5523 21 20V6.82843C21 6.29799 20.7893 5.78929 20.4142 5.41421L18.5858 3.58579C18.2107 3.21071 17.702 3 17.1716 3H17V5C17 6.65685 15.6569 8 14 8H10C8.34315 8 7 6.65685 7 5V3H4ZM17 21V15C17 14.4477 16.5523 14 16 14L8 14C7.44772 14 7 14.4477 7 15L7 21L17 21ZM9 3H15V5C15 5.55228 14.5523 6 14 6H10C9.44772 6 9 5.55228 9 5V3Z" fill="#009900"></path> </g></svg></span>
                    <span class="reset-price" title="Сбросить изменения" onclick="resetChangePrice(1)"><svg width="25px" height="25px" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M4.56189 13.5L4.14285 13.9294L4.5724 14.3486L4.99144 13.9189L4.56189 13.5ZM9.92427 15.9243L15.9243 9.92427L15.0757 9.07574L9.07574 15.0757L9.92427 15.9243ZM9.07574 9.92426L15.0757 15.9243L15.9243 15.0757L9.92426 9.07574L9.07574 9.92426ZM19.9 12.5C19.9 16.5869 16.5869 19.9 12.5 19.9V21.1C17.2496 21.1 21.1 17.2496 21.1 12.5H19.9ZM5.1 12.5C5.1 8.41309 8.41309 5.1 12.5 5.1V3.9C7.75035 3.9 3.9 7.75035 3.9 12.5H5.1ZM12.5 5.1C16.5869 5.1 19.9 8.41309 19.9 12.5H21.1C21.1 7.75035 17.2496 3.9 12.5 3.9V5.1ZM5.15728 13.4258C5.1195 13.1227 5.1 12.8138 5.1 12.5H3.9C3.9 12.8635 3.92259 13.2221 3.9665 13.5742L5.15728 13.4258ZM12.5 19.9C9.9571 19.9 7.71347 18.6179 6.38048 16.6621L5.38888 17.3379C6.93584 19.6076 9.54355 21.1 12.5 21.1V19.9ZM4.99144 13.9189L7.42955 11.4189L6.57045 10.5811L4.13235 13.0811L4.99144 13.9189ZM4.98094 13.0706L2.41905 10.5706L1.58095 11.4294L4.14285 13.9294L4.98094 13.0706Z" fill="#444444"></path> </g></svg></span>
                </div>
                <div class="price-uzs change-price-el d-none" data-price-dollar="${max_price_dollar}" style="font-size: 0.65rem" data-price-num="${max_price}">(${max_price_format} Сум)</div>
                <div class="change-price" title="Изменить цены">${max_price_dollar_format} $</div>
                <div title="Изменить цены" class="change-price price-mark" data-price-num="${max_price}" style="font-size: 0.65rem">(${max_price_format} Сум)</div>
            </td>

            <td>
                
                <!-- <div class="ta-center"><input type="checkbox" class="checkbox-product" onclick="checkboxChangedProductActive(${id})" ${checked}></div>     -->
                <div class="ta-center"><input type="checkbox" class="checkbox-product" onclick="checkboxChangedProductConfirm(${id})" ${checked-confirm}></div>                 
                <span title="Удалить товар"><svg class="garbage" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20px" height="20px" viewBox="0 0 32 32" id="icons" version="1.0" xml:space="preserve" fill="#000000"><g id="SVGRepo_iconCarrier"><rect class="garbage-svg" height="22" id="XMLID_14_" width="16" x="8" y="7"/><line class="garbage-svg" id="XMLID_4_" x1="16" x2="16" y1="10" y2="26"/><line class="garbage-svg" id="XMLID_118_" x1="20" x2="20" y1="10" y2="26"/><line class="garbage-svg" id="XMLID_3_" x1="12" x2="12" y1="10" y2="26"/><line class="garbage-svg" id="XMLID_5_" x1="5" x2="27" y1="7" y2="7"/><rect class="garbage-svg" height="4" id="XMLID_6_" width="6" x="13" y="3"/><g id="XMLID_386_"/></g></svg></span>          
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
 
 