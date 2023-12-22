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
        // "<link rel='stylesheet' href='./../assets/css/list-products.css'>",
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
        $dataJson = file_get_contents($nginxUrl . '/api/order-vendors/get-count-with-details.php?vendor_deleted=0');
        $data = json_decode($dataJson, true); 
        //print_r($data);

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
            <div class="form-elements-container filters-container-flex">
                <!-- выбор кол-ва отображаемых записей на странице -->
                <?php
                //если еще не переданы гет-параметры сортировки по дате
                if (!isset($_GET['date_from']) && !isset($_GET['date_till'])) {
                ?>
                <div class="id-block">
                    c
                    <input id="from" type="date" class="middle-input" onchange="sortByDateFrom()">
                </div>

                <div class="id-block">
                    по
                    <input id="till" type="date" class="middle-input" onchange="sortByDateTill()">
                </div>

                <?php }
                //а если переданы, отображаем их
                else {
                ?>
                <div class="id-block">
                    c
                    <input id="from" type="date" class="middle-input" onchange="sortByDateFrom()" value="<?php if (isset($_GET['date_from'])) { ?><?= convertUnixForCalendar($_GET['date_from']); ?><?php } ?>">
                </div>

                <div class="id-block">
                    по
                    <input id="till" type="date" class="middle-input" onchange="sortByDateTill()"  value="<?php if (isset($_GET['date_till'])) { ?><?= convertUnixForCalendar($_GET['date_till']); ?><?php } ?>">
                </div>
                <?php } ?>

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
                                <option value="<?= $dataV[$v]['id'] ?>" ><?= $dataV[$v]['name'] ?></option>
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

                <!-- фильтрация по статусу заказа -->
                <div class="d-iblock">
                    <div>Статус</div> 
                    <select id="status" name="status" value="" required>
                        <?php
                        //список статусов
                        $statuses = array(
                            0 => 'Новый',
                            1 => 'Просмотрен',
                            2 => 'Подтверждён',
                            3 => 'Отменён',
                            4 => 'Доставлен',
                            5 => 'В архиве'
                        );

                        $statusSel = '';

                        //если статус не был задан, устанавливаем в селекте выбранное значение "все"
                        if (!isset($_GET['status']) && (!isset($_GET['archive']) || $_GET['archive'] == 0)) {
                        ?>
                            <option value="all" <?= 'selected' ?> >все</option>
                            
                        <?php 
                        //если статус в гет-параметрах задан статус 5 "В архиве", выводим его
                        } elseif (isset($_GET['archive']) && $_GET['archive'] == 1) {
                            $statusSel = 5; ?>
                            <option value="all">все</option>
                        <?php
                        //если задан статус 0-4 
                        } elseif (isset($_GET['status'])) {
                            $statusSel = $_GET['status']; ?>
                            <option value="all">все</option>
                        <?php 
                        } ?>
                            <?php 
                            for ($s = 0; $s < count($statuses); $s++) { 
                                if($statusSel == $s) {?>
                                
                                <option value="<?= $s ?>"  <?= 'selected' ?>><?= $statuses[$s] ?></option>
                            <?php 
                                } else { ?>
                                    
                                <option value="<?= $s ?>"><?= $statuses[$s] ?></option>
                            <?php 
                                }
                            ?>
                        <?php 
                            } ?> 
                    </select>
                </div>

                <!-- поле поиска -->
                <div class="d-iblock">
                    <div>Поиск</div>
                    <input type="search" id="search" name="search" value="" placeholder="№заказа" class="middle-input">
                </div>

                <!-- фильтрация по кол-ву отображаемых элементов на странице -->
                <div class="d-iblock">
                    <div>Показывать по </div>
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

                <!-- чекбокс для архивных заказов -->
                <div class="archive-check">
                    <div>
                        <input type="checkbox" id="archive" name="archive" onclick="archiveChecked()" value="" <?php if (isset($_GET['archived']) || isset($_GET['archive']) && $_GET['archive'] == 1) { echo 'checked'; } ?>>
                    </div>
                    <lable>С архивными</lable>
                </div>
                
                <!-- кнопка, активирующая фильтры на странице и поиск -->
                <button onclick="applyInOrders()" class="btn btn-ok d-iblock">Применить</button>
                <!-- кнопка сброса фильтров -->
                <!-- <button id="btn-cancel-filters" class="btn btn-neutral border-neutral d-iblock">Сбросить</button> -->

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

        //считаем и записываем в переменную кол-во страниц и оффсет
        $totalPages = ceil(count($data) / $limit);
        $offset = ($currentPage - 1) * $limit;
