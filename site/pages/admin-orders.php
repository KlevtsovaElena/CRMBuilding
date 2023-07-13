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
        $dataJson = file_get_contents("http://nginx/api/order-vendors/get-with-details.php");
        $data = json_decode($dataJson, true); 
        //print_r($data);


        if(isset($_GET['orderby'])) {
            $orderByArray = explode(";", $_GET['orderby']);
            $orderBy = explode(":", $orderByArray[0]);
            $sortBy = $orderBy[0];
            $mark = $orderBy[1];
            print_r($sortBy);
            print_r($mark);
        } else {
            $sortBy = "";
        }
    ?>

        <!-- далее отрисовываем всю страницу -->
        <p class="page-title">Заказы</p>

        <section class="form-filters">
            <div class="form-elements-container">
                <!-- выбор кол-ва отображаемых записей на странице -->
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
        $totalNumElements = count($data); ?>

        <!-- таблица заказов -->
        <section class="orders">
            <table id="list-orders">

                <thead>
                    <tr role="row">

                        <th data-id="order_id" data-sort="<?php if ($sortBy == 'order_id')  {echo $mark; } ?>">№ заказа</th>
                        <th data-id="order_date" data-sort="<?php if ($sortBy == 'order_date')  {echo $mark; } ?>">Дата создания</th>
                        <th data-id="status" data-sort="<?php if ($sortBy == 'status')  {echo $mark; } ?>">Статус</th>
                        <th data-id="customer_id">Клиент ID</th>
                        <th data-id="customer_phone" data-sort="<?php if ($sortBy == 'customer_phone')  {echo $mark; } ?>">Телефон</th>
                        <th data-id="products">Товары</th>
                        <th data-id="total_price">Сумма</th>
                        <th data-id="complete_date">Дата завершения</th>

                    </tr>
                </thead>

                <tbody class="list-orders__body">
                <tr role="row" class="list-orders__row row-status${status}" order-id="${id}" archive="${archive}">

                    <?php //если мы НЕ на первой странице
                    if(isset($_GET['page']) && $_GET['page'] > 1) {
                        //отрисовываем строки в цикле от начальной до конечной цифры оффсетного значения
                        for ($i = $offset; $i < $limit + $offset; $i++) {
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
                                <td><a href="vendor-order.php?id=<?= $data[$i]['id'] ?>&role=1"><strong><?= $data[$i]['order_id'] ?></strong></a></td>
                                <td><?= $data[$i]['order_date'] ?></td>
                                <td><a class="list-orders_status d-block status<?= $data[$i]['status'] ?>"><?= $status ?></a></td>
                                <td>id</td>
                                <td><?= $data[$i]['customer_phone'] ?></td>
                                <!-- в отдельном цикле отрисовываем весь список продуктов в данном заказе -->
                                <td class="list-orders_products">
                                    <?php $totalPrice = 0;
                                    for ($p = 0; $p < count($data[$i]['products']); $p++) { ?> 
                                        <?= $data[$i]['products'][$p]['name'] ?> (<?= $data[$i]['products'][$p]['quantity'] ?>)<?php if($data[$i]['products'] > 1 && $p != (count($data[$i]['products']) - 1)) {?>, <?php } ?>
                                        <!-- считаем общую стоимость по данному заказу -->
                                        <?php $totalPrice += $data[$i]['products'][$p]['price'] * $data[$i]['products'][$p]['quantity'];
                                    } ?>
                                </td>
                                <!-- выводим общую стоимость в нужном формате -->
                                <td><?= number_format($totalPrice, 0, ',', ' '); ?> </td>
                                <td></td>
                            </tr>
                            <?php }
                        } 
                    
                    //если мы на первой странице
                    } elseif(!isset($_GET['page']) || $_GET['page'] == 1) {
                        //и поиск не активирован
                        if (!isset($_GET['search'])) {
                            //отрисовываем строки в цикле по заданному лимиту отображаемых элементов на 1 странице
                            for ($i = 0; $i < $limit; $i++) {
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
                                <td><a href="vendor-order.php?id=<?= $data[$i]['id'] ?>&role=1"><strong><?= $data[$i]['order_id'] ?></strong></a></td>
                                    <td><?= $data[$i]['order_date'] ?></td>
                                    <td><a href="vendor-order.php?id=<?= $data[$i]['id'] ?>&role=1" class="list-orders_status d-block status<?= $data[$i]['status'] ?>"><?= $status ?></a></td>
                                    <td>id</td>
                                    <td><?= $data[$i]['customer_phone'] ?></td>
                                    <!-- в отдельном цикле отрисовываем весь список продуктов в данном заказе -->
                                    <td class="list-orders_products">
                                        <?php $totalPrice = 0;
                                        for ($p = 0; $p < count($data[$i]['products']); $p++) { ?> 
                                            <?= $data[$i]['products'][$p]['name'] ?> (<?= $data[$i]['products'][$p]['quantity'] ?>)<?php if($data[$i]['products'] > 1 && $p != (count($data[$i]['products']) - 1)) {?>, <?php } ?>
                                            <!-- считаем общую стоимость по данному заказу -->
                                            <?php $totalPrice += $data[$i]['products'][$p]['price'] * $data[$i]['products'][$p]['quantity'];
                                        } ?>
                                    </td>
                                    <!-- выводим общую стоимость в нужном формате -->
                                    <td><?= number_format($totalPrice, 0, ',', ' '); ?> </td>
                                    <td></td>
                                </tr>
                            <?php }
                            } 
                        }
                    } 

                    //если активирован поиск
                    if(isset($_GET['search'])) {
                        $dataJson = file_get_contents("http://nginx/api/order-vendors/get-with-details.php?search=" . $_GET['search']);
                        $data = json_decode($dataJson, true);
                        //отрисовываем список элементов, которые совпадают с поисковым запросом
                        $totalNumElements = 0; //для подсчета общего кол-ва записей
                        if ($data) {
                            for ($i = 0; $i < count($data); $i++) { 
                                //проверка на то, чтобы выводилось не больше строк, чем есть в БД
                                if(isset($data[$i]['id'])) { 
                                    $totalNumElements++; 
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
                                        <td><a href="vendor-order.php?id=<?= $data[$i]['id'] ?>&role=1"><strong><?= $data[$i]['order_id'] ?></strong></a></td>
                                        <td><?= $data[$i]['order_date'] ?></td>
                                        <td><a class="list-orders_status d-block status<?= $data[$i]['status'] ?>"><?= $status ?></a></td>
                                        <td>id</td>
                                        <td><?= $data[$i]['customer_phone'] ?></td>
                                        <!-- в отдельном цикле отрисовываем весь список продуктов в данном заказе -->
                                        <td class="list-orders_products">
                                            <?php $totalPrice = 0;
                                            for ($p = 0; $p < count($data[$i]['products']); $p++) { ?> 
                                                <?= $data[$i]['products'][$p]['name'] ?> (<?= $data[$i]['products'][$p]['quantity'] ?>)<?php if($data[$i]['products'] > 1 && $p != (count($data[$i]['products']) - 1)) {?>, <?php } ?>
                                                <!-- считаем общую стоимость по данному заказу -->
                                                <?php $totalPrice += $data[$i]['products'][$p]['price'] * $data[$i]['products'][$p]['quantity'];
                                            } ?>
                                        </td>
                                        <!-- выводим общую стоимость в нужном формате -->                                        
                                        <td><?= number_format($totalPrice, 0, ',', ' '); ?> </td>
                                        <td></td>
                                    </tr>
                            <?php }
                            } 
                        }
                        
                    } ?>

                </tr>
            </tbody>

        </table>

        <!-- внизу таблицы выдаем общее количество записей -->
        <div class="info-table">Всего записей: <?= $totalNumElements ?></div>
    </section>

    <!-- если НЕ одна страница и НЕ задан поиск, показываем внизу пагинацию -->
    <?php if($totalPages > 1 && !isset($_GET['search'])) { ?>
    <section class="pagination-wrapper">
        <div class="page-switch">                 
            <a href="?limit=<?= $limit ?>&page=<?= $currentPage - 1; ?>" class="page-switch__prev" <?php if($currentPage <= 1) { ?>  disabled <?php } ?> > 
                <svg  class="fill" viewBox="0 8 23 16" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>down</title> <path d="M11.125 16.313l7.688-7.688 3.594 3.719-11.094 11.063-11.313-11.313 3.5-3.531z"></path> </g></svg>
            </a>
            
            <span class="current-page"><?= $currentPage ?></span>
            <a href="?limit=<?= $limit ?>&page=<?= $currentPage + 1; ?>" class="page-switch__next"  <?php if($currentPage == $totalPages) { ?>  disabled <?php } ?> >
                <svg  class="fill" viewBox="0 8 23 16" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>down</title> <path d="M11.125 16.313l7.688-7.688 3.594 3.719-11.094 11.063-11.313-11.313 3.5-3.531z"></path> </g></svg>
            </a>
        </div>
        <div class="page-status">стр <span class="current-page"><?= $currentPage ?></span> из <span class="total-page"><?= $totalPages ?></span></div>
    </section>
    <?php } ?>

<!-- подключим футер -->
<?php include('./../components/footer.php'); ?>
