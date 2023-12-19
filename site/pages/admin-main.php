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
        "<link rel='stylesheet' href='./../assets/css/list-products.css'>",
        "<link rel='stylesheet' href='./../assets/css/list-orders.css'>",
        "<link rel='stylesheet' href='./../assets/css/admin.css'>"
    ];
    $scriptsSrc = [
        "<script src='./../assets/js/main.js'></script>",
        "<script src='./../assets/js/joint.js'></script>",
        "<script src='./../assets/js/admin.js'></script>"
    ];
?>

<!-- подключим хэдер -->
<?php include('./../components/header.php'); ?>

    <?php
        //записываем в переменную текущую страницу
        $currentPage;
        if(isset($_GET['page']) && $_GET['page'] > 0) {
            $currentPage = $_GET['page'];
        } else {
            $currentPage = 1;
        }

        //соберём данные для отображения в форме 
        $dataJson = file_get_contents($nginxUrl . '/api/analytics/get-count-with-products-sales-by-period.php?vendor_deleted=0');
        $data = json_decode($dataJson, true); 
        //print_r($data);

        //сразу записываем в переменную общее кол-во элементов для вывода внизу таблицы
        $totalEntries = $data['count'];

        //если на странице уже заданы гет-параметры сортировки
        if(isset($_GET['orderby'])) {
            $orderByArray = explode(";", $_GET['orderby']);
            $orderBy = explode(":", $orderByArray[0]);
            $sortBy = $orderBy[0];
            $mark = $orderBy[1];
            //print_r($sortBy);
            //print_r($mark);
        } else {
            $sortBy = '';
            $_GET['orderby'] = 'name:asc';
        }

        //функция конвертации юникс времени в локальное время для календаря в формате Y-m-d
        function convertUnixForCalendar($unixTime) {

            //задаем дефолтный часовой пояс или достаем из куки
            $timeZone = 'UTC';
            if(isset($_COOKIE['time_zone'])) {
                $timeZone = $_COOKIE['time_zone'];
            }
            
            date_default_timezone_set("$timeZone");

            //конвертируем время в часовой пояс, указанный выше
            $date = date('Y-m-d', $unixTime);

            return $date;
        }
    ?>

    <?php
        //достанем актуальный телефон для отображения
        $dataJsonPhone = file_get_contents($nginxUrl . '/api/settings.php?name=phone');
        $phone = json_decode($dataJsonPhone, true); 
        $phone = $phone[0]['value'];

    ?>

    <p class="page-title">Главная</p>

    <br>
    <div class="id-block">
        <p>Телефон для связи:</p>
        <div class="phone-block">

            <p id="phone-number" class="phone"><?= $phone ?></p>

            <span class="edit" onclick="changePhone()">&#9998;</span>
            <span class="save d-none"><svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke=""><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M18.1716 1C18.702 1 19.2107 1.21071 19.5858 1.58579L22.4142 4.41421C22.7893 4.78929 23 5.29799 23 5.82843V20C23 21.6569 21.6569 23 20 23H4C2.34315 23 1 21.6569 1 20V4C1 2.34315 2.34315 1 4 1H18.1716ZM4 3C3.44772 3 3 3.44772 3 4V20C3 20.5523 3.44772 21 4 21L5 21L5 15C5 13.3431 6.34315 12 8 12L16 12C17.6569 12 19 13.3431 19 15V21H20C20.5523 21 21 20.5523 21 20V6.82843C21 6.29799 20.7893 5.78929 20.4142 5.41421L18.5858 3.58579C18.2107 3.21071 17.702 3 17.1716 3H17V5C17 6.65685 15.6569 8 14 8H10C8.34315 8 7 6.65685 7 5V3H4ZM17 21V15C17 14.4477 16.5523 14 16 14L8 14C7.44772 14 7 14.4477 7 15L7 21L17 21ZM9 3H15V5C15 5.55228 14.5523 6 14 6H10C9.44772 6 9 5.55228 9 5V3Z" fill="#009900"></path> </g></svg></span>
            <span class="cancel d-none" onclick="cancel()"><svg width="25px" height="25px" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M4.56189 13.5L4.14285 13.9294L4.5724 14.3486L4.99144 13.9189L4.56189 13.5ZM9.92427 15.9243L15.9243 9.92427L15.0757 9.07574L9.07574 15.0757L9.92427 15.9243ZM9.07574 9.92426L15.0757 15.9243L15.9243 15.0757L9.92426 9.07574L9.07574 9.92426ZM19.9 12.5C19.9 16.5869 16.5869 19.9 12.5 19.9V21.1C17.2496 21.1 21.1 17.2496 21.1 12.5H19.9ZM5.1 12.5C5.1 8.41309 8.41309 5.1 12.5 5.1V3.9C7.75035 3.9 3.9 7.75035 3.9 12.5H5.1ZM12.5 5.1C16.5869 5.1 19.9 8.41309 19.9 12.5H21.1C21.1 7.75035 17.2496 3.9 12.5 3.9V5.1ZM5.15728 13.4258C5.1195 13.1227 5.1 12.8138 5.1 12.5H3.9C3.9 12.8635 3.92259 13.2221 3.9665 13.5742L5.15728 13.4258ZM12.5 19.9C9.9571 19.9 7.71347 18.6179 6.38048 16.6621L5.38888 17.3379C6.93584 19.6076 9.54355 21.1 12.5 21.1V19.9ZM4.99144 13.9189L7.42955 11.4189L6.57045 10.5811L4.13235 13.0811L4.99144 13.9189ZM4.98094 13.0706L2.41905 10.5706L1.58095 11.4294L4.14285 13.9294L4.98094 13.0706Z" fill="#444444"></path> </g></svg></span>
            <!-- <button id="btn-phone" class="btn btn-ok d-iblock" onclick="changePhone()">Изменить</button> -->

        </div>


    </div>

    <!-- далее отрисовываем таблицу заказов за период -->
    

    <br>
    <!-- сортировка по дате -->
    <div class="id-block ta-center">
        <p class="page-title ta-center">Заказано товаров за период</p>
            
    </div>

    <section class="form-filters ta-center">
        <div class="form-elements-container filters-container-flex">

        <div class="d-iblock">
            <?php
                //если еще не переданы гет-параметры сортировки по дате
                if (!isset($_GET['date_from']) && !isset($_GET['date_till'])) {
                ?>
                    c
                    <input id="from" type="date" class="middle-input" onchange="sortByDateFrom()">
                    по
                    <input id="till" type="date" class="middle-input" onchange="sortByDateTill()">
                        
                <?php }
                //а если переданы, отображаем их
                else {
                ?>
                    c
                    <input id="from" type="date" class="middle-input" onchange="sortByDateFrom()" value="<?php if (isset($_GET['date_from'])) { ?><?= convertUnixForCalendar($_GET['date_from']); ?><?php } ?>">
                    по
                    <input id="till" type="date" class="middle-input" onchange="sortByDateTill()"  value="<?php if (isset($_GET['date_till'])) { ?><?= convertUnixForCalendar($_GET['date_till']); ?><?php } ?>">
                        
                <?php } ?>
            </div>

            <!-- фильтрация по городу -->
            <div class="d-iblock">
                <div>Город</div> 
                <select id="city" name="city" value="" required onchange="cityChanged(this)">
                <?php
                    //запрашиваем данные по городам из БД
                    $dataJsonV = file_get_contents($nginxUrl . '/api/cities.php?is_active=1&deleted=0');
                    $dataV = json_decode($dataJsonV, true);

                    //если город не был задан, устанавливаем в селекте выбранное значение "все"
                    if (!isset($_GET['city_id'])) {
                        
                    ?>
                        <option value="" <?= 'selected' ?> >все</option>
                        <?php for ($v = 0; $v < count($dataV); $v++) { ?>
                            <option value="<?= $dataV[$v]['id'] ?>"><?= $dataV[$v]['name'] ?></option>
                        <?php }
                    //если город уже задан в гет-параметрах, выводим его
                    } else {
                        $city = $_GET['city_id']; 
                        ?>

                        <option value="" <?php if($city == 'all') { echo 'selected'; } ?> >все</option>
                        <?php 
                        for ($v = 0; $v < count($dataV); $v++) { 
                            if($city == $dataV[$v]['id']) {?>
                            
                            <option value="<?= $dataV[$v]['id'] ?>" <?= 'selected' ?> ><?= $dataV[$v]['name'] ?></option>
                        <?php 
                            } else { ?>
                                
                            <option value="<?= $dataV[$v]['id'] ?>" ><?= $dataV[$v]['name'] ?></option>
                        <?php 
                            }
                        ?>
                    <?php 
                        }
                    } ?> 
                </select>
            </div>

            <!-- фильтрация по поставщику -->
            <div class="d-iblock">
                <div>Поставщик</div> 
                <select id="vendor" name="vendor" value="" required>
                <?php

                    $dataV = [];
                    //если в гет-параметре еще не задан город
                    if (!isset($_GET['city_id'])) {

                        //запрашиваем данные по поставщикам из БД
                        $dataJsonV = file_get_contents($nginxUrl . '/api/vendors/get-with-details.php?deleted=0&city_deleted=0');
                        $dataV = json_decode($dataJsonV, true);

                    } else {
                        //запрашиваем данные по поставщикам из БД с учетом города
                        $dataJsonV = file_get_contents($nginxUrl . '/api/vendors/get-with-details.php?deleted=0&city_deleted=0&city_id=' . $_GET['city_id']);
                        $dataV = json_decode($dataJsonV, true);
                    }

                    //если поставщик не был задан, устанавливаем в селекте выбранное значение "все"
                    if (!isset($_GET['vendor_id'])) {
                        
                    ?>
                        <option value="" <?= 'selected' ?> >все</option>
                        <?php for ($v = 0; $v < count($dataV); $v++) { ?>
                            <option value="<?= $dataV[$v]['id'] ?>"><?= $dataV[$v]['name'] ?></option>
                        <?php }
                    //если поставщик уже задан в гет-параметрах, выводим его
                    } else {
                        $vendor = $_GET['vendor_id']; 
                        ?>

                        <option value="" <?php if($vendor == 'all') { echo 'selected'; } ?> >все</option>
                        <?php 
                        for ($v = 0; $v < count($dataV); $v++) { 
                            if($vendor == $dataV[$v]['id']) {?>
                            
                            <option value="<?= $dataV[$v]['id'] ?>" <?= 'selected' ?>><?= $dataV[$v]['name'] ?></option>
                        <?php 
                            } else { ?>
                                
                            <option value="<?= $dataV[$v]['id'] ?>"><?= $dataV[$v]['name'] ?></option>
                        <?php 
                            }
                        ?>
                    <?php 
                        }
                    } ?> 
                </select>
            </div>

            <div class="d-iblock">Показывать по
                <select id="limit" name="limit" value="" required>
                <?php
                    //если лимит не был задан
                    if (!isset($_GET['limit'])) {
                    ?>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <!-- если задан поиск, лимит переключается на "все" -->
                        <option value="all" <?php if (isset($_GET['search'])) {echo 'selected';} ?> >все</option>
                    <?php
                    //если лимит уже задан
                    } else {
                        $limit = $_GET['limit'];
                        ?>
                        <option value="10" <?php if ($_GET['limit'] == 10) {echo 'selected';} ?> >10</option>
                        <option value="20" <?php if ($_GET['limit'] == 20) {echo 'selected';} ?> >20</option>
                        <option value="50" <?php if ($_GET['limit'] == 50) {echo 'selected';} ?> >50</option>
                        <option value="all" <?php if ($_GET['limit'] == 'all' || isset($_GET['search'])) {echo 'selected';} ?> >все</option>
                    <?php }
                    ?> 
                </select>
            </div>

            <!-- поле поиска -->
            <!-- <div class="d-iblock">
                <div>Поиск</div>
                <input type="search" id="search" name="search" value="" placeholder="Наименование" class="middle-input">
            </div> -->

            <!-- кнопка, активирующая выбранный лимит записей на странице и поиск -->
            <button onclick="applyInMain()" class="btn btn-ok d-iblock">Применить</button>
        </div>

    </section>

    
    <?php
        //теперь сокращаем выдачу данных в массиве до products
        $data = $data['products'];
        //print_r($data);

        //проверяем, выбран ли лимит на кол-во отображаемых элементов
        if (isset($_GET['limit'])) {
            //если выбрано отображать все
            if($_GET['limit'] == 'all') {
                $limit = count($data);
            } 
        //если лимит не выбран, задаем дефолтное значение
        } else {
            $limit = 10;
        }

        //считаем и записываем в переменные общее кол-во страниц и оффсет
        $totalPages = ceil(count($data) / $limit);
        $offset = ($currentPage - 1) * $limit;
