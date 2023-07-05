<?php 
    // собираем массив из подключаемых файлов css и js
    $styleSrc = [
        "<link rel='stylesheet' href='./../assets/css/base.css'>",
    ];
    $scriptsSrc = [
        "<script src='./../assets/js/main.js'></script>",
    ];
?>



<?php

    include('components/header.php');

?>
<?php if (!isset($_GET['role'])) {
?>
    <div class="form-elements-container form-index">
        <p class="form-index-p">Войти как</p>
        <a href="javascript: setRole(1)" class="btn btn-ok form-index-btn">Администратор</a>
        <a href="javascript: setRole(2)" class="btn btn-ok form-index-btn">Поставщик</a>
    </div>


    <style>
        .form-index-p {
            font-size: 24px;
            text-align: center;
        }
        .form-index-btn {
            margin: 10px auto;
            width: 200px;
            text-align: center;
        }

        .form-index {
            width: 600px;
            height: 400px;
            padding: 50px;
            display: flex;
            justify-content: center;
            flex-direction: column;
        }
    </style>

    <script>
        // сбрасываем куки, если нет гет параметров
        if (!window.location.search) {
            document.cookie = "role=''; max-age=-1";
            console.log('параметров нет');
        }
        function setRole(role) {
            document.cookie = "role=" + role + "; max-age=86400";
            document.location.href = "http://localhost/index.php?role=" + role; 
        }

    </script>



<?php }
?>
<?php
    //  echo "<h1>Это страница входа в CRM. Здесь будет проверка залогинен товарищ или нет, и соответственно отрисован либо кабинет, либо форма авторизации</h1>
    //  <h1>И здесь же будет прописана Домашняя страница кабинета. </h1>";
    include('components/footer.php');

?>
