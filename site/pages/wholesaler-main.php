<?php require('../handler/check-profile.php'); 
if($role !== 3) {
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
        //$totalEntries = $data['count']; ВЕРНУТЬ

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

    <p class="page-title">Главная</p>


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

            <!-- фильтрация по поставщику -->
            <div class="d-iblock">
                <div>Поставщик</div> 
                <select id="vendor" name="vendor" value="" required>
                <?php
                    //запрашиваем данные по поставщикам из БД
                    $dataJsonV = file_get_contents($nginxUrl . '/api/vendors/get-with-details.php?deleted=0&city_deleted=0');
                    $dataV = json_decode($dataJsonV, true);

                    //если поставщик не был задан, устанавливаем в селекте выбранное значение "все"
                    if (!isset($_GET['vendor_name'])) {
                        
                    ?>
                        <option value="" <?= 'selected' ?> >все</option>
                        <?php for ($v = 0; $v < count($dataV); $v++) { ?>
                            <option value="<?= $dataV[$v]['name'] ?>"><?= $dataV[$v]['name'] ?></option>
                        <?php }
                    //если поставщик уже задан в гет-параметрах, выводим его
                    } else {
                        $vendor = $_GET['vendor_name']; 
                        ?>

                        <option value="" <?php if($vendor == 'all') { echo 'selected'; } ?> >все</option>
                        <?php 
                        for ($v = 0; $v < count($dataV); $v++) { 
                            if($vendor == $dataV[$v]['name']) {?>
                            
                            <option value="<?= $dataV[$v]['name'] ?>" <?= 'selected' ?>><?= $dataV[$v]['name'] ?></option>
                        <?php 
                            } else { ?>
                                
                            <option value="<?= $dataV[$v]['name'] ?>"><?= $dataV[$v]['name'] ?></option>
                        <?php 
                            }
                        ?>
                    <?php 
                        }
                    } ?> 
                </select>
            </div>

            <!-- фильтрация по городу -->
            <div class="d-iblock">
                <div>Город</div> 
                <select id="city" name="city" value="" required>
                <?php
                    //запрашиваем данные по городам из БД
                    $dataJsonV = file_get_contents($nginxUrl . '/api/cities.php?is_active=1&deleted=0');
                    $dataV = json_decode($dataJsonV, true);

                    //если город не был задан, устанавливаем в селекте выбранное значение "все"
                    if (!isset($_GET['vendor_city'])) {
                        
                    ?>
                        <option value="" <?= 'selected' ?> >все</option>
                        <?php for ($v = 0; $v < count($dataV); $v++) { ?>
                            <option value="<?= $dataV[$v]['name'] ?>"><?= $dataV[$v]['name'] ?></option>
                        <?php }
                    //если город уже задан в гет-параметрах, выводим его
                    } else {
                        $city = $_GET['vendor_city']; 
                        ?>

                        <option value="" <?php if($city == 'all') { echo 'selected'; } ?> >все</option>
                        <?php 
                        for ($v = 0; $v < count($dataV); $v++) { 
                            if($city == $dataV[$v]['name']) {?>
                            
                            <option value="<?= $dataV[$v]['name'] ?>" <?= 'selected' ?>><?= $dataV[$v]['name'] ?></option>
                        <?php 
                            } else { ?>
                                
                            <option value="<?= $dataV[$v]['name'] ?>"><?= $dataV[$v]['name'] ?></option>
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

        // УБРАТЬ ______
        //print_r($data);

        ?> 
        
        <!-- <br><br> -->

        <?php

        //достаем из БД categories данного залогинившегося поставщика
        $categoriesJSON = file_get_contents($nginxUrl . '/api/vendors/get-with-details.php?id=' . $profile['id']);
        $categories = json_decode($categoriesJSON, true);
        $categories = $categories[0]['categories'];
        $categoriesArr = json_decode($categories, true);
        //print_r($categoriesArr);

        ?> 
        
        <!-- <br><br> -->

        <?php

        //фильтруем массив только по нужным категориям
        $byCategory = [];
        for ($i = 0; $i <  count($data); $i++) { 
            for ($k = 0; $k < count($categoriesArr); $k++) {
                if ($data[$i]['category_id'] == $categoriesArr[$k]) {
                    array_push($byCategory, $data[$i]);
                }
            }

        }

        //print_r($byCategory);
        $data = $byCategory;

        //сразу записываем в переменную общее кол-во элементов для вывода внизу таблицы
        $totalEntries = count($data);

        // ______ УБРАТЬ 

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
            <table id="list-orders" data-section="admin-main" data-limit="<?php if (isset($_GET['limit'])) {?><?=$limit?><?php } else { ?><?=$limit?><?php } ?>" <?php if (isset($_GET['page'])) { ?> data-page="<?= $_GET['page'] ?>" <?php } else if (isset($_GET['search'])) { ?> data-search="<?= $_GET['search'] ?>" <?php } ?> data-vendor-select="<?php if (isset($_GET['vendor_name'])) { echo $_GET['vendor_name']; } ?>">

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
                    if (isset($_GET['vendor_name'])) {
                        $params = $params . '&vendor_name=' . $_GET['vendor_name'];
                    }

                    //если заданы гет-параметры города, собираем в переменную
                    if (isset($_GET['vendor_city'])) {
                        $params = $params . '&vendor_city=' . $_GET['vendor_city'];
                    }

                    //считаем суммарную стоимость всех товаров за весь период, чтобы вывести ее отдельно
                    $totalSum = 0;
                    $dataJson = file_get_contents($nginxUrl . '/api/analytics/get-count-with-products-sales-by-period.php?' . $params);
                    $data = json_decode($dataJson, true); 
                    $data = $data['products'];

                    // УБРАТЬ _______
                    //фильтруем массив только по нужным категориям
                    $byCategory = [];
                    for ($i = 0; $i <  count($data); $i++) { 
                        for ($k = 0; $k < count($categoriesArr); $k++) {
                            if ($data[$i]['category_id'] == $categoriesArr[$k]) {
                                array_push($byCategory, $data[$i]);
                            }
                        }

                    }

                    //print_r($byCategory);
                    $data = $byCategory;
                    // _______ УБРАТЬ 

                    for ($l = 0; $l < count($data); $l++) {
                        $totalSum += $data[$l]['total_price'];
                    }

                    //если мы НЕ на первой странице
                    if(isset($_GET['page']) && $_GET['page'] > 1) {
                        //соберём данные для отображения в форме 
                        $dataJson = file_get_contents($nginxUrl . '/api/analytics/get-count-with-products-sales-by-period.php?offset=' . $offset .'&limit=' . $limit . '&orderby=' . $_GET['orderby'] . $params);
                        $data = json_decode($dataJson, true); 
                        $data = $data['products'];

                        // УБРАТЬ _______
                        //фильтруем массив только по нужным категориям
                        $byCategory = [];
                        for ($i = 0; $i <  count($data); $i++) { 
                            for ($k = 0; $k < count($categoriesArr); $k++) {
                                if ($data[$i]['category_id'] == $categoriesArr[$k]) {
                                    array_push($byCategory, $data[$i]);
                                }
                            }

                        }

                        //print_r($byCategory);
                        $data = $byCategory;
                        // _______ УБРАТЬ
                        
                        //print_r($data);
                        //$num = $offset + 1; //переменная для отображения порядкового номера (чтобы не было пропусков, т.к. некоторые id "удалены")

                    //если мы на первой странице
                    } elseif(!isset($_GET['page']) || $_GET['page'] == 1) {
                        //и поиск не активирован
                        if (!isset($_GET['search'])) {
                            //print_r('not 1');
                            //соберём данные для отображения в форме 
                            $dataJson = file_get_contents($nginxUrl . '/api/analytics/get-count-with-products-sales-by-period.php?limit=' . $limit . '&offset=0&orderby=' . $_GET['orderby'] . $params);
                            $data = json_decode($dataJson, true); 
                            $data = $data['products'];

                            // УБРАТЬ _______
                            //фильтруем массив только по нужным категориям
                            $byCategory = [];
                            for ($i = 0; $i <  count($data); $i++) { 
                                for ($k = 0; $k < count($categoriesArr); $k++) {
                                    if ($data[$i]['category_id'] == $categoriesArr[$k]) {
                                        array_push($byCategory, $data[$i]);
                                    }
                                }

                            }

                            //print_r($byCategory);
                            $data = $byCategory;
                            // _______ УБРАТЬ

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