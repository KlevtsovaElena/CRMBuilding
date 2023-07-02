<?php 
    // собираем массив из подключаемых файлов css и js
    $styleSrc = [
        "<link rel='stylesheet' href='./../assets/css/base.css'>",
        "<link rel='stylesheet' href='./../assets/css/new-order.css'>"
    ];
    $scriptsSrc = [
        "<script src='./../assets/js/main.js'></script>",
        "<script src='./../assets/js/new-order.js'></script>"
    ];
?>

<!-- подключим хэдер -->
<?php include('./../components/header.php'); ?>   

    <p class="page-title">Новый заказ</p>

    <!-- здесь храним id заказа -->
    <input type="hidden" id="order_id" name="order_id" value="<?= $_GET['id'] ?>">

    <!-- соберём данные для отображения в форме -->
    <?php
        $dataJson = file_get_contents("http://nginx/api/order-vendors/get-with-details.php");
        $infos = json_decode($dataJson, true);
        //print_r($infos);

        //отбираем данные только по нужному id
        $data = [];

        foreach ($infos as $info) {
            if ($info['id'] == $_GET['id']) {
                $data = $info;
                //print_r($data);
            }
        }

        //конвертация юникс времени в стандартное в формате d.m.Y (H:i)
        $timestamp = $data['order_date'];
        $date = date('d.m.Y (H:i)', $timestamp);
        // print_r($date);

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
    ?>

    <!-- таблица нового заказа -->
    <section class="orders" data-id= <?= $data['id'] ?> >
        <table>
            
            <thead id="new-order">
                <tr role="row">
                    <th class="table-header">
                        <div>Заказ <span>№ <?= $data['order_id'] ?></span> от <?= $date ?> </div>
                        <div class="contact-data">
                            <div><a href="tel:<?= $data['customer_phone'] ?>" class="phone"><?= $data['customer_phone'] ?></a></div>
                            <div>До клиента: <?= getDistanceBetweenPointsNew($data['vendor_location']['latitude'], $data['vendor_location']['longitude'], $data['order_location']['latitude'], $data['vendor_location']['longitude']) ?> км</div> 
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

    <!-- кнопки, на которые будет нажимать поставщик после просмотра заказа -->
    <section class="buttons">
        <!-- <button class="btn btn-ok d-iblock show-contact" onclick="showContact()">КОНТАКТНЫЕ ДАННЫЕ</button> -->
        <button class="btn btn-ok d-iblock" onclick="confirmOrder()">ПОДТВЕРДИТЬ ЗАКАЗ</button>
        <button class="btn btn-ok d-iblock" onclick="cancelOrder()">ОТМЕНИТЬ ЗАКАЗ</button>
        <button class="btn btn-ok d-iblock" onclick="customerOutOfReach()">НЕ ДОЗВОНИЛИСЬ</button>
    </section>

<!-- подключим футер -->
<?php include('./../components/footer.php'); ?>






