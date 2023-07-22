<?php require('../handler/check-profile.php'); 
if($role !== 1) {
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

    <p class="page-title">Цены</p>

    <!-- ЗДЕСЬ БУДЕТ ОСНОВНОЙ КОД СТРАНИЦЫ -->


<?php include('./../components/footer.php'); ?>