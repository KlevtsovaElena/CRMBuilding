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
        "<link rel='stylesheet' href='./../assets/css/list-orders.css'>"
    ];
    $scriptsSrc = [
        "<script src='./../assets/js/main.js'></script>",
        "<script src='./../assets/js/list-orders.js'></script>"
    ];
?>
<?php include('./../components/header.php'); ?>
                
    <p class="page-title">ЗАКАЗЫ</p>

    <!-- здесь храним id поставщика -->
    <input type="hidden" id="vendor_id" name="vendor_id" value="<?= $vendor_id; ?>">

    <!-- соберём данные для отображения в форме -->

<!-- если параметры get пустые -->
<!-- отрисовываем страницу по дефолту -->
<?php 
        if (count($_GET) == 0)  {
        ?>

            <!-- Выбор фильтров -->
            <section class="form-filters">

            <div class="form-elements-container filters-container-flex">
                    <!-- дата начало -->
                    <div class="d-iblock">
                        с <input type="date" id="date_from" name="date_from" order-date="0" class="middle-input">
                    </div>
                    <!-- дата конец -->
                    <div class="d-iblock">
                        по <input type="date" id="date_till" name="date_till"  order-date="0" class="middle-input">
                    </div>
                    <!-- поле поиска -->
                    <div class="d-iblock">
                        <div>Поиск</div>
                        <input type="search" id="search" name="search" class="small-input" value="" placeholder="№заказа">
                    </div>
                    <!-- выбор статуса -->
                    <div class="d-iblock">
                        <div>Статус</div>
                        <select id="status" name="status" value=""  class="middle-input">

                            <option value="">Все</option>
                            <option value="0">Новый</option>
                            <option value="1">Просмотрен</option>
                            <option value="2">Подтверждён</option>
                            <option value="3">Отменён</option>
                            <option value="5">В доставке</option>
                            <option value="4">Доставлен</option>
                            <option value="archive=1">Только архивные</option>

                        </select>
                    </div>
                    <!-- выбор кол-во записей на листе -->
                    <div class="d-iblock">
                        <div>Показывать по</div>
                        <select id="limit" name="limit" value="">

                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="30">30</option>
                            <option value="50">50</option>

                        </select>
                    </div>
                    <!-- показывать архивные -->
                    <div class="archive-check">
                        <div >
                            <input type="checkbox" id="archive" name="archive" value="archive=0">
                            <!-- <svg class="d-none" width="18px" height="18px" viewBox="5 5 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g id="Interface / Check"> <path id="Vector" d="M6 12L10.2426 16.2426L18.727 7.75732" stroke="#0088cc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g> </g></svg> -->
                        </div>
                        <lable>Архивные</lable>
                    </div>
                    <button class="btn btn-ok d-iblock">Применить</button>

                </div>
            </section>

            <!-- таблица заказов -->
            <section class="orders">
                <table id="list-orders">

                    <thead>
                        <tr role="row">

                            <th class="ta-center" data-id="order_id" data-sort="">№</th>
                            <th class="ta-center" data-id="order_date" data-sort="">Дата</th>
                            <th class="ta-center" data-id="status" data-sort="">Статус</th>
                            <!-- <th class="ta-center" data-id="customer_id" data-sort="">ID</th> -->
                            <th class="ta-center" data-id="customer_phone" data-sort="">Телефон</th>
                            <th class="ta-center" data-id="products">Товары</th>
                            <th class="ta-center" data-id="total_price" data-sort="">Сумма</th>
                            <th class="ta-center" data-id="distance" data-sort="">Расстояние</th>
                            <!-- <th data-id="complete_date">Дата завершения</th> -->

                        </tr>
                    </thead>

                    <tbody class="list-orders__body">
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
        $search = explode(":", $searchText);
        $searchText = $search[1];
    } else {
        $searchText = "";
    }

    if(isset($_GET['status'])) {
        $status = $_GET['status'];
    } else {
        $status = "";
    }

    if(isset($_GET['date_from'])) {
        $date_from = $_GET['date_from'];
    } else {
        $date_from = 0;
    }

    if(isset($_GET['date_till'])) {
        $date_till = $_GET['date_till'];
    } else {
        $date_till = 0;
    }

    if(isset($_GET['orderby'])) {
        $orderByArray = explode(";", $_GET['orderby']);
        $orderBy = explode(":", $orderByArray[0]);
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

    if(isset($_GET['archive']) && $_GET['archive'] !== '') {
        $archive = $_GET['archive'];
    } else {
        $archive = "";
    }

?>

            <!-- Выбор фильтров -->
            <section class="form-filters">

                <div class="form-elements-container filters-container-flex">

                    <!-- дата начало -->
                    <div class="d-iblock">
                        с <input type="date" id="date_from" name="date_from" order-date="<?= $date_from; ?>" class="middle-input">
                    </div>
                    <!-- дата конец -->
                    <div class="d-iblock">
                        по <input type="date" id="date_till" name="date_till" order-date="<?= $date_till; ?>"  class="middle-input">
                    </div>

                    <!-- поле поиска -->
                    <div class="d-iblock">
                        <div>Поиск</div>
                        <input type="search" id="search" name="search" value="<?= $searchText; ?>" placeholder="№заказа" class="small-input">
                    </div>
                    
                    <!-- выбор статуса -->
                    <div class="d-iblock">
                        <div>Статус</div>
                        <select id="status" name="status" value="" class="middle-input">

                            <?php
                            if (!isset($_GET['status']) && !isset($_GET['archive'])) {
                            ?>
                                <option value="">Все</option>
                                <option value="0">Новый</option>
                                <option value="1">Просмотрен</option>
                                <option value="2">Подтверждён</option>
                                <option value="3">Отменён</option>
                                <option value="5">В доставке</option>
                                <option value="4">Доставлен</option>
                                <option value="archive=1">Только архивные</option>

                                
                            <?php

                            } else if (isset($_GET['status'])) {    
                                ?>
                                <option value="">Все</option>
                                <option value="0"  <?php if ($_GET['status'] == 0) {echo 'selected';} ?> >Новый</option>
                                <option value="1"  <?php if ($_GET['status'] == 1) {echo 'selected';} ?> >Просмотрен</option>
                                <option value="2"  <?php if ($_GET['status'] == 2) {echo 'selected';} ?> >Подтверждён</option>
                                <option value="3"  <?php if ($_GET['status'] == 3) {echo 'selected';} ?> >Отменён</option>
                                <option value="5"  <?php if ($_GET['status'] == 5) {echo 'selected';} ?> >В доставке</option>
                                <option value="4"  <?php if ($_GET['status'] == 4) {echo 'selected';} ?> >Доставлен</option>
                                <option value="archive=1">Только архивные</option>


                            <?php 
                            } else if (isset($_GET['archive'])) {
                            ?>
                                <option value="">Все</option>
                                <option value="0">Новый</option>
                                <option value="1">Просмотрен</option>
                                <option value="2">Подтверждён</option>
                                <option value="3">Отменён</option>
                                <option value="5">В доставке</option>
                                <option value="4">Доставлен</option>
                                <option value="archive=1" <?php if ($_GET['archive'] == '1') {echo 'selected';} ?>>Только архивные</option>


                            <?php }
                            ?> 

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
                            <option value="50">30</option>
                            <option value="50">50</option>
                        <?php

                        } else {
                            ?>
                            <option value="10" <?php if ($_GET['limit'] == 10) {echo 'selected';} ?> >10</option>
                            <option value="20" <?php if ($_GET['limit'] == 20) {echo 'selected';} ?> >20</option>
                            <option value="30" <?php if ($_GET['limit'] == 30) {echo 'selected';} ?> >30</option>
                            <option value="50" <?php if ($_GET['limit'] == 50) {echo 'selected';} ?> >50</option>
                        <?php }
                        ?> 

                        </select>
                    </div>
                    <!-- показывать архивные -->
                    <div class="archive-check">
                        <div >
                            <input type="checkbox" id="archive" name="archive" <?php if($archive == 0 || $archive == 1) {echo "value='archive=0'";} else {echo "value='' checked";} ?>>
                            <!-- <svg class="d-none" width="18px" height="18px" viewBox="5 5 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g id="Interface / Check"> <path id="Vector" d="M6 12L10.2426 16.2426L18.727 7.75732" stroke="#0088cc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g> </g></svg> -->
                        </div>
                        <lable>Архивные</lable>
                    </div>
                    <button class="btn btn-ok d-iblock">Применить</button>

                </div>
            </section>

            <!-- таблица заказов -->
            <section class="orders">
                <table id="list-orders">

                    <thead>
                        <tr role="row">

                            <th class="ta-center" data-id="order_id" data-sort="<?php if ($sortBy == 'order_id')  {echo $mark; } ?>">№</th>
                            <th class="ta-center" data-id="order_date" data-sort="<?php if ($sortBy == 'order_date')  {echo $mark; } ?>">Дата</th>
                            <th class="ta-center" data-id="status" data-sort="<?php if ($sortBy == 'status')  {echo $mark; } ?>">Статус</th>
                           
                            <th class="ta-center" data-id="customer_phone" data-sort="<?php if ($sortBy == 'customer_phone')  {echo $mark; } ?>">Телефон</th>
                            <th class="ta-center" data-id="products">Товары</th>
                            <th class="ta-center" data-id="total_price"  data-sort="<?php if ($sortBy == 'total_price')  {echo $mark; } ?>">Сумма</th>
                            <th class="ta-center" data-id="distance"  data-sort="<?php if ($sortBy == 'distance')  {echo $mark; } ?>">Расстояние</th>
                            <!-- <th data-id="complete_date">Дата завершения</th> -->

                        </tr>
                    </thead>

                    <tbody class="list-orders__body">
                    <!-- контент таблицы из шаблона -->
                    </tbody>

                </table>

                <div class="info-table"></div>
            </section>

            <section class="pagination-wrapper" offset="<?= $offset; ?>"><!-- пагинация --></section>

