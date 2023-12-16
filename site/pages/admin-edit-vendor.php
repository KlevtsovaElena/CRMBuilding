<?php require('../handler/check-profile.php'); 
if($role !== 1) {
    setcookie('profile', '', -1, '/');
    header('Location: ' . $mainUrl . '/pages/login.php');
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
        "<script src='./../assets/js/main.js'></script>",
        "<script src='./../assets/js/imask.min.js'></script>",
        "<script src='./../assets/js/add-vendor.js'></script>"
    ];
?>

<!-- подключим хэдер -->
<?php include('./../components/header.php'); ?>  
         
<?php 
    $id = $_GET['id']; 

    $vendorJson = file_get_contents($nginxUrl . "/api/vendors/get-with-details.php?deleted=0&id=" . $id);
    $vendor = json_decode($vendorJson, true);

    $citiesJson = file_get_contents($nginxUrl . "/api/cities.php?deleted=0&is_active=1&orderby=name:asc");
    $cities = json_decode($citiesJson, true);

?>

    <p class="page-title">Редактирование поставщика</p>

        <!-- Редактирование поставщика -->
        <section class="add-vendor">
        <form class="form-add-vendor form-elements-container">

            <!-- инфа о подтверждении цен -->
            <?php if($vendor[0]['price_confirmed'] == 1) { ?>
            
                <div class="price-confirm-container" confirm-price="1">              
                    <svg onclick="changePriceConfirm()" fill="#009933" width="20px" height="20px" viewBox="0 0 32.00 32.00" enable-background="new 0 0 32 32" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" stroke="#009933" transform="rotate(0)matrix(1, 0, 0, 1, 0, 0)"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#66FF99" stroke-width="0.256"></g><g id="SVGRepo_iconCarrier"> <g id="Approved"></g> <g id="Approved_1_"> <g> <path d="M30,1H2C1.448,1,1,1.448,1,2v28c0,0.552,0.448,1,1,1h28c0.552,0,1-0.448,1-1V2C31,1.448,30.552,1,30,1z M29,29H3V3h26V29z "></path> <path d="M12.629,21.73c0.192,0.18,0.438,0.27,0.683,0.27s0.491-0.09,0.683-0.27l10.688-10c0.403-0.377,0.424-1.01,0.047-1.413 c-0.377-0.404-1.01-0.425-1.413-0.047l-10.004,9.36l-4.629-4.332c-0.402-0.377-1.035-0.356-1.413,0.047 c-0.377,0.403-0.356,1.036,0.047,1.413L12.629,21.73z"></path> </g> </g> <g id="File_Approve"></g> <g id="Folder_Approved"></g> <g id="Security_Approved"></g> <g id="Certificate_Approved"></g> <g id="User_Approved"></g> <g id="ID_Card_Approved"></g> <g id="Android_Approved"></g> <g id="Privacy_Approved"></g> <g id="Approved_2_"></g> <g id="Message_Approved"></g> <g id="Upload_Approved"></g> <g id="Download_Approved"></g> <g id="Email_Approved"></g> <g id="Data_Approved"></g> </g></svg>
                    <span class="price-confirm">Поставщик подтвердил цены</span>
                </div> 
            
            <?php } else { ?>

                <div class="price-confirm-container" confirm-price="0">              
                    <svg onclick="changePriceConfirm()" fill="#d2323d" width="20px" height="20px" viewBox="0 0 128 128" id="Layer_1" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <polygon points="82.4,40 64,58.3 45.6,40 40,45.6 58.3,64 40,82.4 45.6,88 64,69.7 82.4,88 88,82.4 69.7,64 88,45.6 "></polygon> <path d="M1,127h126V1H1V127z M9,9h110v110H9V9z"></path> </g> </g></svg>
                    <span class="price-not-confirm">Поставщик не подтвердил цены</span>
                </div>

            <?php }  ?>
            <div class="link-text">
             <a href="admin-list-products.php?vendor_id=<?= $id; ?>">Посмотреть товары поставщика</a>
            </div>
            <!-- название -->
            <div class="form-add-vendor__item">
                <p>Название</p><input type="text" id="name" name="name" value="<?= $vendor[0]['name']?>" required>
                <div class="error-info d-none"></div>
            </div>

            <!-- город -->
            <div class="form-add-vendor__item">
                <p>Город</p>
                <select id="city_id" name="city_id" value="" required>
                    <option value="<?= $vendor[0]['city_id']; ?>" selected hidden><?= $vendor[0]['city_name']; ?></option>
                    <?php 
                    foreach($cities as $city) { ?>
                            <option value="<?= $city['id']; ?>" ><?= $city['name']; ?></option>                       
                    <?php   }; ?>

                </select>
                <div class="error-info d-none"></div> 
            </div>

            <!-- комментарий -->
            <div class="form-add-vendor__item">
                <p>Комментарий</p><textarea id="comment" name="comment"><?= $vendor[0]['comment']; ?></textarea>
                <div class="error-info d-none"></div> 
            </div>

            <!-- телефон -->
            <div class="form-add-vendor__item">
                <p>Телефон</p>
                <?php 
                // если телефон в базе стандартного формата (12 цифр, первые 3 - 998), то форматируем 
                // если нет - запишем, как в базе
                    $tel = $vendor[0]['phone'];
                    $telFormat = $tel;
                    substr($tel, 0,3);
                    if ($tel && (strlen($tel) === 12) && substr($tel, 0,3) == '998') {
                        $telFormat = '+' . substr($tel, 0,3) . '-' . substr($tel, 3,2) . '-' . substr($tel, 5,3) . '-' . substr($tel, 8,2) . '-' . substr($tel, 10);
                    } 
                ?>
                <input type="tel" id="phoneOld" onclick="test()" value="<?= $telFormat; ?>" readOnly style="color: rgba(0,0,0,0.5);"/>

                
                <input type="hidden" id="phone" name="phone" class="phone-edit" data-phone="<?= $vendor[0]['phone']; ?>" value="<?= $vendor[0]['phone']; ?>" placeholder="+998-88-888-88-88" />
                <div class="error-info d-none"></div>
            </div>

            <!-- email -->
            <div class="form-add-vendor__item">
                <p>Email</p><input type="email" id="email" name="email" value="<?= $vendor[0]['email']; ?>" placeholder="example@example.com" required>
                <div class="error-info d-none"></div>
            </div>

            <!-- процент -->
            <div class="form-add-vendor__item">
                <p>Установленная % ставка</p><input type="number" id="percent" name="percent"  min="0" max="100" value="<?= $vendor[0]['percent']; ?>" placeholder="0" onchange="percentValid(this)">
                <div class="error-info d-none"></div>
            </div>

            <!-- валюта поставщика -->
            <div class="form-add-vendor__item" currency="<?= $vendor[0]['currency_dollar']; ?>">
                <p>Валюта цен</p>
                <input type="radio" id="uzs" name="currency_dollar" value="0" <?php if ($vendor[0]['currency_dollar'] == 0) {echo 'checked'; } ?>><span class="currency-title"> Цена в сумах</span>
                <input type="radio" id="usd" name="currency_dollar" value="1" <?php if ($vendor[0]['currency_dollar'] == 1) {echo 'checked'; } ?>  onclick="checkCurrency()"><span class="currency-title"> Цена в долларах</span>
                <?php if ($vendor[0]['currency_dollar'] == 1) {echo "<span>&nbsp  &nbsp (1 $ = " . $vendor[0]['rate'] . " Сум)</span>" ;} ?>
                <div class="error-info d-none"></div>
            </div>


            <!-- статус -->
            <div class="form-add-vendor__item">
                <p>Статус</p>
                <select id="is_active" name="is_active" value="" required>
                    <option value="1" <?php if($vendor[0]['is_active'] == 1) {echo 'selected';} ?>>Активен</option>
                    <option value="0" <?php if($vendor[0]['is_active'] == 0) {echo 'selected';} ?>>Не активен</option>
                </select>
                <div class="error-info d-none"></div>
            </div> 

            <!-- координаты -->
            <div class="form-add-vendor__item">
                <p>Координаты: 
                    <?php 
                    if(isset($vendor[0]['coordinates']['latitude']) && isset($vendor[0]['coordinates']['longitude']))  {
                        echo $vendor[0]['coordinates']['latitude'] . ', ' . $vendor[0]['coordinates']['longitude']; 
                    } else {
                        echo "не указаны";
                    }?></p>
            </div> 

            <!-- пароль -->
            <div class="form-add-vendor__item">
                <p>Пароль: <?= $vendor[0]['password']; ?></p>
            </div> 

        </form>
        <div class="btn-group-3">
            <div>
                <button class="btn btn-ok" onclick="editVendor(<?= $id; ?>)">Сохранить</button>
                <a href="admin-edit-vendor.php?id=<?= $id; ?>" class="btn btn-neutral">Сбросить изменения</a> 
            </div>
        
            <div class="btn btn-delete" onclick="deleteVendorFromEditForm(<?= $id; ?>)">Удалить</div>
        </div>
 


        <div class="vendor-info">

            <p>Логин и пароль поставщика <b> <?= $vendor[0]['name']; ?> </b> <br> Скопируйте и отправьте пользователю:</p>
            <br>
            <p><b>Вход в CRM</b></p>
            <div class="vendor-info-text d-flex">
                <div class="copy-text">
                    <p><i>Логин: <?= $vendor[0]['email']; ?> &nbsp&nbsp</i></p>
                    <p><i>Пароль: <?= $vendor[0]['password']; ?></i></p> 
                </div>
                <button class="copy-result btn btn-ok" onclick="copyText()">Copy</button>                             
            </div>
            <br>
            <p><b>Ссылка для бота:</b></p>
            <div class="vendor-info-text">
                <span class="copy-text">https://t.me/str0y_bot?start=provider_<?= $vendor[0]['hash_string']; ?></span>
                <button class="copy-result btn btn-ok" onclick="copyText()">Copy</button>
            </div>
        </div>
    </section>   
 
    <!-- шаблон цены подтверждены -->
    <template id="tmpl-price-confirm">
        <svg onclick="changePriceConfirm()" fill="#009933" width="20px" height="20px" viewBox="0 0 32.00 32.00" enable-background="new 0 0 32 32" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" stroke="#009933" transform="rotate(0)matrix(1, 0, 0, 1, 0, 0)"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#66FF99" stroke-width="0.256"></g><g id="SVGRepo_iconCarrier"> <g id="Approved"></g> <g id="Approved_1_"> <g> <path d="M30,1H2C1.448,1,1,1.448,1,2v28c0,0.552,0.448,1,1,1h28c0.552,0,1-0.448,1-1V2C31,1.448,30.552,1,30,1z M29,29H3V3h26V29z "></path> <path d="M12.629,21.73c0.192,0.18,0.438,0.27,0.683,0.27s0.491-0.09,0.683-0.27l10.688-10c0.403-0.377,0.424-1.01,0.047-1.413 c-0.377-0.404-1.01-0.425-1.413-0.047l-10.004,9.36l-4.629-4.332c-0.402-0.377-1.035-0.356-1.413,0.047 c-0.377,0.403-0.356,1.036,0.047,1.413L12.629,21.73z"></path> </g> </g> <g id="File_Approve"></g> <g id="Folder_Approved"></g> <g id="Security_Approved"></g> <g id="Certificate_Approved"></g> <g id="User_Approved"></g> <g id="ID_Card_Approved"></g> <g id="Android_Approved"></g> <g id="Privacy_Approved"></g> <g id="Approved_2_"></g> <g id="Message_Approved"></g> <g id="Upload_Approved"></g> <g id="Download_Approved"></g> <g id="Email_Approved"></g> <g id="Data_Approved"></g> </g></svg>
        <span class="price-confirm">Поставщик подтвердил цены</span>
    </template>

    <!-- шаблон цены не подтверждены -->
    <template id="tmpl-price-not-confirm">
        <svg onclick="changePriceConfirm()" fill="#d2323d" width="20px" height="20px" viewBox="0 0 128 128" id="Layer_1" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <polygon points="82.4,40 64,58.3 45.6,40 40,45.6 58.3,64 40,82.4 45.6,88 64,69.7 82.4,88 88,82.4 69.7,64 88,45.6 "></polygon> <path d="M1,127h126V1H1V127z M9,9h110v110H9V9z"></path> </g> </g></svg>
        <span class="price-not-confirm">Поставщик не подтвердил цены</span>
    </template>

<!-- подключим футер -->
<?php include('./../components/footer.php'); ?>
