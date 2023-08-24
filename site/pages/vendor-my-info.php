<?php require('../handler/check-profile.php'); 
if($role !== 2) {
    setcookie('profile', '', -1, '/');
    header('Location: ' . $mainUrl . '/pages/login.php');
    exit(0);
};
?>

<?php 
    // собираем массив из подключаемых файлов css и js
    $styleSrc = [
        "<link rel='stylesheet' href='./../assets/css/base.css'>",
        "<link rel='stylesheet' href='./../assets/css/vendor.css'>"
    ];
    $scriptsSrc = [
        "<script src='./../assets/js/main.js'></script>",
        "<script src='./../assets/js/vendor-main.js'></script>"
    ];
?>
<?php include('./../components/header.php'); ?>
<?php 
    $vendorDataJson = file_get_contents($nginxUrl . "/api/vendors/get-with-details.php?id=" . $vendor_id);
    $vendorData = json_decode($vendorDataJson, true);
    if(isset($vendorData[0]['coordinates']['latitude']) && isset($vendorData[0]['coordinates']['longitude'])) {
        $coordinates = $vendorData[0]['coordinates']['latitude'] . ', ' . $vendorData[0]['coordinates']['longitude'];
    } else {
        $coordinates = "не указаны";
    }
?>

    <p class="page-title">МОИ ДАННЫЕ</p>


<!-- убрать попозже -->
    <p><b>Ссылка на бота:</b></p>
    <div class="vendor-info-text">
        <?php $link_bot2 = 'https://t.me/str0y_bot?start=provider_' . $vendor_hash_string; ?>
        <a href="<?= $link_bot2; ?>" class="copy-text" target="_blank"><?= $link_bot2; ?></a>
        <button class="copy-result btn btn-ok" onclick="copyText()">Copy</button>
    </div>
    <br>
    <br>
<!-- убрать попозже -->