<?php } ?>



            <!-- ШАБЛОНЫ -->
            <!-- шаблон таблицы -->
            <template id="template-body-table">

                    <tr role="row" class="list-orders__row row-status${status}" order-id="${id}" archive="${archive}" customer-tg-id="${customer_tg_id}" >

                        <td class="ta-center"><a href="javascript: showOrder(${id})"><strong>${order_id}</strong></a></td>
                        <td class="ta-center">${order_date}</td>
                        <td>
                            <div class="ta-center list-orders_status d-block status${status}" title="Изменить статус">${status}</div>
                            <div class="change-status d-none">
                                <select  class="change-order-select" name="change-order" value="">
                            
                                    <option value="status=1">Просмотрен</option>
                                    <option value="status=2">Подтверждён</option>
                                    <option value="status=3">Отменён</option>
                                    <option value="status=5">В доставке</option>
                                    <option value="status=4">Доставлен</option>
                                    <option value="${archive_status}">${archive_text}</option>

                                </select>
                                <span class="save-order" title="Сохранить изменения"><svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke=""><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M18.1716 1C18.702 1 19.2107 1.21071 19.5858 1.58579L22.4142 4.41421C22.7893 4.78929 23 5.29799 23 5.82843V20C23 21.6569 21.6569 23 20 23H4C2.34315 23 1 21.6569 1 20V4C1 2.34315 2.34315 1 4 1H18.1716ZM4 3C3.44772 3 3 3.44772 3 4V20C3 20.5523 3.44772 21 4 21L5 21L5 15C5 13.3431 6.34315 12 8 12L16 12C17.6569 12 19 13.3431 19 15V21H20C20.5523 21 21 20.5523 21 20V6.82843C21 6.29799 20.7893 5.78929 20.4142 5.41421L18.5858 3.58579C18.2107 3.21071 17.702 3 17.1716 3H17V5C17 6.65685 15.6569 8 14 8H10C8.34315 8 7 6.65685 7 5V3H4ZM17 21V15C17 14.4477 16.5523 14 16 14L8 14C7.44772 14 7 14.4477 7 15L7 21L17 21ZM9 3H15V5C15 5.55228 14.5523 6 14 6H10C9.44772 6 9 5.55228 9 5V3Z" fill="#009900"></path> </g></svg></span>
                                <span class="reset-order" title="Сбросить изменения"><svg width="25px" height="25px" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M4.56189 13.5L4.14285 13.9294L4.5724 14.3486L4.99144 13.9189L4.56189 13.5ZM9.92427 15.9243L15.9243 9.92427L15.0757 9.07574L9.07574 15.0757L9.92427 15.9243ZM9.07574 9.92426L15.0757 15.9243L15.9243 15.0757L9.92426 9.07574L9.07574 9.92426ZM19.9 12.5C19.9 16.5869 16.5869 19.9 12.5 19.9V21.1C17.2496 21.1 21.1 17.2496 21.1 12.5H19.9ZM5.1 12.5C5.1 8.41309 8.41309 5.1 12.5 5.1V3.9C7.75035 3.9 3.9 7.75035 3.9 12.5H5.1ZM12.5 5.1C16.5869 5.1 19.9 8.41309 19.9 12.5H21.1C21.1 7.75035 17.2496 3.9 12.5 3.9V5.1ZM5.15728 13.4258C5.1195 13.1227 5.1 12.8138 5.1 12.5H3.9C3.9 12.8635 3.92259 13.2221 3.9665 13.5742L5.15728 13.4258ZM12.5 19.9C9.9571 19.9 7.71347 18.6179 6.38048 16.6621L5.38888 17.3379C6.93584 19.6076 9.54355 21.1 12.5 21.1V19.9ZM4.99144 13.9189L7.42955 11.4189L6.57045 10.5811L4.13235 13.0811L4.99144 13.9189ZM4.98094 13.0706L2.41905 10.5706L1.58095 11.4294L4.14285 13.9294L4.98094 13.0706Z" fill="#444444"></path> </g></svg></span>
                            </div>
                        </td>
                        <!-- <td  class="ta-center">${customer_id}</td> -->
                        <td class="ta-center"><a href="tel:${customer_phone}">${customer_phone}</a></td>
                        <td class="list-orders_products">${products}</td>
                        <td class="ta-center">${total_price}</td>
                        <td class="ta-center">${distance}</td>
                       

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


<?php include('./../components/footer.php'); ?>