?>

        <!-- таблица заказов -->
        <section class="orders">
            <table id="list-orders" data-section="admin-orders" data-limit="<?php if (isset($_GET['limit'])) {?><?=$limit?><?php } else { ?><?=$limit?><?php } ?>" <?php if (isset($_GET['page'])) { ?> data-page="<?= $_GET['page'] ?>" <?php } else if (isset($_GET['search'])) { ?> data-search="<?= $_GET['search'] ?>" <?php } ?> data-vendor-select="<?php if (isset($_GET['vendor_id'])) { echo $_GET['vendor_id']; } ?>" data-status-select="<?php if (isset($_GET['status'])) { echo $_GET['status']; } ?>" data-archive-select="<?php if(isset($_GET['archive'])) { ?><?= $_GET['archive'] ?><?php } ?>" data-city-select="<?php if (isset($_GET['city_id'])) { echo $_GET['city_id']; } ?>">

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

                    //если заданы параметры статуса, собираем в переменную
                    if (isset($_GET['status'])) {
                        $params = $params . '&status=' . $_GET['status'];
                    }

                    //если задан гет-параметр архива, собираем в переменную
                    if (isset($_GET['archive'])) {
                        $params = $params . '&archive=' . $_GET['archive'];
                    } else if (isset($_GET['archived'])) {
                        true;
                    } else {
                        $params = $params . '&archive=0';
                    }

                    //print_r($params);

                    //если еще НЕ задан гет-параметр сортировки полей таблицы по одному ключу
                    if (!isset($_GET['orderby'])) {

                        //если мы НЕ на первой странице
                        if(isset($_GET['page']) && $_GET['page'] > 1) {
                            //соберём данные для отображения в форме 
                            $dataJson = file_get_contents($nginxUrl . '/api/order-vendors/get-count-with-details.php?vendor_deleted=0&limit=' . $limit . '&offset=' . $offset . $params);
                            $data = json_decode($dataJson, true);
                            //записываем в переменную кол-во записей
                            $totalEntries = $data['count']; 
                            $data = $data['orders'];
                            if ($params) {
                                //считаем и записываем в переменную общее кол-во страниц
                                $totalPages = ceil($totalEntries / $limit);
                            }
                            //$num = $offset + 1; //переменная для отображения порядкового номера (чтобы не было пропусков, т.к. некоторые id "удалены")

                        //если мы на первой странице
                        } elseif(!isset($_GET['page']) || $_GET['page'] == 1) {
                            //и поиск не активирован
                            if (!isset($_GET['search'])) {
                                //соберём данные для отображения в форме 
                                $dataJson = file_get_contents($nginxUrl . '/api/order-vendors/get-count-with-details.php?vendor_deleted=0&limit=' . $limit . '&offset=0' . $params);
                                $data = json_decode($dataJson, true);
                                $totalEntries = $data['count']; 
                                $data = $data['orders'];
                                //$num = 1; //переменная для отображения порядкового номера (чтобы не было пропусков, т.к. некоторые id "удалены")
                            }
                        }

                        //если активирован поиск
                        if(isset($_GET['search'])) {
                            $dataJson = file_get_contents($nginxUrl . '/api/order-vendors/get-count-with-details.php?vendor_deleted=0&search=' . $_GET['search'] . $params);
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
                            //считаем и записываем в переменную общее кол-во страниц
                            $totalPages = ceil(count($data) / $limit);
                        }
                             
                    }
                        
                    //если уже имеется гет-параметр сортировки полей таблицы по одному ключу
                    elseif (isset($_GET['orderby'])) {

                        //если мы НЕ на первой странице
                        if(isset($_GET['page']) && $_GET['page'] > 1) {
                            //соберём данные для отображения в форме 
                            $dataJson = file_get_contents($nginxUrl . '/api/order-vendors/get-count-with-details.php?vendor_deleted=0&offset=' . $offset .'&limit=' . $limit . '&orderby=' . $_GET['orderby'] . $params);
                            $data = json_decode($dataJson, true);
                            $totalEntries = $data['count']; 
                            $data = $data['orders'];
                            //print_r($data);
                            //$num = $offset + 1; //переменная для отображения порядкового номера (чтобы не было пропусков, т.к. некоторые id "удалены")

                        //если мы на первой странице
                        } elseif(!isset($_GET['page']) || $_GET['page'] == 1) {
                            //и поиск не активирован
                            if (!isset($_GET['search'])) {
                                //соберём данные для отображения в форме 
                                $dataJson = file_get_contents($nginxUrl . '/api/order-vendors/get-count-with-details.php?vendor_deleted=0&limit=' . $limit . '&offset=0&orderby=' . $_GET['orderby'] . $params);
                                $data = json_decode($dataJson, true);
                                $totalEntries = $data['count']; 
                                $data = $data['orders'];
                                if ($params) {
                                    //считаем и записываем в переменную общее кол-во страниц
                                    $totalPages = ceil($totalEntries / $limit);
                                }
                                //$num = 1; //переменная для отображения порядкового номера (чтобы не было пропусков, т.к. некоторые id "удалены")
                            }
                        }

                        //если активирован поиск
                        if(isset($_GET['search'])) {
                            $dataJson = file_get_contents($nginxUrl . '/api/order-vendors/get-count-with-details.php?vendor_deleted=0&limit=all&offset=0&orderby=' . $_GET['orderby'] . '&search=' . $_GET['search'] . $params);
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
                            //считаем и записываем в переменную общее кол-во страниц
                            $totalPages = ceil(count($data) / $limit);
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
                            <tr id="pages-info" role="row" class="list-orders__row" data-pages="<?= $totalPages ?>" data-current-page="<?= $currentPage ?>" <?php if($data[$i]['archive'] == 1) { echo 'archive="1"';} ?>>
                                <td class="ta-center"><a href="vendor-order.php?id=<?= $data[$i]['id'] ?>&role=1"><strong><?= $data[$i]['order_id'] ?></strong></a></td>
                                <!-- конвертация юникс времени в стандартное в формате d.m.Y H:i -->
                                <td class="ta-center"><?= convertUnixToLocalTime($data[$i]['order_date']); ?></td>
                                <td class="ta-center"><?= $data[$i]['vendor_name'] ?></td>
                                <td class="ta-center"><?= $data[$i]['vendor_city'] ?></td>
                                <td class="ta-center"><a class="list-orders_status d-block status<?= $data[$i]['status'] ?>"><?= $status ?></a></td>
                                <td class="ta-center">
                                    <a href="tel:+<?= $data[$i]['customer_phone'] ?>" >
                                        <?php
                                            if( $data[$i]['customer_phone']) {echo '+' . $data[$i]['customer_phone'];}
                                        ?>
                                    </a>
                                </td>
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
            <a <?php if($currentPage > 1) { ?> href="?limit=<?= $limit ?>&page=<?= $currentPage - 1; ?><?php if(isset($_GET['orderby'])) {?>&orderby=<?= $_GET['orderby'] ?><?php } ?><?= $params ?><?php if (isset($_GET['archived'])) { echo '&archived'; } ?>"<?php } ?> class="page-switch__prev" <?php if($currentPage <= 1) { ?>  disabled <?php } ?> > 
                <svg  class="fill" viewBox="0 8 23 16" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>down</title> <path d="M11.125 16.313l7.688-7.688 3.594 3.719-11.094 11.063-11.313-11.313 3.5-3.531z"></path> </g></svg>
            </a>
            
            <span class="current-page"><?= $currentPage ?></span>
            <a <?php if($currentPage != $totalPages) { ?> href="?limit=<?= $limit ?>&page=<?= $currentPage + 1; ?><?php if(isset($_GET['orderby'])) {?>&orderby=<?= $_GET['orderby'] ?><?php } ?><?= $params ?><?php if (isset($_GET['archived'])) { echo '&archived'; } ?>"<?php } ?> class="page-switch__next"  <?php if($currentPage == $totalPages) { ?>  disabled <?php } ?> >
                <svg  class="fill" viewBox="0 8 23 16" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>down</title> <path d="M11.125 16.313l7.688-7.688 3.594 3.719-11.094 11.063-11.313-11.313 3.5-3.531z"></path> </g></svg>
            </a>
        </div>
        <div class="page-status">стр <span class="current-page"><?= $currentPage ?></span> из <span class="total-page"><?= $totalPages ?></span></div>
    </section>
    <?php } ?>

<!-- подключим футер -->
<?php include('./../components/footer.php'); ?>

