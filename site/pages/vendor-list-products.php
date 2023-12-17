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

<a href="javascript: addProduct()" class="btn btn-ok d-iblock">+ Добавить товар</a>

<!-- соберём данные для отображения в форме -->

    <!-- инфа о подтверждении цен -->
    <div class="confirm-price-daily">
        <div  class="price-confirm-container" confirm-price="<?= $profile['price_confirmed']; ?>">
        </div>
        <a href="javascript: confirmPriceDaily()" class="btn btn-green d-iblock">Подтвердить цены</a>
        <div class="confirm-price-daily__info" onclick="showConfirmPriceDailyInfo()">
            ?
            <div class="confirm-price-daily__info-text">Необходимо подтверждать цены <b>ежедневно с 9:00 до 10:00</b></div>
        </div>
    </div>
    
        
<!-- если параметры get пустые -->
<!-- отрисовываем страницу по дефолту -->
        <?php 
        if (count($_GET) == 0)  {
        ?>

            <!-- Выбор фильтров -->
            <section class="form-filters">

                <!-- здесь храним id поставщика -->
                <input type="hidden" id="vendor_id" name="vendor_id" value="<?= $vendor_id; ?>">
                
                <div class="form-elements-container filters-container-flex">

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

                    <!-- показывать неактивные -->
                    <div class="active-check">
                        <div >
                            <input type="checkbox" id="active-check" name="active-check" checked value="">
                        </div>
                        <lable>Неактивные</lable>
                    </div>

                    <button id="btn-apply-filters" class="btn btn-ok d-iblock">Применить</button>

                    <?php if($profile['currency_dollar'] == '0') { ?>
                       <button id="btn-masschange-price" class="btn btn-ok d-iblock">Массовое изменение цен</button>
                    <?php } ?>
                    
                    
                </div>

                
            </section>

            <!-- таблица товаров -->
            <section class="products">
                <table id="list-products">

                    <thead>
                        <tr role="row">

                            <th data-id="name_front" data-sort="">Наименование</th>
                            <th data-id="category_name" data-sort="">Категория</th>
                            <th data-id="brand_name" data-sort="">Бренд</th>
                            <th data-id="quantity_available" data-sort="">Остаток</th>
                            <th data-id="price" data-sort="">Цена, <?php if ($profile['currency_dollar'] == '0') {echo 'Сум';} else {echo '$';} ?></th>
                            <th data-id="max_price" data-sort="">Цена рынок, <?php if ($profile['currency_dollar'] == '0') {echo 'Сум';} else {echo '$';} ?></th>
                            <th data-id="is_active" data-sort="">Активен</th>
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

?>

    <!-- Выбор фильтров -->
    <section class="form-filters">

        <!-- здесь храним id поставщика -->
        <input type="hidden" id="vendor_id" name="vendor_id" value="<?= $vendor_id; ?>">
        
        <div class="form-elements-container filters-container-flex">
            
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
            
            <!-- показывать неактивные -->
            <div class="active-check">
                <div >
                    <input type="checkbox" id="active-check" name="active-check" 
                        <?php 
                            if(isset($_GET['is_active']) && $_GET['is_active']=='1') {
                                echo 'value="is_active=1"';
                            } else {
                                echo 'checked value=""';
                            }?>
                    >
                </div>
                <lable>Неактивные</lable>
            </div>
                        
            <button id="btn-apply-filters" class="btn btn-ok d-iblock">Применить</button>
            <?php if($profile['currency_dollar'] == '0') { ?>
                       <button id="btn-masschange-price" class="btn btn-ok d-iblock">Массовое изменение цен</button>
            <?php } ?>
            
        </div>
    </section>

    <!-- таблица товаров -->
    <section class="products">
        <table id="list-products">

            <thead>
                <tr role="row">

                    <th data-id="name_front" data-sort="<?php if ($sortBy == 'name_front')  {echo $mark; } ?>">Наименование</th>
                    <th data-id="category_name" data-sort="<?php if ($sortBy == 'category_name')  {echo $mark; } ?>">Категория</th>
                    <th data-id="brand_name" data-sort="<?php if ($sortBy == 'brand_name')  {echo $mark; } ?>">Бренд</th>
                    <th data-id="quantity_available" data-sort="<?php if ($sortBy == 'quantity_available')  {echo $mark; } ?>">Остаток</th>
                    <th data-id="price" data-sort="<?php if ($sortBy == 'price')  {echo $mark; } ?>">Цена, <?php if ($profile['currency_dollar'] == '0') {echo 'Сум';} else {echo '$';} ?></th>
                    <th data-id="max_price" data-sort="<?php if ($sortBy == 'max_price')  {echo $mark; } ?>">Цена рынок, <?php if ($profile['currency_dollar'] == '0') {echo 'Сум';} else {echo '$';} ?></th>
                    <th data-id="is_active" data-sort="<?php if ($sortBy == 'is_active')  {echo $mark; } ?>">Активен</th>
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
    <!-- шаблон таблицы -->
    <template id="template-body-table">
        
        <tr role="row" class="list-products__row" product-id="${id}" is-active="${is_active}" is-confirm="${is_confirm_product}">
            
            <td class="list-products_name"><a href="javascript: editProduct(${id})"><img src="${photo}" /><strong>${name}</strong></td>
            <td>${category_id}</td>
            <td>${brand_id}</td>
            <td>${quantity_available} ${unit}</td>
            <td>
                <?php if ($profile['currency_dollar'] == '0') { ?>
                    <input type="text" name="price" class="change-price-el change-price-input d-none" placeholder="${price_format}" value="" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                    <div class="change-price price-mark" title="Изменить цены"  data-price-num="${price}">${price_format} Сум</div>
                <?php } else { ?>
                    <input type="text" name="price_dollar" class="change-price-el change-price-input d-none" onchange="calcPriceUzs(${rate})" placeholder="${price_dollar_format} $" value="" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                    <div class="price-uzs change-price-el d-none" data-price-dollar="${price_dollar}" style="font-size: 0.65rem" data-price-num="${price}">(${price_format} Сум)</div>
                    
                    <div class="change-price" title="Изменить цены">${price_dollar_format} $ </div>
                    <div title="Изменить цены" class="change-price price-mark" data-price-num="${price}" style="font-size: 0.65rem">(${price_format} Сум)</div>
                <?php } ?>

            </td>
            <td>
                <div class="change-price-el  d-none">
                    <input type="text" name="<?php if ($profile['currency_dollar'] == '0') {echo 'max_price'; } else {echo 'max_price_dollar'; }?>" class="change-price-input" <?php if ($profile['currency_dollar'] == '1') {echo 'onchange="calcPriceUzs(${rate})"';} ?> placeholder="<?php if ($profile['currency_dollar'] == '0') {echo '${max_price_format}';} else {echo '${max_price_dollar_format} $';} ?>" value="" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                    <span class="save-price" title="Сохранить цены" onclick="saveChangePrice(<?= $profile['currency_dollar']; ?>, <?= $profile['rate']; ?>)"><svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke=""><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M18.1716 1C18.702 1 19.2107 1.21071 19.5858 1.58579L22.4142 4.41421C22.7893 4.78929 23 5.29799 23 5.82843V20C23 21.6569 21.6569 23 20 23H4C2.34315 23 1 21.6569 1 20V4C1 2.34315 2.34315 1 4 1H18.1716ZM4 3C3.44772 3 3 3.44772 3 4V20C3 20.5523 3.44772 21 4 21L5 21L5 15C5 13.3431 6.34315 12 8 12L16 12C17.6569 12 19 13.3431 19 15V21H20C20.5523 21 21 20.5523 21 20V6.82843C21 6.29799 20.7893 5.78929 20.4142 5.41421L18.5858 3.58579C18.2107 3.21071 17.702 3 17.1716 3H17V5C17 6.65685 15.6569 8 14 8H10C8.34315 8 7 6.65685 7 5V3H4ZM17 21V15C17 14.4477 16.5523 14 16 14L8 14C7.44772 14 7 14.4477 7 15L7 21L17 21ZM9 3H15V5C15 5.55228 14.5523 6 14 6H10C9.44772 6 9 5.55228 9 5V3Z" fill="#009900"></path> </g></svg></span>
                    <span class="reset-price" title="Сбросить изменения" onclick="resetChangePrice(<?= $profile['currency_dollar']; ?>)"><svg width="25px" height="25px" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M4.56189 13.5L4.14285 13.9294L4.5724 14.3486L4.99144 13.9189L4.56189 13.5ZM9.92427 15.9243L15.9243 9.92427L15.0757 9.07574L9.07574 15.0757L9.92427 15.9243ZM9.07574 9.92426L15.0757 15.9243L15.9243 15.0757L9.92426 9.07574L9.07574 9.92426ZM19.9 12.5C19.9 16.5869 16.5869 19.9 12.5 19.9V21.1C17.2496 21.1 21.1 17.2496 21.1 12.5H19.9ZM5.1 12.5C5.1 8.41309 8.41309 5.1 12.5 5.1V3.9C7.75035 3.9 3.9 7.75035 3.9 12.5H5.1ZM12.5 5.1C16.5869 5.1 19.9 8.41309 19.9 12.5H21.1C21.1 7.75035 17.2496 3.9 12.5 3.9V5.1ZM5.15728 13.4258C5.1195 13.1227 5.1 12.8138 5.1 12.5H3.9C3.9 12.8635 3.92259 13.2221 3.9665 13.5742L5.15728 13.4258ZM12.5 19.9C9.9571 19.9 7.71347 18.6179 6.38048 16.6621L5.38888 17.3379C6.93584 19.6076 9.54355 21.1 12.5 21.1V19.9ZM4.99144 13.9189L7.42955 11.4189L6.57045 10.5811L4.13235 13.0811L4.99144 13.9189ZM4.98094 13.0706L2.41905 10.5706L1.58095 11.4294L4.14285 13.9294L4.98094 13.0706Z" fill="#444444"></path> </g></svg></span>
                </div>
                <?php if ($profile['currency_dollar'] == '0') { ?>
                    <div class="change-price price-mark" title="Изменить цены" data-price-num="${max_price}">${max_price_format} Сум</div>
                <?php } else { ?>
                    <div class="price-uzs change-price-el d-none" data-price-dollar="${max_price_dollar}" style="font-size: 0.65rem" data-price-num="${max_price}">(${max_price_format} Сум)</div>
                    <div class="change-price " title="Изменить цены">${max_price_dollar_format} $</div>
                    <div title="Изменить цены" class="change-price price-mark" data-price-num="${max_price}" style="font-size: 0.65rem">(${max_price_format} Сум)</div>

                <?php } ?>
                
            </td>
            <td>
                <div class="ta-center"><input type="checkbox" class="checkbox-product" onclick="checkboxChangedProductActive(${id})" ${checked}></div>                
            </td>
            <td>
                <div class="ta-center" class="confirm-product">${is_confirm}</div>                
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

    <!-- шаблон цены подтверждены -->
    <template id="tmpl-price-confirm">
        <svg fill="#009933" width="20px" height="20px" viewBox="0 0 32.00 32.00" enable-background="new 0 0 32 32" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" stroke="#009933" transform="rotate(0)matrix(1, 0, 0, 1, 0, 0)"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#66FF99" stroke-width="0.256"></g><g id="SVGRepo_iconCarrier"> <g id="Approved"></g> <g id="Approved_1_"> <g> <path d="M30,1H2C1.448,1,1,1.448,1,2v28c0,0.552,0.448,1,1,1h28c0.552,0,1-0.448,1-1V2C31,1.448,30.552,1,30,1z M29,29H3V3h26V29z "></path> <path d="M12.629,21.73c0.192,0.18,0.438,0.27,0.683,0.27s0.491-0.09,0.683-0.27l10.688-10c0.403-0.377,0.424-1.01,0.047-1.413 c-0.377-0.404-1.01-0.425-1.413-0.047l-10.004,9.36l-4.629-4.332c-0.402-0.377-1.035-0.356-1.413,0.047 c-0.377,0.403-0.356,1.036,0.047,1.413L12.629,21.73z"></path> </g> </g> <g id="File_Approve"></g> <g id="Folder_Approved"></g> <g id="Security_Approved"></g> <g id="Certificate_Approved"></g> <g id="User_Approved"></g> <g id="ID_Card_Approved"></g> <g id="Android_Approved"></g> <g id="Privacy_Approved"></g> <g id="Approved_2_"></g> <g id="Message_Approved"></g> <g id="Upload_Approved"></g> <g id="Download_Approved"></g> <g id="Email_Approved"></g> <g id="Data_Approved"></g> </g></svg>
        <span class="price-confirm">Вы подтвердили цены</span>
    </template>

    <!-- шаблон цены не подтверждены -->
    <template id="tmpl-price-not-confirm">
        <svg fill="#d2323d" width="20px" height="20px" viewBox="0 0 128 128" id="Layer_1" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <polygon points="82.4,40 64,58.3 45.6,40 40,45.6 58.3,64 40,82.4 45.6,88 64,69.7 82.4,88 88,82.4 69.7,64 88,45.6 "></polygon> <path d="M1,127h126V1H1V127z M9,9h110v110H9V9z"></path> </g> </g></svg>
        <span class="price-not-confirm">Подтвердите цены</span>
    </template>
      
    <!-- masschange-form -->   

    <div class="modalbox d-none" >
        <form action="#" class="modal-form masschange-price" method="post">
            <div class="close-icon-flex"><div class="close-icon" onclick="closeMassChangePriceFormIcon()">x</div></div>

            <p class="masschange-price__title">Массовое изменение цен</p>

            <!-- выбор случая -->
            <div class="masschange-price-case">
                <span class="masschange-price-case__text">Что сделать: </span>
                <select id="case" name="case" value="">

                    <option value="priceUpPercent">Поднять в %</option>
                    <option value="priceDownPercent">Снизить в %</option>
                    <option value="priceUpSoums">Поднять в Сумах</option>
                    <option value="priceDownSoums">Снизить в Сумах</option>

                </select>
            </div>

            <!-- выбор какие цены менять через чекбокс, по умолчанию все галки сняты -->
            <div class="masschange-price-kind">
                <div class="masschange-price-kind__input"><input type="checkbox" id="kind-price" name="kind-price" value=""><lable for="kind-price">цены</lable></div>
                <div class="masschange-price-kind__input"><input type="checkbox" id="kind-price-max" name="kind-price-max" value=""><lable for="kind-price-max">среднерыночные цены</lable></div>
            </div>

            <!-- выбор категорий и брендов, по умолчанию выделены все -->
            <div class="masschange-price-choose-products">
                <p class="masschange-price-choose-products__title">для товаров:</p>
                <!-- категории -->
                <div class="masschange-price-choose-products__check">
                    <select id="category_id_masschange" name="category_id_masschange" value="">

                        <option value="">Всех категорий</option>
                        <?php foreach($categories as $category) { ?>
                            <option value="<?= $category['id']; ?>"><?= $category['category_name']; ?></option>
                        <?php }; ?>

                    </select>
                </div>
                <!-- бренды -->
                <div class="masschange-price-choose-products__check">
                    <select id="brand_id_masschange" name="brand_id_masschange" value="">

                        <option value="">Всех брендов</option>
                        <?php foreach($brands as $brand) { ?>
                            <option value="<?= $brand['id']; ?>"><?= $brand['brand_name']; ?></option>
                        <?php }; ?>

                    </select>
                </div>
            </div>

            <!-- выбор на сколько изменить цены -->
            <div class="masschange-price-value">
                <span>на </span>
                <input type="number" id="change-price-value" name="change-price-value" value="" min="0"  onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                <span class="masschange-price-value__unit"> %</span>
            </div>

            <div class="ta-center"><button class="btn btn-ok" onclick="massChangePriceClick()">Выполнить</button></div>
            <div id="result-masschange-price" class="result-masschange-price"></div>
        </form>
    </div>

    <!-- /masschange-form -->


<!-- подключим футер -->
<?php include('./../components/footer.php'); ?> 