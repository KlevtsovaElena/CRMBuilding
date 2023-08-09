<?php require('handler/check-profile.php'); ?>


<?php 
    // собираем массив из подключаемых файлов css и js
    $styleSrc = [
        "<link rel='stylesheet' href='./../assets/css/base.css'>",
        "<link rel='stylesheet' href='./../assets/css/vendor.css'>",
    ];
    $scriptsSrc = [
        "<script src='./../assets/js/main.js'></script>"
    ];
?>

<?php include('components/header.php'); ?>

    <p class="page-title">Главная</p>

    <!-- Главная для поставщика -->
    <?php if ($role == "2") { 
        $scriptsSrc = [
            "<script src='./../assets/js/main.js'></script>",
            "<script src='./../assets/js/vendor-main.js'></script>"
        ];
            
    ?>
        
        <!-- поле для установки курса доллара -->
        <!-- если цены в долларах -->
        <?php if ($profile['currency_dollar'] == "1") {?>
            <div class="">
                <p>Установленный курс доллара:</p>
                <span><b>1 $ = </b></span> 
                <input type="number" class="rate-dollar" id="rate" name="rate" value="<?= $profile['rate']; ?>" title="Изменить"> 
                <span><b>сум</b></span>
            </div>
        <?php } ?>
    <?php } ?> 
    




    <!-- Главная для админа -->
    <?php if ($role == "1") { ?>

        <!-- код для главной Админа -->

    <?php } ?> 




<?php include('components/footer.php'); ?>
