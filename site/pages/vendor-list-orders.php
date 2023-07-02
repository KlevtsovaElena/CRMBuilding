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
                
    <p class="page-title">Заказы</p>

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

                <div class="form-elements-container">
                    <!-- поле поиска -->
                    <input type="search" id="search" name="search" value="" placeholder="Поиск по №заказа">
                    <!-- выбор кол-во записей на листе -->
                    <div class="d-iblock">Показывать по
                        <select id="limit" name="limit" value="" required>

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
                            <th data-id="status" data-sort="">Статус</th>
                            <th data-id="order_date" data-sort="">Дата создания</th>
                            <th data-id="products">Товары</th>
                            <th data-id="total_price">Сумма</th>
                            <th data-id="complete_date">Дата завершения</th>

                        </tr>
                    </thead>

                    <tbody class="list-orders__body">
                    <!-- контент таблицы из шаблона -->
                    </tbody>

                    <tbody class="list-orders__body2">
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

                <div class="form-elements-container">
                    <!-- поле поиска -->
                    <input type="search" id="search" name="search" value="<?= $searchText; ?>" placeholder="Поиск по №заказа">
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
                            <th data-id="status" data-sort="<?php if ($sortBy == 'status')  {echo $mark; } ?>">Статус</th>
                            <th data-id="order_date" data-sort="<?php if ($sortBy == 'order_date')  {echo $mark; } ?>">Дата создания</th>
                            <th data-id="products">Товары</th>
                            <th data-id="total_price">Сумма</th>
                            <th data-id="complete_date">Дата завершения</th>

                        </tr>
                    </thead>

                    <tbody class="list-orders__body">
                    <!-- контент таблицы из шаблона -->
                    </tbody>

                    <tbody class="list-orders__body2">
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

                    <tr role="row" class="list-orders__row row-status${status}" order-id="${id}">

                        <td><a href="javascript: showOrder(${id})"><strong>${order_id}</strong></a></td>
                        <td><a href="javascript: showOrder(${id})" class="list-orders_status d-block status${status}">${status}</a></td>
                        <td>${order_date}</td>
                        <td class="list-orders_products">${products}</td>
                        <td>${total_price}</td>
                        <td>${complete_date}</td>

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