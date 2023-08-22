<?php if (!isset($profile)) {
    //  если профиля нет, то проверку не прошли
    header('Location: ' . $mainUrl . '/pages/login.php');
} ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRMBuilding</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Open+Sans:wght@400;500;600;700&family=Raleway:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <?php
        for ($i = 0; $i < count($styleSrc); $i++) {
            echo $styleSrc[$i];
        }; 
    ?>
</head>
<body>


<!-- Проверяем роль -->

<?php if ($role == 1) { ?>

    <!-- role=1 -->
    <!-- МЕНЮ АДМИНИСТРАТОРА -->
    <header>

        <div class="menu-top">
            <div class="menu-top__container">
                <div class="menu-top__logo">StroiCRM</div>
                <div class="menu-top__profile">
                    <div class="menu-top__profile-name">        
                        <?= $vendor_name; ?>
                    </div>
                    <div class="menu-top__profile-avatar"><?= mb_substr($vendor_name, 0, 1); ?></div>
                    <div class="logout" onclick="logOut()">
                        
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/>
                            <g id="SVGRepo_iconCarrier"> <path d="M15 16.5V19C15 20.1046 14.1046 21 13 21H6C4.89543 21 4 20.1046 4 19V5C4 3.89543 4.89543 3 6 3H13C14.1046 3 15 3.89543 15 5V8.0625M11 12H21M21 12L18.5 9.5M21 12L18.5 14.5"  stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/> </g>
                        </svg>
                    </div>
                    
                </div>
            </div>
        </div>

        <nav class="menu-left" >

            <ul class="menu-left__items">
                <li class="menu-left__item" data-page-name = "/pages/admin-main.php">
                    <a href="./../pages/admin-main.php" class="menu-left__item-link">
                        <svg fill="#ced4da" class="fill" viewBox="0 2 34 34" version="1.1" preserveAspectRatio="xMidYMid meet" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" stroke-width="0.00036">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/>
                            <g id="SVGRepo_iconCarrier"> <title>house-solid</title> <path class="clr-i-solid clr-i-solid-path-1" d="M33,19a1,1,0,0,1-.71-.29L18,4.41,3.71,18.71a1,1,0,0,1-1.41-1.41l15-15a1,1,0,0,1,1.41,0l15,15A1,1,0,0,1,33,19Z"/>
                            <path class="clr-i-solid clr-i-solid-path-2" d="M18,7.79,6,19.83V32a2,2,0,0,0,2,2h7V24h6V34h7a2,2,0,0,0,2-2V19.76Z"/> <rect x="0" y="0" width="36" height="36" fill-opacity="0"/> </g> 
                        </svg>
                        <span>Главная</span>
                    </a>
                </li>
                <li class="menu-left__item"   data-page-name = "/pages/admin-orders.php">
                    <a href="./../pages/admin-orders.php" class="menu-left__item-link">
                        <svg class="fill" viewBox="0 0 1024 1024" fill="#ced4da" class="icon" version="1.1" xmlns="http://www.w3.org/2000/svg">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                            <g id="SVGRepo_iconCarrier">
                                <path d="M300 462.4h424.8v48H300v-48zM300 673.6H560v48H300v-48z" fill=""></path><path d="M818.4 981.6H205.6c-12.8 0-24.8-2.4-36.8-7.2-11.2-4.8-21.6-11.2-29.6-20-8.8-8.8-15.2-18.4-20-29.6-4.8-12-7.2-24-7.2-36.8V250.4c0-12.8 2.4-24.8 7.2-36.8 4.8-11.2 11.2-21.6 20-29.6 8.8-8.8 18.4-15.2 29.6-20 12-4.8 24-7.2 36.8-7.2h92.8v47.2H205.6c-25.6 0-47.2 20.8-47.2 47.2v637.6c0 25.6 20.8 47.2 47.2 47.2h612c25.6 0 47.2-20.8 47.2-47.2V250.4c0-25.6-20.8-47.2-47.2-47.2H725.6v-47.2h92.8c12.8 0 24.8 2.4 36.8 7.2 11.2 4.8 21.6 11.2 29.6 20 8.8 8.8 15.2 18.4 20 29.6 4.8 12 7.2 24 7.2 36.8v637.6c0 12.8-2.4 24.8-7.2 36.8-4.8 11.2-11.2 21.6-20 29.6-8.8 8.8-18.4 15.2-29.6 20-12 5.6-24 8-36.8 8z" fill=""></path><path d="M747.2 297.6H276.8V144c0-32.8 26.4-59.2 59.2-59.2h60.8c21.6-43.2 66.4-71.2 116-71.2 49.6 0 94.4 28 116 71.2h60.8c32.8 0 59.2 26.4 59.2 59.2l-1.6 153.6z m-423.2-47.2h376.8V144c0-6.4-5.6-12-12-12H595.2l-5.6-16c-11.2-32.8-42.4-55.2-77.6-55.2-35.2 0-66.4 22.4-77.6 55.2l-5.6 16H335.2c-6.4 0-12 5.6-12 12v106.4z" fill=""></path>
                            </g>
                        </svg>
                        <?php 
                            //собираем данные по заказам поставщиков
                            $dataJson = file_get_contents($nginxUrl . "/api/order-vendors/get-count.php?status=0");
                            $numNew = json_decode($dataJson, true);
                            $numNew = $numNew['count'];
                        ?>
                        <span class="orders-with-counter">Заказы<div id="counter" class="counter <?php if ($numNew <= 0) {?> <?= 'd-none' ?> <?php } ?>"><?= $numNew ?></div></span>
                    </a>
                </li>
                <li class="menu-left__item"   data-page-name = "/pages/admin-vendors.php">
                    <a href="./../pages/admin-vendors.php" class="menu-left__item-link">
                        <svg fill="#ced4da" class="fill" viewBox="0 0 34 34" version="1.1" xmlns="http://www.w3.org/2000/svg" stroke="#ced4da">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/>
                            <g id="SVGRepo_iconCarrier"> <title>list</title> <path d="M0 26.016v-20q0-2.496 1.76-4.256t4.256-1.76h20q2.464 0 4.224 1.76t1.76 4.256v20q0 2.496-1.76 4.224t-4.224 1.76h-20q-2.496 0-4.256-1.76t-1.76-4.224zM4 26.016q0 0.832 0.576 1.408t1.44 0.576h20q0.8 0 1.408-0.576t0.576-1.408v-20q0-0.832-0.576-1.408t-1.408-0.608h-20q-0.832 0-1.44 0.608t-0.576 1.408v20zM8 24v-4h4v4h-4zM8 18.016v-4h4v4h-4zM8 12v-4h4v4h-4zM14.016 24v-4h9.984v4h-9.984zM14.016 18.016v-4h9.984v4h-9.984zM14.016 12v-4h9.984v4h-9.984z"/> </g>
                        </svg>
                        <span>Поставщики</span>
                    </a>
                </li>
                <li class="menu-left__item"   data-page-name = "/pages/admin-list-products.php">
                    <a href="./../pages/admin-list-products.php" class="menu-left__item-link">
                        <svg fill="#ced4da" class="fill" viewBox="0 0 34 34" version="1.1" xmlns="http://www.w3.org/2000/svg" stroke="#ced4da">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/>
                            <g id="SVGRepo_iconCarrier"> <title>list</title> <path d="M0 26.016v-20q0-2.496 1.76-4.256t4.256-1.76h20q2.464 0 4.224 1.76t1.76 4.256v20q0 2.496-1.76 4.224t-4.224 1.76h-20q-2.496 0-4.256-1.76t-1.76-4.224zM4 26.016q0 0.832 0.576 1.408t1.44 0.576h20q0.8 0 1.408-0.576t0.576-1.408v-20q0-0.832-0.576-1.408t-1.408-0.608h-20q-0.832 0-1.44 0.608t-0.576 1.408v20zM8 24v-4h4v4h-4zM8 18.016v-4h4v4h-4zM8 12v-4h4v4h-4zM14.016 24v-4h9.984v4h-9.984zM14.016 18.016v-4h9.984v4h-9.984zM14.016 12v-4h9.984v4h-9.984z"/> </g>
                        </svg>
                        <span>Товары</span>
                    </a>
                </li>
                <li class="menu-left__item"   data-page-name = "/pages/admin.php?section=cities">
                    <a href="./../pages/admin.php?section=cities" class="menu-left__item-link">
                        <svg fill="#ced4da" class="fill" viewBox="0 0 34 34" version="1.1" xmlns="http://www.w3.org/2000/svg" stroke="#ced4da">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/>
                            <g id="SVGRepo_iconCarrier"> <title>list</title> <path d="M0 26.016v-20q0-2.496 1.76-4.256t4.256-1.76h20q2.464 0 4.224 1.76t1.76 4.256v20q0 2.496-1.76 4.224t-4.224 1.76h-20q-2.496 0-4.256-1.76t-1.76-4.224zM4 26.016q0 0.832 0.576 1.408t1.44 0.576h20q0.8 0 1.408-0.576t0.576-1.408v-20q0-0.832-0.576-1.408t-1.408-0.608h-20q-0.832 0-1.44 0.608t-0.576 1.408v20zM8 24v-4h4v4h-4zM8 18.016v-4h4v4h-4zM8 12v-4h4v4h-4zM14.016 24v-4h9.984v4h-9.984zM14.016 18.016v-4h9.984v4h-9.984zM14.016 12v-4h9.984v4h-9.984z"/> </g>
                        </svg>
                        <span>Города</span>
                    </a>
                </li>
                <li class="menu-left__item"  data-page-name = "/pages/admin.php?section=categories">
                    <a href="./../pages/admin.php?section=categories" class="menu-left__item-link">
                        <svg fill="#ced4da" class="fill" viewBox="0 0 34 34" version="1.1" xmlns="http://www.w3.org/2000/svg" stroke="#ced4da">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/>
                            <g id="SVGRepo_iconCarrier"> <title>list</title> <path d="M0 26.016v-20q0-2.496 1.76-4.256t4.256-1.76h20q2.464 0 4.224 1.76t1.76 4.256v20q0 2.496-1.76 4.224t-4.224 1.76h-20q-2.496 0-4.256-1.76t-1.76-4.224zM4 26.016q0 0.832 0.576 1.408t1.44 0.576h20q0.8 0 1.408-0.576t0.576-1.408v-20q0-0.832-0.576-1.408t-1.408-0.608h-20q-0.832 0-1.44 0.608t-0.576 1.408v20zM8 24v-4h4v4h-4zM8 18.016v-4h4v4h-4zM8 12v-4h4v4h-4zM14.016 24v-4h9.984v4h-9.984zM14.016 18.016v-4h9.984v4h-9.984zM14.016 12v-4h9.984v4h-9.984z"/> </g>
                        </svg>
                        <span>Категории</span>
                    </a>
                </li>
                <li class="menu-left__item"   data-page-name = "/pages/admin.php?section=brands">
                    <a href="./../pages/admin.php?section=brands" class="menu-left__item-link">
                        <svg fill="#ced4da" class="fill" viewBox="0 0 34 34" version="1.1" xmlns="http://www.w3.org/2000/svg" stroke="#ced4da">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/>
                            <g id="SVGRepo_iconCarrier"> <title>list</title> <path d="M0 26.016v-20q0-2.496 1.76-4.256t4.256-1.76h20q2.464 0 4.224 1.76t1.76 4.256v20q0 2.496-1.76 4.224t-4.224 1.76h-20q-2.496 0-4.256-1.76t-1.76-4.224zM4 26.016q0 0.832 0.576 1.408t1.44 0.576h20q0.8 0 1.408-0.576t0.576-1.408v-20q0-0.832-0.576-1.408t-1.408-0.608h-20q-0.832 0-1.44 0.608t-0.576 1.408v20zM8 24v-4h4v4h-4zM8 18.016v-4h4v4h-4zM8 12v-4h4v4h-4zM14.016 24v-4h9.984v4h-9.984zM14.016 18.016v-4h9.984v4h-9.984zM14.016 12v-4h9.984v4h-9.984z"/> </g>
                        </svg>
                        <span>Бренды</span>
                    </a>
                </li>

            </ul>

            <button class="menu-left__collapse" onclick="toggleMenu()">
                <svg  class="fill" viewBox="0 8 23 16" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>down</title> <path d="M11.125 16.313l7.688-7.688 3.594 3.719-11.094 11.063-11.313-11.313 3.5-3.531z"></path> </g></svg>
                <span>Свернуть меню</span> 
            </button>
        </nav>

    </header>

<?php } else if($role == 2 ) { ?>

    <!-- role=2 -->
    <!-- МЕНЮ ПОСТАВЩИКА -->
    <header>

        <div class="menu-top">
            <div class="menu-top__container">
                <div class="menu-top__logo">StroiCRM</div>
                <div class="menu-top__profile" vendor-id="<?= $vendor_id ?>" currency_dollar="<?= $profile['currency_dollar'] ?>">
                    <div class="menu-top__profile-name">        
                        <?= $vendor_name; ?>
                    </div>
                    <div class="menu-top__profile-avatar"><?= mb_substr($vendor_name, 0, 1); ?></div>
                    <div class="logout"  onclick="logOut()">
                        
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/>
                            <g id="SVGRepo_iconCarrier"> <path d="M15 16.5V19C15 20.1046 14.1046 21 13 21H6C4.89543 21 4 20.1046 4 19V5C4 3.89543 4.89543 3 6 3H13C14.1046 3 15 3.89543 15 5V8.0625M11 12H21M21 12L18.5 9.5M21 12L18.5 14.5"  stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/> </g>
                        </svg>
                    </div>
                    
                </div>
            </div>
        </div>

        <nav class="menu-left" >

            <ul class="menu-left__items">
                <!-- <li class="menu-left__item" data-page-name = "/index.php">
                    <a href="/index.php" class="menu-left__item-link">
                        <svg fill="#ced4da" class="fill" viewBox="0 2 34 34" version="1.1" preserveAspectRatio="xMidYMid meet" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" stroke-width="0.00036">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/>
                            <g id="SVGRepo_iconCarrier"> <title>house-solid</title> <path class="clr-i-solid clr-i-solid-path-1" d="M33,19a1,1,0,0,1-.71-.29L18,4.41,3.71,18.71a1,1,0,0,1-1.41-1.41l15-15a1,1,0,0,1,1.41,0l15,15A1,1,0,0,1,33,19Z"/>
                            <path class="clr-i-solid clr-i-solid-path-2" d="M18,7.79,6,19.83V32a2,2,0,0,0,2,2h7V24h6V34h7a2,2,0,0,0,2-2V19.76Z"/> <rect x="0" y="0" width="36" height="36" fill-opacity="0"/> </g> 
                        </svg>
                        <span id="header-main" data-vendor="">Главная</span>
                    </a>
                </li> -->
                <!-- <li class="menu-left__item" data-page-name = "/pages/vendor-add-product.php">
                    <a href="./../pages/vendor-add-product.php" class="menu-left__item-link">
                        <svg stroke="#ced4da" viewBox="2 2 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" >
                            <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/>
                            <g id="SVGRepo_iconCarrier"> <g id="Edit / Add_Plus_Square"> <path id="Vector" d="M8 12H12M12 12H16M12 12V16M12 12V8M4 16.8002V7.2002C4 6.08009 4 5.51962 4.21799 5.0918C4.40973 4.71547 4.71547 4.40973 5.0918 4.21799C5.51962 4 6.08009 4 7.2002 4H16.8002C17.9203 4 18.4801 4 18.9079 4.21799C19.2842 4.40973 19.5905 4.71547 19.7822 5.0918C20.0002 5.51962 20.0002 6.07967 20.0002 7.19978V16.7998C20.0002 17.9199 20.0002 18.48 19.7822 18.9078C19.5905 19.2841 19.2842 19.5905 18.9079 19.7822C18.4805 20 17.9215 20 16.8036 20H7.19691C6.07899 20 5.5192 20 5.0918 19.7822C4.71547 19.5905 4.40973 19.2842 4.21799 18.9079C4 18.4801 4 17.9203 4 16.8002Z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/> </g> </g>
                            </svg>
                        <span>Добавить товар</span>
                    </a>
                </li> -->
                <!-- <li class="menu-left__item" data-page-name = "/pages/vendor-finances.php">
                    <a href="./../pages/vendor-finances.php" class="menu-left__item-link">
                    <svg class="fill" viewBox="2 7 28 18" stroke="#ced4da"><g id="SVGRepo_iconCarrier"> <style type="text/css"> .st0{fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;} .st1{fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:3;} .st2{fill:none;stroke-width:2;stroke-linejoin:round;stroke-miterlimit:10;} .st3{fill:none;} </style> <rect x="3" y="8" class="st0" width="22" height="12"></rect> <path class="st0" d="M3,16c2.2,0,4,1.8,4,4v4h22V12h-4c-2.2,0-4-1.8-4-4"></path> <circle class="st0" cx="14" cy="14" r="3"></circle> <path class="st0" d="M7,8c0,2.2-1.8,4-4,4"></path> <path class="st0" d="M25,16c-2.2,0-4,1.8-4,4"></path> </g></svg>
                        <span>Финансы</span>
                    </a>
                </li> -->
                <li class="menu-left__item" data-page-name = "/pages/vendor-my-info.php">
                    <a href="./../pages/vendor-my-info.php" class="menu-left__item-link">
                        <svg class="fill" viewBox="5 5 38 38" xmlns="http://www.w3.org/2000/svg" fill="#ced4da"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M0 0h48v48H0z" fill="none"></path> <g id="Shopicon"> <path d="M4,20h4v24h32V20h4L24,4L4,20z M36,20v20H12V20v-1.277l12-9.6l12,9.6V20z"></path> <g> <path d="M29,23c0-2.761-2.239-5-5-5s-5,2.239-5,5s2.239,5,5,5S29,25.761,29,23z"></path> <path d="M24,28c-4.42,0-8,3.58-8,8h16C32,31.58,28.42,28,24,28z"></path> </g> </g> </g></svg>
                        <span id="header-main" data-vendor="">Мои данные</span>
                    </a>
                </li>
                <li class="menu-left__item"  data-page-name = "/pages/vendor-list-orders.php">
                    <a href="./../pages/vendor-list-orders.php" class="menu-left__item-link">
                        <svg class="fill" viewBox="0 0 1024 1024" fill="#ced4da" class="icon" version="1.1" xmlns="http://www.w3.org/2000/svg">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                            <g id="SVGRepo_iconCarrier">
                                <path d="M300 462.4h424.8v48H300v-48zM300 673.6H560v48H300v-48z" fill=""></path><path d="M818.4 981.6H205.6c-12.8 0-24.8-2.4-36.8-7.2-11.2-4.8-21.6-11.2-29.6-20-8.8-8.8-15.2-18.4-20-29.6-4.8-12-7.2-24-7.2-36.8V250.4c0-12.8 2.4-24.8 7.2-36.8 4.8-11.2 11.2-21.6 20-29.6 8.8-8.8 18.4-15.2 29.6-20 12-4.8 24-7.2 36.8-7.2h92.8v47.2H205.6c-25.6 0-47.2 20.8-47.2 47.2v637.6c0 25.6 20.8 47.2 47.2 47.2h612c25.6 0 47.2-20.8 47.2-47.2V250.4c0-25.6-20.8-47.2-47.2-47.2H725.6v-47.2h92.8c12.8 0 24.8 2.4 36.8 7.2 11.2 4.8 21.6 11.2 29.6 20 8.8 8.8 15.2 18.4 20 29.6 4.8 12 7.2 24 7.2 36.8v637.6c0 12.8-2.4 24.8-7.2 36.8-4.8 11.2-11.2 21.6-20 29.6-8.8 8.8-18.4 15.2-29.6 20-12 5.6-24 8-36.8 8z" fill=""></path><path d="M747.2 297.6H276.8V144c0-32.8 26.4-59.2 59.2-59.2h60.8c21.6-43.2 66.4-71.2 116-71.2 49.6 0 94.4 28 116 71.2h60.8c32.8 0 59.2 26.4 59.2 59.2l-1.6 153.6z m-423.2-47.2h376.8V144c0-6.4-5.6-12-12-12H595.2l-5.6-16c-11.2-32.8-42.4-55.2-77.6-55.2-35.2 0-66.4 22.4-77.6 55.2l-5.6 16H335.2c-6.4 0-12 5.6-12 12v106.4z" fill=""></path>
                            </g>
                        </svg>
                        <?php 

                            //собираем данные по заказам КОНКРЕТНОГО поставщика
                            $dataJson = file_get_contents($nginxUrl . "/api/order-vendors/get-count.php?status=0&vendor_id=" . $vendor_id);
                            $numNew = json_decode($dataJson, true);
                            $numNew = $numNew['count'];
                        ?>
                        <span class="orders-with-counter">Заказы<div id="counter" class="counter <?php if ($numNew <= 0) {?> <?= 'd-none' ?> <?php } ?>"><?= $numNew ?></div></span>
                    </a>
                </li>
                <li class="menu-left__item" data-page-name = "/pages/vendor-list-products.php">
                    <a href="./../pages/vendor-list-products.php" class="menu-left__item-link">
                        <svg fill="#ced4da" class="fill" viewBox="0 0 34 34" version="1.1" xmlns="http://www.w3.org/2000/svg" stroke="#ced4da">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/>
                            <g id="SVGRepo_iconCarrier"> <title>list</title> <path d="M0 26.016v-20q0-2.496 1.76-4.256t4.256-1.76h20q2.464 0 4.224 1.76t1.76 4.256v20q0 2.496-1.76 4.224t-4.224 1.76h-20q-2.496 0-4.256-1.76t-1.76-4.224zM4 26.016q0 0.832 0.576 1.408t1.44 0.576h20q0.8 0 1.408-0.576t0.576-1.408v-20q0-0.832-0.576-1.408t-1.408-0.608h-20q-0.832 0-1.44 0.608t-0.576 1.408v20zM8 24v-4h4v4h-4zM8 18.016v-4h4v4h-4zM8 12v-4h4v4h-4zM14.016 24v-4h9.984v4h-9.984zM14.016 18.016v-4h9.984v4h-9.984zM14.016 12v-4h9.984v4h-9.984z"/> </g>
                        </svg>
                        <span>Товары</span>
                    </a>
                </li>

            </ul>

            <button class="menu-left__collapse" onclick="toggleMenu()">
                <svg  class="fill" viewBox="0 8 23 16" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>down</title> <path d="M11.125 16.313l7.688-7.688 3.594 3.719-11.094 11.063-11.313-11.313 3.5-3.531z"></path> </g></svg>
                <span>Свернуть меню</span> 
            </button>
        </nav>


    </header>

<?php
    }
?>

    <section class="main-content">
        <div class="container">