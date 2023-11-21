<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRMBuilding</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Open+Sans:wght@400;500;600;700&family=Raleway:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
 
    <link rel="stylesheet" href="./../assets/css/base.css">
    <link rel="stylesheet" href="./../assets/css/authorization.css">
</head>
<body>

    <div class="form-elements-container form-login">
        <p class="form-login-p">Вход в CRM</p>

        <!-- login -->
        <div class="form-login__item">
            <p>Логин</p><input type="text" class="input-login" id="email" name="email" value="" required>
            <div class="error-info d-none"></div>
        </div>

        <!-- password -->
        <div class="form-login__item">
            <p>Пароль</p><input type="password" class="input-login" id="password" name="password" value="" required>
            <div class="error-info d-none"></div>
        </div>

        <div class="info-auth error-info"></div>

        <button class="btn btn-ok form-index-btn" onclick="logIn()">Войти</a>
        
    </div>




    <script src="./../assets/js/main.js"></script>
    <script src="./../assets/js/authorization.js"></script>
</body>
</html>