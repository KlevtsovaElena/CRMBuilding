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
        $dataJson = file_get_contents("http://nginx/api/order-vendors/get-count-with-details.php?vendor_deleted=0");
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
            $_GET['orderby'] = 'order_date:desc';
        }

        //функция конвертации юникс времени в локальное время на клиенте в формате d.m.Y H:i
        function convertUnixToLocalTime($unixTime) {
            
            //задаем дефолтный часовой пояс или достаем из куки
            $timeZone = 'UTC';
            if(isset($_COOKIE['time_zone'])) {
                $timeZone = $_COOKIE['time_zone'];
            }

            date_default_timezone_set("$timeZone");
        
            //конвертируем время в часовой пояс, указанный выше
            $localTime = date('d.m.Y H:i', $unixTime);
        
            return $localTime;
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

        <!-- далее отрисовываем всю страницу -->
        <p class="page-title">Заказы</p>

        <section class="form-filters">
            <div class="form-elements-container">
                <!-- выбор кол-ва отображаемых записей на странице -->
                <div class="id-block">
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
                <input type="search" id="search" name="search" value="" placeholder="№ заказа">
                <!-- кнопка, активирующая выбранный лимит записей на странице и поиск -->
                <button onclick="applyInOrders()" class="btn btn-ok d-iblock">Применить</button>
            </div>

        </section>

        <?php
        //теперь сокращаем выдачу данных в массиве до orders
        $data = $data['orders'];
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

        <!-- таблица заказов -->
        <section class="orders">
            <table id="list-orders" data-section="admin-orders" data-limit="<?php if (isset($_GET['limit'])) {?><?=$limit?><?php } else { ?><?=$limit?><?php } ?>" <?php if (isset($_GET['page'])) { ?> data-page="<?= $_GET['page'] ?>" <?php } else if (isset($_GET['search'])) { ?> data-search="<?= $_GET['search'] ?>" <?php } ?> >

                <thead>
                    <tr role="row">

                        <th class="ta-center cell-title" data-id="order_id" data-sort="<?php if ($sortBy == 'order_id')  {echo $mark; } ?>">№</th>
                        <!-- <th class="ta-center">№</th> -->
                        <th class="ta-center cell-title" data-id="order_date" data-sort="<?php if ($sortBy == 'order_date')  {echo $mark; } ?>">Дата</th>
                        <th class="ta-center cell-title" data-id="vendor_name" data-sort="<?php if ($sortBy == 'vendor_name')  {echo $mark; } ?>">Поставщик</th>
                        <th class="ta-center cell-title" data-id="vendor_city" data-sort="<?php if ($sortBy == 'vendor_city')  {echo $mark; } ?>">Город</th>
                        <th class="ta-center cell-title" data-id="status" data-sort="<?php if ($sortBy == 'status')  {echo $mark; } ?>">Статус</th>
                        <th class="ta-center cell-title" data-id="customer_phone" data-sort="<?php if ($sortBy == 'customer_phone')  {echo $mark; } ?>">Телефон</th>
                        <th class="ta-center">Товары</th>
                        <th class="ta-center cell-title" data-id="total_price" data-sort="<?php if ($sortBy == 'total_price')  {echo $mark; } ?>">Сумма</th>

                    </tr>
                </thead>

                <tbody class="list-orders__body">
                <tr role="row" class="list-orders__row">

                    <?php

                    //если заданы гет-параметры даты, собираем их в переменную
                    $dateParams = '';
                    
                    if (isset($_GET['date_from']) || isset($_GET['date_till'])) {
                        if (isset($_GET['date_from'])) {
                            $dateParams = $dateParams . '&date_from=' . $_GET['date_from'];
                        }
                        if (isset($_GET['date_till'])) {
                            $dateParams = $dateParams . '&date_till=' . $_GET['date_till'];
                        }
                        //print_r($dateParams);
                    }
                    //если еще НЕ задан гет-параметр сортировки полей таблицы по одному ключу
                    if (!isset($_GET['orderby'])) {

                        //если мы НЕ на первой странице
                        if(isset($_GET['page']) && $_GET['page'] > 1) {
                            //соберём данные для отображения в форме 
                            $dataJson = file_get_contents("http://nginx/api/order-vendors/get-count-with-details.php?vendor_deleted=0&limit=" . $limit . '&offset=' . $offset . $dateParams);
                            $data = json_decode($dataJson, true); 
                            $data = $data['orders'];
                            //$num = $offset + 1; //переменная для отображения порядкового номера (чтобы не было пропусков, т.к. некоторые id "удалены")

                        //если мы на первой странице
                        } elseif(!isset($_GET['page']) || $_GET['page'] == 1) {
                            //и поиск не активирован
                            if (!isset($_GET['search'])) {
                                //соберём данные для отображения в форме 
                                $dataJson = file_get_contents("http://nginx/api/order-vendors/get-count-with-details.php?vendor_deleted=0&limit=" . $limit . '&offset=0' . $dateParams);
                                $data = json_decode($dataJson, true); 
                                $data = $data['orders'];
                                //$num = 1; //переменная для отображения порядкового номера (чтобы не было пропусков, т.к. некоторые id "удалены")
                            }
                        }

                        //если активирован поиск
                        if(isset($_GET['search'])) {
                            $dataJson = file_get_contents("http://nginx/api/order-vendors/get-count-with-details.php?vendor_deleted=0&search=" . $_GET['search'] . $dateParams);
                            $data = json_decode($dataJson, true);
                            //если по данному поисковому запросу записей нет, записываем в переменную 0
                            if (!$data) {
                                $totalEntries = 0;
                            //если есть, записываем в переменную их количество
                            } else {
                                $totalEntries = $data['count'];
                                //$num = 1; //переменная для отображения порядкового номера (чтобы не было пропусков, т.к. некоторые id "удалены")
                            }
                            //сокращаем массив до данных только по заказам для выведения в форме
                            $data = $data['orders'];
                        }
                             
                    }
                        
                    //если уже имеется гет-параметр сортировки полей таблицы по одному ключу
                    elseif (isset($_GET['orderby'])) {

                        //если мы НЕ на первой странице
                        if(isset($_GET['page']) && $_GET['page'] > 1) {
                            //соберём данные для отображения в форме 
                            $dataJson = file_get_contents('http://nginx/api/order-vendors/get-count-with-details.php?vendor_deleted=0&offset=' . $offset .'&limit=' . $limit . '&orderby=' . $_GET['orderby'] . $dateParams);
                            $data = json_decode($dataJson, true); 
                            $data = $data['orders'];
                            //print_r($data);
                            //$num = $offset + 1; //переменная для отображения порядкового номера (чтобы не было пропусков, т.к. некоторые id "удалены")

                        //если мы на первой странице
                        } elseif(!isset($_GET['page']) || $_GET['page'] == 1) {
                            //и поиск не активирован
                            if (!isset($_GET['search'])) {
                                //соберём данные для отображения в форме 
                                $dataJson = file_get_contents("http://nginx/api/order-vendors/get-count-with-details.php?vendor_deleted=0&limit=" . $limit . '&offset=0&orderby=' . $_GET['orderby'] . $dateParams);
                                $data = json_decode($dataJson, true); 
                                $data = $data['orders'];
                                //$num = 1; //переменная для отображения порядкового номера (чтобы не было пропусков, т.к. некоторые id "удалены")
                            }
                        }

                        //если активирован поиск
                        if(isset($_GET['search'])) {
                            $dataJson = file_get_contents('http://nginx/api/order-vendors/get-count-with-details.php?vendor_deleted=0&limit=all&offset=0&orderby=' . $_GET['orderby'] . '&search=' . $_GET['search'] . $dateParams);
                            $data = json_decode($dataJson, true);
                            //если по данному поисковому запросу записей нет, записываем в переменную 0
                            if (!$data) {
                                $totalEntries = 0;
                            //если есть, записываем в переменную их количество
                            } else {
                                $totalEntries = $data['count'];
                                //$num = 1; //переменная для отображения порядкового номера (чтобы не было пропусков, т.к. некоторые id "удалены")
                            }
                            //сокращаем массив до данных только по заказам для выведения в форме
                            $data = $data['orders'];
                        }

                    } 
                    
                    if ($data) {
                        //отрисовываем строки в цикле от начальной до конечной цифры оффсетного значения
                        for ($i = 0; $i < count($data); $i++) {
                            //проверка на то, чтобы выводилось не больше строк, чем есть в БД
                            if(isset($data[$i]['id'])) {
                                //расфишровка статусов
                                $status;
                                if($data[$i]['status'] == 0) {
                                    $status = 'Новый';
                                }
                                if($data[$i]['status'] == 1) {
                                    $status = 'Просмотрен';
                                }
                                if($data[$i]['status'] == 2) {
                                    $status = 'Подтвержден';
                                }
                                if($data[$i]['status'] == 3) {
                                    $status = 'Отменен';
                                }
                                if($data[$i]['status'] == 4) {
                                    $status = 'Завершен';
                                } 
                                ?>
                            <!-- вносим в атрибуты общее кол-во страниц и текущую страницу для js -->
                            <tr id="pages-info" role="row" class="list-orders__row" data-pages="<?= $totalPages ?>" data-current-page="<?= $currentPage ?>">
                                <td class="ta-center"><a href="vendor-order.php?id=<?= $data[$i]['id'] ?>&role=1"><strong><?= $data[$i]['order_id'] ?></strong></a></td>
                                <!-- конвертация юникс времени в стандартное в формате d.m.Y H:i -->
                                <td><?= convertUnixToLocalTime($data[$i]['order_date']); ?></td>
                                <td><?= $data[$i]['vendor_name'] ?></td>
                                <td><?= $data[$i]['vendor_city'] ?></td>
                                <td><a class="list-orders_status d-block status<?= $data[$i]['status'] ?>"><?= $status ?></a></td>
                                <td><?= $data[$i]['customer_phone'] ?></td>
                                <!-- в отдельном цикле отрисовываем весь список продуктов в данном заказе -->
                                <td class="list-orders_products">
                                <?php for ($p = 0; $p < count($data[$i]['products']); $p++) { ?> 
                                    <?= $data[$i]['products'][$p]['name'] ?> (<?= $data[$i]['products'][$p]['quantity'] ?>)<?php if($data[$i]['products'] > 1 && $p != (count($data[$i]['products']) - 1)) {?>, <?php }
                                } ?>
                                </td>
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

    <!-- если НЕ одна страница и НЕ задан поиск, показываем внизу пагинацию -->
    <?php if($totalPages > 1 && !isset($_GET['search'])) { ?>
    <section class="pagination-wrapper">
        <div class="page-switch">                 
            <a href="?limit=<?= $limit ?>&page=<?= $currentPage - 1; ?><?php if(isset($_GET['orderby'])) {?>&orderby=<?= $_GET['orderby'] ?><?php } ?>" class="page-switch__prev" <?php if($currentPage <= 1) { ?>  disabled <?php } ?> > 
                <svg  class="fill" viewBox="0 8 23 16" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>down</title> <path d="M11.125 16.313l7.688-7.688 3.594 3.719-11.094 11.063-11.313-11.313 3.5-3.531z"></path> </g></svg>
            </a>
            
            <span class="current-page"><?= $currentPage ?></span>
            <a href="?limit=<?= $limit ?>&page=<?= $currentPage + 1; ?><?php if(isset($_GET['orderby'])) {?>&orderby=<?= $_GET['orderby'] ?><?php } ?>" class="page-switch__next"  <?php if($currentPage == $totalPages) { ?>  disabled <?php } ?> >
                <svg  class="fill" viewBox="0 8 23 16" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>down</title> <path d="M11.125 16.313l7.688-7.688 3.594 3.719-11.094 11.063-11.313-11.313 3.5-3.531z"></path> </g></svg>
            </a>
        </div>
        <div class="page-status">стр <span class="current-page"><?= $currentPage ?></span> из <span class="total-page"><?= $totalPages ?></span></div>
    </section>
    <?php } ?>

<!-- подключим футер -->
<?php include('./../components/footer.php'); ?>