?>

        <!-- таблица заказанных товаров за указанный период -->
        <section class="orders">
            <table id="list-orders" data-section="admin-main" data-limit="<?php if (isset($_GET['limit'])) {?><?=$limit?><?php } else { ?><?=$limit?><?php } ?>" <?php if (isset($_GET['page'])) { ?> data-page="<?= $_GET['page'] ?>" <?php } else if (isset($_GET['search'])) { ?> data-search="<?= $_GET['search'] ?>" <?php } ?> data-vendor-select="<?php if (isset($_GET['vendor_id'])) { echo $_GET['vendor_id']; } ?>" data-city-select="<?php if (isset($_GET['city_id'])) { echo $_GET['city_id']; } ?>">

                <thead>
                    <tr role="row">

                        <!-- <th class="ta-center cell-title" data-id="id" data-sort="<?php if ($sortBy == 'id')  {echo $mark; } ?>">№</th> -->
                        <!-- <th class="ta-center">№</th> -->
                        <th class="ta-center cell-title" data-id="vendor_city" data-sort="<?php if ($sortBy == 'vendor_city')  {echo $mark; } ?>">Город</th>
                        <th class="ta-center cell-title" data-id="vendor_name" data-sort="<?php if ($sortBy == 'vendor_name')  {echo $mark; } ?>">Поставщик</th>
                        <th class="ta-center cell-title" data-id="name" data-sort="<?php if ($sortBy == 'name')  {echo $mark; } ?>">Наименование</th>
                        <th class="ta-center cell-title" data-id="price" data-sort="<?php if ($sortBy == 'price')  {echo $mark; } ?>">Цена за 1 ед.
                        <th class="ta-center cell-title" data-id="quantity" data-sort="<?php if ($sortBy == 'quantity')  {echo $mark; } ?>">Кол-во
                        <th class="ta-center cell-title" data-id="total_price" data-sort="<?php if ($sortBy == 'total_price')  {echo $mark; } ?>">Цена за период</th>
                    </tr>
                </thead>

                <tbody class="list-orders__body">
                <tr role="row" class="list-orders__row">

                    <?php

                    //собираем в отдельную переменную фильтры
                    $params = '';
                    
                    //если заданы гет-параметры даты, собираем их в переменную
                    if (isset($_GET['date_from']) || isset($_GET['date_till'])) {
                        if (isset($_GET['date_from'])) {
                            $params = $params . '&date_from=' . $_GET['date_from'];
                        }
                        if (isset($_GET['date_till'])) {
                            $params = $params . '&date_till=' . $_GET['date_till'];
                        }
                    }

                    //если заданы гет-параметры поставщика, собираем в переменную
                    if (isset($_GET['vendor_id'])) {
                        $params = $params . '&vendor_id=' . $_GET['vendor_id'];
                    }

                    //если заданы гет-параметры города, собираем в переменную
                    if (isset($_GET['city_id'])) {
                        $params = $params . '&city_id=' . $_GET['city_id'];
                    }

                    //считаем суммарную стоимость всех товаров за весь период, чтобы вывести ее отдельно
                    $totalSum = 0;
                    $dataJson = file_get_contents($nginxUrl . '/api/analytics/get-count-with-products-sales-by-period.php?' . $params);
                    $data = json_decode($dataJson, true); 
                    $data = $data['products'];
                    for ($l = 0; $l < count($data); $l++) {
                        $totalSum += $data[$l]['total_price'];
                    }

                    //если мы НЕ на первой странице
                    if(isset($_GET['page']) && $_GET['page'] > 1) {
                        //соберём данные для отображения в форме 
                        $dataJson = file_get_contents($nginxUrl . '/api/analytics/get-count-with-products-sales-by-period.php?offset=' . $offset .'&limit=' . $limit . '&orderby=' . $_GET['orderby'] . $params);
                        $data = json_decode($dataJson, true); 

                        //сразу записываем в переменную общее кол-во элементов для вывода внизу таблицы
                        $dataJsonN = file_get_contents($nginxUrl . '/api/analytics/get-count-with-products-sales-by-period.php?&orderby=' . $_GET['orderby'] . $params);
                        $dataN = json_decode($dataJsonN, true); 
                        $totalEntries = $dataN['count'];
                        //и об общем кол-ве страниц
                        $totalPages = ceil((int)$dataN['count'] / $limit);

                        $data = $data['products'];
                        //print_r($data);
                        //$num = $offset + 1; //переменная для отображения порядкового номера (чтобы не было пропусков, т.к. некоторые id "удалены")

                    //если мы на первой странице
                    } elseif(!isset($_GET['page']) || $_GET['page'] == 1) {
                        //и поиск не активирован
                        if (!isset($_GET['search'])) {
                            //print_r('not 1');
                            //соберём данные для отображения в форме 
                            $dataJson = file_get_contents($nginxUrl . '/api/analytics/get-count-with-products-sales-by-period.php?limit=' . $limit . '&orderby=' . $_GET['orderby'] . $params);
                            $data = json_decode($dataJson, true); 

                            //сразу записываем в переменную общее кол-во элементов для вывода внизу таблицы
                            $dataJsonN = file_get_contents($nginxUrl . '/api/analytics/get-count-with-products-sales-by-period.php?&orderby=' . $_GET['orderby'] . $params);
                            $dataN = json_decode($dataJsonN, true); 
                            $totalEntries = $dataN['count'];
                            //и об общем кол-ве страниц
                            $totalPages = ceil((int)$dataN['count'] / $limit);

                            $data = $data['products'];
                            //print_r($data);
                            //$num = 1; //переменная для отображения порядкового номера (чтобы не было пропусков, т.к. некоторые id "удалены")
                        }
                    }

                    //если активирован поиск
                    // if(isset($_GET['search'])) {
                    //     $dataJson = file_get_contents($nginxUrl . '/api/analytics/get-count-with-products-sales-by-period.php?limit=all&offset=0&orderby=' . $_GET['orderby'] . '&search=' . $_GET['search'] . $params);
                    //     $data = json_decode($dataJson, true);
                    //     //если по данному поисковому запросу записей нет, записываем в переменную 0
                    //     if (!$data) {
                    //         $totalEntries = 0;
                    //     //если есть, записываем в переменную их количество
                    //     } else {
                    //         $totalEntries = $data['count'];
                    //         //$num = 1; //переменная для отображения порядкового номера (чтобы не было пропусков, т.к. некоторые id "удалены")
                    //     }
                    //     //сокращаем массив до данных только по заказам для выведения в форме
                    //     $data = $data['products'];
                    //     print_r($data);
                    // }

                    
                    if ($data) {
                        //print_r('yes');
                        //отрисовываем строки в цикле от начальной до конечной цифры оффсетного значения
                        for ($i = 0; $i < count($data); $i++) {
                            //проверка на то, чтобы выводилось не больше строк, чем есть в БД
                            if(isset($data[$i]['vendor_id'])) { ?>
                                
                            <!-- вносим в атрибуты общее кол-во страниц и текущую страницу для js -->
                            <tr id="pages-info" role="row" class="list-orders__row" data-pages="<?= $totalPages ?>" data-current-page="<?= $currentPage ?>">
                                <!-- <td class="ta-center"><a href="vendor-order.php?id=<?= $data[$i]['id'] ?>&role=1"><strong><?= $data[$i]['id'] ?></strong></a></td> -->
                                <td class="ta-center"><?= $data[$i]['vendor_city'] ?></td>
                                <td class="ta-center"><?= $data[$i]['vendor_name'] ?></td>
                                <td><?= $data[$i]['name'] ?></td>
                                <td class="ta-center"><?= number_format($data[$i]['price'], 0, ',', ' '); ?></td>
                                <td class="ta-center"><?= $data[$i]['quantity'] ?></td>
                                <!-- выводим общую стоимость в нужном формате -->
                                <td class="ta-center"><?= number_format($data[$i]['total_price'], 0, ',', ' '); ?> </td>
                            </tr>
                            <?php }
                            }
                    } else {
                        $totalEntries = 0;
                    }?>

                </tr>
            </tbody>

        </table>

        <!-- внизу таблицы выдаем общее количество записей -->
        <div class="info-table">Всего записей: <?= $totalEntries ?></div>
    </section>

    <!-- выводим общую стоимость в нужном формате -->
    <div>Итого сумма за период: <b><?= number_format($totalSum, 0, ',', ' '); ?></b></div>

    <!-- если НЕ одна страница и НЕ задан поиск, показываем внизу пагинацию -->
    <?php if($totalPages > 1 && !isset($_GET['search'])) { ?>
    <section class="pagination-wrapper">
        <div class="page-switch">                 
            <a <?php if($currentPage > 1) { ?>href="?limit=<?= $limit ?>&page=<?= $currentPage - 1; ?><?php if(isset($_GET['orderby'])) {?>&orderby=<?= $_GET['orderby'] ?><?php } ?><?= $params ?>"<?php } ?> class="page-switch__prev" <?php if($currentPage <= 1) { ?>  disabled <?php } ?> > 
                <svg  class="fill" viewBox="0 8 23 16" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>down</title> <path d="M11.125 16.313l7.688-7.688 3.594 3.719-11.094 11.063-11.313-11.313 3.5-3.531z"></path> </g></svg>
            </a>
            
            <span class="current-page"><?= $currentPage ?></span>
            <a <?php if($currentPage != $totalPages) { ?>href="?limit=<?= $limit ?>&page=<?= $currentPage + 1; ?><?php if(isset($_GET['orderby'])) {?>&orderby=<?= $_GET['orderby'] ?><?php } ?><?= $params ?>"<?php } ?> class="page-switch__next"  <?php if($currentPage == $totalPages) { ?>  disabled <?php } ?> >
                <svg  class="fill" viewBox="0 8 23 16" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>down</title> <path d="M11.125 16.313l7.688-7.688 3.594 3.719-11.094 11.063-11.313-11.313 3.5-3.531z"></path> </g></svg>
            </a>
        </div>
        <div class="page-status">стр <span class="current-page"><?= $currentPage ?></span> из <span class="total-page"><?= $totalPages ?></span></div>
    </section>
    <?php } ?>

<!-- подключим футер -->
<?php include('./../components/footer.php'); ?>