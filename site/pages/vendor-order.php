<?php require('../handler/check-profile.php'); 
// if($role !== 2) {
//     setcookie('profile', '', -1, '/');
//     header('Location: http://localhost/pages/login.php');
//     exit(0);
// };
// ?>

<?php 

    // собираем массив из подключаемых файлов css и js
    $styleSrc = [
        "<link rel='stylesheet' href='./../assets/css/base.css'>",
        "<link rel='stylesheet' href='./../assets/css/new-order.css'>"
    ];
    $scriptsSrc = [
        "<script src='./../assets/js/main.js'></script>",
        "<script src='./../assets/js/order.js'></script>"
    ];
?>

<!-- подключим хэдер -->
<?php include('./../components/header.php'); ?>   

    <!-- соберём данные для отображения в форме -->
    <?php
        $dataJson = file_get_contents("http://nginx/api/order-vendors/get-with-details.php?id=".$_GET['id']);
        $data = json_decode($dataJson, true);
        $data = $data[0];
        print_r($data);

        function convertUnixToLocalTime($unixTime) {

            //задаем дефолтный часовой пояс или достаем из куки
            $timeZone = 'UTC';
            if(isset($_COOKIE['time_zone'])) {
                $timeZone = $_COOKIE['time_zone'];
            }
            date_default_timezone_set("$timeZone");
        
            //конвертируем время в часовой пояс, указанный выше
            $localTime = date('d.m.Y (H:i)', $unixTime);
        
            return $localTime;
        }

        //функция для расчета расстояния в км по координатам
        function getDistanceBetweenPointsNew($latitude1, $longitude1, $latitude2, $longitude2) {
            $theta = $longitude1 - $longitude2; 
            $distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta))); 
            $distance = acos($distance); 
            $distance = rad2deg($distance); 
            $distance = $distance * 1.609344; 

            //округляем значение до целого числа
            return (round($distance,0)); 
          }

          //записываем в переменную локацию клиента в виде джейсона для передачи внутри функции на фронт
        //   $clientLocation = json_encode($data['order_location']);
        //   print_r($clientLocation);
    ?>

    <!-- если заказ новый, отражаем это в заголовке -->
    <?php if ($data['status'] == 0) { ?>

        <p class="page-title" data-status=" <?= $data['status'] ?> ">Новый заказ</p>

    <!-- если заказ уже открывался ранее -->
    <?php }  else {?>

        <p class="page-title" data-status=" <?= $data['status'] ?> ">Заказ</p>

    <?php } ?>



    <!-- таблица заказа -->
    <section class="orders" data-id=<?= $data['id'] ?> >
        <table>
            
            <thead id="new-order" data-role="<?= $role ?>" data-client-latitude="<?= $data['order_location']['latitude'] ?>" data-client-longitude="<?= $data['order_location']['longitude'] ?>" data-vendor-id="<?= $data['vendor_id'] ?>">
                <tr role="row">
                    <th class="table-header">
                        <div>Заказ <span>№ <?= $data['order_id'] ?></span> от <span><?= convertUnixToLocalTime($data['order_date']); ?></span></div>
                        <div class="contact-data">
                            <div><a href="tel:<?= $data['customer_phone'] ?>" class="phone"><?= $data['customer_phone'] ?></a></div>
                            <div>До клиента: <?php if(!$data['vendor_location']) { ?> <?='локация поставщика отсутствует' ?> <?php } else { ?> <?= getDistanceBetweenPointsNew($data['vendor_location']['latitude'], $data['vendor_location']['longitude'], $data['order_location']['latitude'], $data['order_location']['longitude']) ?> км <?php } ?></div> 
                        </div> 
                    </th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>             
            </thead>

            <tbody class="list-products__body" id="new-order-products">
                <?php $totalQuantity = 0; ?>
                <?php $totalSum = 0; ?>
                <?php for ($i = 0; $i < count($data['products']); $i++) {?>
                    <tr role="row" class="list-orders__row">
                        <td><?= $data['products'][$i]['name']; ?></td>
                        <td class="list-orders_status"><?= $data['products'][$i]['quantity'] ?></td>
                        <td><?= number_format($data['products'][$i]['price'], 0, ',', ' '); ?> сум</td>
                        <td><?= number_format($data['products'][$i]['price'] * $data['products'][$i]['quantity'], 0, ',', ' '); ?> сум</td>
                        <?php $totalQuantity += $data['products'][$i]['quantity']; ?>
                        <?php $totalSum += $data['products'][$i]['price'] * $data['products'][$i]['quantity']; ?>
                    </tr>
                    <?php }; ?>
                    <tr role="row" class="list-orders__row total_row">
                        <td class="total">Итого</td>
                        <td><?= $totalQuantity ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr role="row" class="list-orders__row total_row">
                        <td class="total">Итого</td>
                        <td></td>
                        <td></td>
                        <td><?= number_format($totalSum, 0, ',', ' '); ?> сум</td>
                    </tr>
            </tbody>

        </table>
    </section>

    <!-- если эту страницу открывает НЕ администратор, то видны кнопки -->
    <?php if(!isset($_GET['role']) || $_GET['role'] != 1) { ?>
    <!-- кнопки, на которые будет нажимать поставщик после просмотра заказа -->
    <section class="buttons">

        <!-- если статус заказа НЕ "завершен"-->
        <?php if($data['status'] != 4) {

            //и также НЕ "подтвержден"
            if($data['status'] != 2) {?>
                <!-- будет видна кнопка "ПОДТВЕРДИТЬ ЗАКАЗ" -->
                <button id="btn-confirm" class="btn btn-ok d-iblock" onclick="confirmOrder()">ПОДТВЕРДИТЬ ЗАКАЗ</button>
            <?php } ?>

            <!-- и также НЕ "отменен"--> 
            <?php if($data['status'] != 3) {?>
                <!-- будет видна кнопка "ОТМЕНИТЬ ЗАКАЗ" -->
                <button id="cancel-order" class="btn btn-ok d-iblock" onclick="cancelOrder()">ОТМЕНИТЬ ЗАКАЗ</button>
            <?php } ?>

        <?php } ?>

        <!-- если статус заказа либо "новый", либо "просмотрен", будет видна кнопка "НЕ ДОЗВОНИЛИСЬ" -->
        <?php if($data['status'] == 0 || $data['status'] == 1) {?>
            <button id="btn-out-of-reach" class="btn btn-ok d-iblock" onclick="customerOutOfReach()">НЕ ДОЗВОНИЛИСЬ</button>
        <?php } ?>

        <!-- если статус заказа "подтвержден", будет видна кнопка "ПОДТВЕРДИТЬ ДОСТАВКУ" -->
        <?php if ($data['status'] == 2) {?>
            <button id="btn-confirm-delivery"  class="btn btn-ok d-iblock" onclick="confirmDelivery()">ПОДТВЕРДИТЬ ДОСТАВКУ</button>
            <button id="send-location" class="btn btn-ok d-iblock" onclick="sendLocation(<?= $data['order_location']['latitude'] ?>, <?= $data['order_location']['longitude'] ?>, <?= $data['vendor_id'] ?>)">ОТПРАВИТЬ СЕБЕ КООРДИНАТЫ</button>
        <?php } ?>

    </section>
    <?php } ?>

<!-- подключим футер -->
<?php include('./../components/footer.php'); ?>

