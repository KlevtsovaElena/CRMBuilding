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
        "<link rel='stylesheet' href='./../assets/css/admin.css'>"
    ];
    $scriptsSrc = [
        "<script src='./../assets/js/main.js'></script>",
        "<script src='./../assets/js/admin.js'></script>"
    ];
?>

<!-- подключим хэдер -->
<?php include('./../components/header.php'); ?> 

    <?php

        //достанем актуальный телефон для отображения
        $dataJson = file_get_contents("http://nginx/api/settings.php?name=phone");
        $phone = json_decode($dataJson, true); 
        $phone = $phone[0]['value'];

    ?>

    <p class="page-title">Главная</p>

    <br>
    <div class="id-block">
        <p>Телефон для связи:</p>
        <div class="phone-block">

            <p id="phone-number" class="phone"><?= $phone ?></p>

            <button id="btn-phone" class="btn btn-ok d-iblock" onclick="changePhone()">Изменить</button>

        </div>


    </div>

<!-- подключим футер -->
<?php include('./../components/footer.php'); ?>