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
        "<link rel='stylesheet' href='./../assets/css/base.css'>"
    ];
    $scriptsSrc = [
        "<script src='./../assets/js/main.js'></script>"
    ];
?>
<?php include('./../components/header.php'); ?>

    <p class="page-title">Мои данные</p>

    <p><b>Ссылка для бота локального:</b></p>
    <div class="vendor-info-text">
        <span class="copy-text"><?php echo 'https://t.me/Uzstroibot?start=provider_' . $vendor_hash_string;?></span>
        <button class="copy-result btn btn-ok" onclick="copyText()">Copy</button>
    </div>
<br>
<br>
<br>

    <p><b>Ссылка для бота сервер:</b></p>
    <div class="vendor-info-text">
        <span class="copy-text"><?php echo 'https://t.me/str0y_bot?start=provider_' . $vendor_hash_string;?></span>
        <button class="copy-result btn btn-ok" onclick="copyText()">Copy</button>
    </div>
<!-- ЗДЕСЬ БУДЕТ ОСНОВНОЙ КОД СТРАНИЦЫ -->

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

<?php include('./../components/footer.php'); ?>