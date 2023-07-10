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
    echo "<script>document.location.href = 'http://localhost/pages/vendor-list-orders.php?vendor_id=" . $vendor_id . $params ."'; </script>";

}
?>

<!-- подключим хэдер -->
<?php include('./../components/header.php'); ?>
                
<p class="page-title">ЗАКАЗЫ222222</p>

<!-- соберём данные для отображения в форме -->

<?php

    $status_table = [
        '0' => 'Новый',
        '1' => 'Просмотрен',
        '2' => 'Подтверждён',
        '3' => 'Отменён',
        '4' => 'Доставлен'
    ];

    // дефолтные значения
    $limit = 10;
    $offset = 0;
    $currentPage = 1;
    $tableInfo = "";
    $url ='http://nginx/api/order-vendors/get-count-with-details.php?vendor_id=' . $vendor_id;
    $params = "";
    $limitParams = "";
    $filters = "";
    $offsetParams = "";

?>



<!-- если параметры get пустые -->
<!-- отрисовываем страницу по дефолту -->
<?php 
        if (count($_GET) == 0)  {

            $ordersJson = file_get_contents($url . "&offset=0&limit=" . $limit . "&orderby=order_date:desc");
            $orders = json_decode($ordersJson, true);

            // сколько всего записей в базе
            $countOrders = $orders['count'];
            $tableInfo = "Всего записей: <span class='total-orders'>" . $countOrders . "</span>";

            // рассчёт кол-ва страниц
            if ($countOrders == 0) {
                $countPages = 1;
            } else {
                $countPages = ceil($countOrders/$limit);
            }

        ?>

            <!-- Выбор фильтров -->
            <section class="form-filters">

                <!-- здесь храним id поставщика -->
                <input type="hidden" id="vendor_id" name="vendor_id" value="<?= $vendor_id; ?>">
                
                <div class="form-elements-container">

                    <!-- поле поиска -->
                    <input type="search" id="search" name="search" value="" placeholder="Поиск по №заказа">
                    
                    <!-- выбор статуса -->
                    <div class="d-iblock">Статус
                        <select id="status" name="status" value="">

                            <option value="">Все</option>
                            <?php foreach($status_table as $id => $status) { ?>
                                <option value="<?= $id; ?>"><?= $status; ?></option>
                            <?php }; ?>

                        </select>
                    </div>
                    
                    <!-- выбор кол-во записей на листе -->
                    <div class="d-iblock">Показывать по
                        <select id="limit" name="limit" value="">

                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="30">30</option>
                            <option value="50">50</option>

                        </select>
                    </div>
                    <br>
                    <button class="btn btn-ok d-iblock">Применить</button>

                </div>
            </section>

            <!-- таблица заказов -->
            <section class="orders">
                <table id="list-orders">

                    <thead>
                        <tr role="row">

                            <th data-id="order_id" data-sort="">№ заказа</th>
                            <th data-id="order_date" data-sort="">Дата создания</th>
                            <th data-id="status" data-sort="">Статус</th>
                            <th data-id="customer_id">Клиент ID</th>
                            <th data-id="customer_phone" data-sort="">Телефон</th>
                            <th data-id="products">Товары</th>
                            <th data-id="total_price">Сумма</th>
                            <th data-id="complete_date">Дата завершения</th>

                        </tr>
                    </thead>

<?php 
} ?>



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

    if(isset($_GET['status'])) {
        $status = $_GET['status'];
        $filters .= "&status=" . $_GET['status'];
    } else {
        $status = "";
    }

    if(isset($_GET['orderby'])) {
        $orderByArray = explode(";", $_GET['orderby']);
        $orderBy = explode(":", $orderByArray[0]);
        $sortBy = $orderBy[0];
        $mark = $orderBy[1];

        $filters .= "&orderby=" . $_GET['orderby'];

    } else {
        $sortBy = "";
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
    $ordersJson = file_get_contents($url . $params);
    $orders = json_decode($ordersJson, true);

    // сколько всего в базе записей с такими параметрами
    $countOrders = $orders['count'];

    // рассчитаем текущую страницу
    $currentPage = ceil($offset/$limit) + 1;

    // рассчитаем сколько всего страниц
    if ($countOrders == 0) {
        $countPages = 1;
    } else {
        $countPages = ceil($countOrders/$limit);
    }

    // если в базе записи есть, а по указанному офсету выборка = 0
    if ($countOrders > 0 && count($orders['orders']) === 0) {
        changeData($countPages, $limit, $filters, $vendor_id);
    }

    $tableInfo = "Всего записей: <span class='total-orders'>" . $countOrders . "</span>";


?>

<!-- Выбор фильтров -->
<section class="form-filters">

    <!-- здесь храним id поставщика -->
    <input type="hidden" id="vendor_id" name="vendor_id" value="<?= $vendor_id; ?>">

    <div class="form-elements-container">

        <!-- поле поиска -->
        <input type="search" id="search" name="search" value="<?= $searchText; ?>" placeholder="Поиск по №заказа">
        
        <!-- выбор статуса -->
        <div class="d-iblock">Статус
            <select id="status" name="status" value="">

                <option value="">Все</option>
                <?php foreach($status_table as $id => $status) {
                    if (!isset($_GET['status'])) {
                ?>
                    
                    <?php foreach($status_table as $id => $status) { ?>
                        <option value="<?= $id; ?>"><?= $status; ?></option>
                    <?php }; ?>
                    
                <?php
                } else if ($_GET['status'] == $id) {
                ?>

                    <option value="<?= $id; ?>" selected><?= $status; ?></option>

                <?php
                } else {
                ?>

                    <option value="<?= $id; ?>"><?= $status; ?></option>

                <?php 
                    }
                }; ?> 
                
            </select>
        </div>
        <!-- выбор кол-во записей на листе -->
        <div class="d-iblock">Показывать по
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
        <br>
        <button class="btn btn-ok d-iblock">Применить</button>

    </div>
</section>

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

<?php } ?>


        <tbody class="list-orders__body">
            <?php foreach ($orders['orders'] as $order) { 
                $productsList = "";
                $totalPrice = 0;

                for ($i = 0; $i < count($order['products']); $i++) {

                    $productsList .= $order['products'][$i]['name'] . ' (' .
                                    ($order['products'][$i]['quantity']) . '), ';

                    $totalPrice += (int)$order['products'][$i]['quantity'] * (int)$order['products'][$i]['price'];
                }
                
                ?>
            
            <tr role="row" class="list-orders__row row-status<?= $order['status']; ?>" order-id="<?= $order['id']; ?>">

                <td><a href="./../pages/vendor-order.php?id=<?= $order['id']; ?>"><strong><?= $order['order_id']; ?></strong></a></td>
                <td order-date="<?= $order['order_date']; ?>"></td>
                <td><a href="./../pages/vendor-order.php?id=<?= $order['id']; ?>" class="list-orders_status d-block status<?= $order['status']; ?>"><?= $status_table[$order['status']]; ?></a></td>
                <!-- <td><?= $order['customer_id']; ?></td> -->
                <td>id</td>
                <td><?= $order['customer_phone']; ?></td>
                <td class="list-orders_products"><?= $productsList; ?></td>
                <td><?= number_format($totalPrice, 0, ',', ' '); ?></td>
                <td></td>

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