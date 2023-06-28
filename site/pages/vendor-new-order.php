<?php 
    // собираем массив из подключаемых файлов css и js
    $styleSrc = [
        "<link rel='stylesheet' href='./../assets/css/base.css'>",
        "<link rel='stylesheet' href='./../assets/css/new-order.css'>"
    ];
    $scriptsSrc = [
        "<script src='./../assets/js/main.js'></script>",
        // "<script src='./../assets/js/list-products.js'></script>",
        "<script src='./../assets/js/new-order.js'></script>"
    ];
?>

<!-- подключим хэдер -->
<?php include('./../components/header.php'); ?>   

    <p class="page-title">Новый заказ</p>
    <!-- здесь храним id поставщика -->
    <input type="hidden" id="vendor_id" name="vendor_id" value="<?= $_GET['order_id'] ?>">
    <!-- соберём данные для отображения в форме -->

    <?php
        $orderId = $_GET['order_id'];
        $timestamp = 1687282054;
        $date = date('d.m.Y (H:i)', $timestamp);
        print_r($date);

        //функция для расчета расстояния по координатам
        function getDistanceBetweenPointsNew($latitude1, $longitude1, $latitude2, $longitude2, $unit = 'kilometers') {
            $theta = $longitude1 - $longitude2; 
            $distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta))); 
            $distance = acos($distance); 
            $distance = rad2deg($distance); 
            $distance = $distance * 60 * 1.1515; 
            switch($unit) { 
                //для расчета в милях
              case 'miles': 
                break; 
                //для расчета в километрах
              case 'kilometers' : 
                $distance = $distance * 1.609344; 
            } 
            //округляем значение до целого числа
            return (round($distance,0)); 
          }

          print_r(getDistanceBetweenPointsNew(55.657107, 37.569608, 57.569608, 35.569608, $unit = 'kilometers'));
        // print_r($orderId);
        // $newOrderJson = file_get_contents("http://nginx/api/orders.php?id=" . $_GET['id'] . '"');
        // $newOrder = json_decode($newOrderJson, true);
        // print_r($newOrder);

        // $ordersJson = file_get_contents("http://nginx/api/orders.php");
        // $orders = json_decode($ordersJson, true);

        // $vendorProductsJson = file_get_contents("http://nginx/api/ordervendors.php?id=");
        // $vendorProducts = json_decode($brandsJson, true);

        // $productsJson = file_get_contents("http://nginx/api/products.php?id=");
        // $products = json_decode($brandsJson, true);

        // $categoriesJson = file_get_contents("http://nginx/api/categories.php");
        // $categories = json_decode($categoriesJson, true);
    ?>

    <!-- таблица нового заказа -->
    <section class="products">
        <table>

            <thead id="new-order">
                <!-- место для первого шаблона с шапкой таблицы -->
                
            </thead>

            <tbody class="list-products__body" id="new-order-products">
                <!-- место для шаблона со списком заказанных товаров -->
            </tbody>

            <tbody class="list-products__body" id="new-order-sum">
                <!-- место для шаблона с итогом -->
            </tbody>

        </table>
        <!-- <div class="info-table"></div> -->
    </section>

    <section class="buttons">
        <button class="btn btn-ok d-iblock" onclick="showContact()">КОНТАКТНЫЕ ДАННЫЕ</button>
        <button class="btn btn-ok d-iblock" onclick="confirmOrder()">ПОДТВЕРДИТЬ ЗАКАЗ</button>
        <button class="btn btn-ok d-iblock" onclick="cancelOrder()">ОТМЕНИТЬ ЗАКАЗ</button>
        <button class="btn btn-ok d-iblock" onclick="customerOutOfReach()">НЕ ДОЗВОНИЛИСЬ</button>
    </section>

    <!-- ШАБЛОНЫ -->
    <!-- шаблон шапки части таблицы -->
    <template id="template-new-order">
        <tr role="row">

            <th data-id="order_id" data-sort="">
                <div>Заказ № ${order_id} от ${date}</div>
                <div>${phone}</div>
                <div>До клиента: ${distance} километров</div> 
            </th>
            <th></th>
            <th></th>
            <th></th>

        </tr>
        
        <!-- <tr role="row" id="new-order-products"></tr> -->
        <!-- место шаблона с содержимым заказа -->

        
    </template>

    <!-- шаблон с содержимым заказа -->
    <template  id="template-new-order-products">
        <tr role="row" class="list-orders__row" order-id="">
            <td>${name}</td>
            <td class="list-orders_status">${quantity}</td>
            <td>${price} сум</td>
            <td>${calculated_price} сум</td>
            <!-- <td>${complete_date}</td> -->
        </tr>
    </template>

    <!-- шаблон с содержимым заказа -->
    <template  id="template-new-order-sum">
        <tr role="row" class="list-orders__row" order-id="">
            <td>Итого</td>
            <td>${total_quantity}</td>
            <td></td>
            <td></td>
            <td></td>
            <!-- <td>${complete_date}</td> -->
        </tr>
        <tr role="row" class="list-orders__row" order-id="">
            <td>Итого</td>
            <td></td>
            <td></td>
            <td>${total_sum} сум</td>
            <!-- <td>${complete_date}</td> -->
        </tr>
    </template>


<!-- подключим футер -->
<?php include('./../components/footer.php'); ?>


