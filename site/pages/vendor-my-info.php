<?php require('../handler/check-profile.php'); 
if($role !== 2) {
    setcookie('profile', '', -1, '/');
    header('Location: http://localhost/pages/login.php');
    exit(0);
};
?>

<?php 
    // собираем массив из подключаемых файлов css и js
    $styleSrc = [
        "<link rel='stylesheet' href='./../assets/css/base.css'>",
        "<link rel='stylesheet' href='./../assets/css/add-edit-vendor.css'>"
    ];
    $scriptsSrc = [
        "<script src='./../assets/js/main.js'></script>"
    ];
?>
<?php include('./../components/header.php'); ?>
<?php 
    $vendorDataJson = file_get_contents("http://nginx/api/vendors/get-with-details.php?id=" . $vendor_id);
    $vendorData = json_decode($vendorDataJson, true);
    if(isset($vendorData[0]['coordinates']['latitude']) && isset($vendorData[0]['coordinates']['longitude'])) {
        $coordinates = $vendorData[0]['coordinates']['latitude'] . ', ' . $vendorData[0]['coordinates']['longitude'];
    } else {
        $coordinates = "не указаны";
    }
?>

    <p class="page-title">МОИ ДАННЫЕ</p>


<!-- убрать попозже -->
    <p><b>Ссылка для бота локального:</b></p>
    <div class="vendor-info-text">
        <?php $link_bot = 'https://t.me/Uzstroibot?start=provider_' . $vendor_hash_string; ?>
        <a href="<?= $link_bot; ?>" class="copy-text" target="_blank"><?= $link_bot; ?></a>
        <button class="copy-result btn btn-ok" onclick="copyText()">Copy</button>
    </div>
    <br>
    <br>

    <p><b>Ссылка для бота сервер:</b></p>
    <div class="vendor-info-text">
        <?php $link_bot2 = 'https://t.me/str0y_bot?start=provider_' . $vendor_hash_string; ?>
        <a href="<?= $link_bot2; ?>" class="copy-text" target="_blank"><?= $link_bot2; ?></a>
        <button class="copy-result btn btn-ok" onclick="copyText()">Copy</button>
    </div>
    <br>
    <br>
<!-- убрать попозже -->


<!-- ЗДЕСЬ БУДЕТ ОСНОВНОЙ КОД СТРАНИЦЫ -->

<section class="form-elements-container">
    <p class="data-vendor-elem data-vendor-elem__name"><b> <?= $vendor_name; ?> </b></p>
    <hr class="ghost">

    <div class="data-vendor-elem">
        <p class="ghost data-vendor-elem__title">Логин/email:</p>
        <p class="data-vendor-elem__data"><?= $profile['email']; ?></p>
        <hr class="ghost">
    </div>

    <div class="data-vendor-elem">
        <p class="ghost data-vendor-elem__title">Телефон:</p>
        <p class="data-vendor-elem__data"><?= $profile['phone']; ?></p>
        <hr class="ghost">
    </div>

    <div class="data-vendor-elem">
        <p class="ghost data-vendor-elem__title">Установленный процент:</p>
        <p class="data-vendor-elem__data"><?= $vendorData[0]['percent']; ?> %</p>
        <hr class="ghost">
    </div>

    <div class="data-vendor-elem">
        <p class="ghost data-vendor-elem__title">Место нахождения:</p>
        <p class="data-vendor-elem__data"><span class="ghost data-vendor-elem__title">Город:</span> <?= $vendorData[0]['city_name']; ?></p>
        <p class="data-vendor-elem__data"><span class="ghost data-vendor-elem__title">Координаты склада:</span> <?=  $coordinates; ?></p>
        <hr class="ghost">
    </div>
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