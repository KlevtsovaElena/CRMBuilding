<?php 
    // собираем массив из подключаемых файлов css и js
    $styleSrc = [
        "<link rel='stylesheet' href='./../assets/css/base.css'>",
        "<link rel='stylesheet' href='./../assets/css/list-products.css'>",
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

    <!-- таблица заказов -->
    <section class="orders">
        <table id="list-orders">

            <thead>
                <tr role="row">

                    <th data-id="order_id" data-sort="">№ заказа</th>
                    <th data-id="status" data-sort="">Статус</th>
                    <th data-id="order_date" data-sort="">Дата создания</th>
                    <th data-id="products">Товары</th>
                    <th >Сумма</th>
                    <th data-id="" data-sort="">Дата выполнения</th>
                    
                </tr>
            </thead>

            <tbody class="list-orders__body">
                <!-- контент таблицы из шаблона -->
            </tbody>

        </table>
        <div class="info-table"></div>
    </section>

    <section class="pagination-wrapper"><!-- пагинация --></section>

    <!-- ШАБЛОНЫ -->
    <!-- шаблон таблицы -->
    <template id="template-body-table">
        
        <tr role="row" class="list-orders__row" order-id="${id}">
            <td><a href="vendor-new-order.php?order_id=${order_id}"><strong>${order_id}</strong></a></td>
            <td class="list-orders_status">${status}</td>
            <td>${order_date}</td>
            <td>${products}</td>
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