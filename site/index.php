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

    echo "<h1>Это страница входа в CRM. Здесь будет проверка залогинен товарищ или нет, и соответственно отрисован либо кабинет, либо форма авторизации</h1>
            <h1>И здесь же будет прописана Домашняя страница кабинета. </h1>";
    
    include('components/footer.php');
?>