<section class="form-elements-container">

    <!-- инфа о подтверждении цен -->
    <?php if($vendorData[0]['price_confirmed'] == 1) { ?>
       
        <div class="price-confirm-container">              
            <svg fill="#009933" width="20px" height="20px" viewBox="0 0 32.00 32.00" enable-background="new 0 0 32 32" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" stroke="#009933" transform="rotate(0)matrix(1, 0, 0, 1, 0, 0)"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#66FF99" stroke-width="0.256"></g><g id="SVGRepo_iconCarrier"> <g id="Approved"></g> <g id="Approved_1_"> <g> <path d="M30,1H2C1.448,1,1,1.448,1,2v28c0,0.552,0.448,1,1,1h28c0.552,0,1-0.448,1-1V2C31,1.448,30.552,1,30,1z M29,29H3V3h26V29z "></path> <path d="M12.629,21.73c0.192,0.18,0.438,0.27,0.683,0.27s0.491-0.09,0.683-0.27l10.688-10c0.403-0.377,0.424-1.01,0.047-1.413 c-0.377-0.404-1.01-0.425-1.413-0.047l-10.004,9.36l-4.629-4.332c-0.402-0.377-1.035-0.356-1.413,0.047 c-0.377,0.403-0.356,1.036,0.047,1.413L12.629,21.73z"></path> </g> </g> <g id="File_Approve"></g> <g id="Folder_Approved"></g> <g id="Security_Approved"></g> <g id="Certificate_Approved"></g> <g id="User_Approved"></g> <g id="ID_Card_Approved"></g> <g id="Android_Approved"></g> <g id="Privacy_Approved"></g> <g id="Approved_2_"></g> <g id="Message_Approved"></g> <g id="Upload_Approved"></g> <g id="Download_Approved"></g> <g id="Email_Approved"></g> <g id="Data_Approved"></g> </g></svg>
            <span class="price-confirm">Все цены подтверждены</span>
        </div> 
        
    <?php } else { ?>

        <div class="price-confirm-container">              
            <svg fill="#d2323d" width="20px" height="20px" viewBox="0 0 128 128" id="Layer_1" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <polygon points="82.4,40 64,58.3 45.6,40 40,45.6 58.3,64 40,82.4 45.6,88 64,69.7 82.4,88 88,82.4 69.7,64 88,45.6 "></polygon> <path d="M1,127h126V1H1V127z M9,9h110v110H9V9z"></path> </g> </g></svg>
            <span class="price-not-confirm">Цены на рассмотрении</span>
        </div>

    <?php }  ?>

    <!-- название поставщика -->
    <p class="data-vendor-elem data-vendor-elem__name"><b> <?= $vendor_name; ?> </b></p>
    <hr class="ghost">

    <!-- логин поставщика -->
    <div class="data-vendor-elem">
        <p class="ghost data-vendor-elem__title">Логин/email:</p>
        <p class="data-vendor-elem__data"><?= $profile['email']; ?></p>
        <hr class="ghost">
    </div>

    <!-- телефон поставщика -->
    <div class="data-vendor-elem">
        <p class="ghost data-vendor-elem__title">Телефон:</p>
        <p class="data-vendor-elem__data"><?= $profile['phone']; ?></p>
        <hr class="ghost">
    </div>

    <!-- процент -->
    <div class="data-vendor-elem">
        <p class="ghost data-vendor-elem__title">Установленный процент:</p>
        <p class="data-vendor-elem__data"><?= $vendorData[0]['percent']; ?> %</p>
        <hr class="ghost">
    </div>

    <!-- город и координаты поставщика -->
    <div class="data-vendor-elem">
        <p class="ghost data-vendor-elem__title">Место нахождения:</p>
        <p class="data-vendor-elem__data"><span class="ghost data-vendor-elem__title">Город:</span> <?= $vendorData[0]['city_name']; ?></p>
        <p class="data-vendor-elem__data"><span class="ghost data-vendor-elem__title">Координаты склада:</span> <?=  $coordinates; ?></p>
        <hr class="ghost">
    </div>

    <!-- поле для установки курса доллара -->
    <!-- если цены в долларах -->
    <?php if ($profile['currency_dollar'] == "1") {?>
        <div class="data-vendor-elem">
            <div class="">
                <p class="ghost data-vendor-elem__title">Установленный курс доллара:</p>
                <span><b>1 $ = </b></span> 
                <input type="number" class="rate-dollar" id="rate" name="rate" value="<?= $profile['rate']; ?>" title="Изменить"> 
                <span><b>Cум</b></span>
            </div>
            <hr class="ghost">
        </div>
    <?php } ?>

    <!-- если долг перед админом есть, то показываем -->
    <?php if ((int)$vendorData[0]['debt'] > 0) {?>
        <div class="data-vendor-elem">
            <span class="ghost data-vendor-elem__title">Долг:</span>
            <span><b><?= number_format($vendorData[0]['debt'], 0, ',', ' '); ?></b></span><span class="ghost data-vendor-elem__data"> Сум</span>
            <hr class="ghost">
        </div>
    <?php } ?>


</section>

<?php include('./../components/footer.php'); ?>

<!-- скрипт удалить, когда не нужна будет ссылка с хэшем -->
<script>
    function copyText() {
    const copyTextEl = event.target.closest('.vendor-info-text').querySelector('.copy-text');

    const tempInput = document.createElement('input');
    tempInput.setAttribute('value', copyTextEl.innerText);

    document.body.appendChild(tempInput);

    tempInput.select();
    tempInput.setSelectionRange(0, 99999);
    document.execCommand('copy');

    document.body.removeChild(tempInput);

    const alert = document.createElement('div');
    alert.classList.add('alert');
    alert.textContent = "Скопировано";

    document.body.appendChild(alert);

    setTimeout(() => {

        document.body.removeChild(alert);

    }, 1500);

}
    </script>


