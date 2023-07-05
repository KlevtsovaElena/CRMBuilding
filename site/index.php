<?php 
    // собираем массив из подключаемых файлов css и js
    $styleSrc = [
        "<link rel='stylesheet' href='./../assets/css/base.css'>",
    ];
    $scriptsSrc = [
        "<script src='./../assets/js/main.js'></script>",
    ];
?>

<!-- 1. Проверяем есть ли в гет-параметрах роль, если нет то:
            1. меню не рисуем
            2. показываем форму выбора
            3. удаляем куки
    2. Если роль есть в гет параметре, то:
            1. подключаем хэдер
            2. в хэдере проверяем куки (1 или 2)
            3. рисуем меню поставщика или администратора
    3. При нажатии на кнопку Войти как Администратор:
            1. устанавливаем в куки роль = 1
            2. перезагружаем стр с гет-параметром роли 1
    4. При нажатии на кнопку Войти как Поставщик аналогично п.3, только с циферкой 2
      -->
<?php
if (isset($_GET['role'])) {
    include('components/header.php');
} else {
    ?>
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRMBuilding</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Open+Sans:wght@400;500;600;700&family=Raleway:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel='stylesheet' href='./../assets/css/base-temp.css'>
    
</head>
<body>
    <section class="main-content">
        <div class="container">
<?php
}


 if (!isset($_GET['role'])) {
?>
    <div class="form-elements-container form-index">
        <p class="form-index-p">Войти как</p>
        <a href="javascript: setRole(1)" class="btn btn-ok form-index-btn">Администратор</a>
        <a href="javascript: setRole(2)" class="btn btn-ok form-index-btn">Поставщик</a>
    </div>




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
