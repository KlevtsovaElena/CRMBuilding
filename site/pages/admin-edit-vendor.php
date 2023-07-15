<?php 
    // собираем массив из подключаемых файлов css и js
    $styleSrc = [
        "<link rel='stylesheet' href='./../assets/css/base.css'>",
        "<link rel='stylesheet' href='./../assets/css/add-edit-vendor.css'>"
    ];
    $scriptsSrc = [
        "<script src='./../assets/js/main.js'></script>",
        "<script src='./../assets/js/add-vendor.js'></script>"
    ];
?>

<!-- подключим хэдер -->
<?php include('./../components/header.php'); ?>  
         
<?php 
    $id = $_GET['id']; 

    $vendorJson = file_get_contents("http://nginx/api/vendors.php?id=" . $id);
    $vendor = json_decode($vendorJson, true);

    $citiesJson = file_get_contents("http://nginx/api/cities.php");
    $cities = json_decode($citiesJson, true);

?>

    <p class="page-title">Редактирование поставщика</p>

        <!-- Редактирование поставщика -->
        <section class="add-vendor">
        <form class="form-add-vendor form-elements-container">

            <!-- название -->
            <div class="form-add-vendor__item">
                <p>Название</p><input type="text" id="name" name="name" value="<?= $vendor['name']?>" required>
                <div class="error-info d-none"></div>
            </div>

            <!-- город -->
            <div class="form-add-vendor__item">
                <p>Город</p>
                <select id="city_id" name="city_id" value="" required>
                    <?php 
                    foreach($cities as $city) { 
                        if ($city['id'] === $vendor['city_id']) {?>
                            <option value="<?= $city['id']; ?>" selected><?= $city['name']; ?></option>
                        <?php } else { ?>
                            <option value="<?= $city['id']; ?>" ><?= $city['name']; ?></option>
                        <?php }                         
                    }; ?>

                </select>
                <div class="error-info d-none"></div> 
            </div>

            <!-- комментарий -->
            <div class="form-add-vendor__item">
                <p>Комментарий</p><textarea id="comment" name="comment"><?= $vendor['comment']; ?></textarea>
                <div class="error-info d-none"></div> 
            </div>

            <!-- телефон -->
            <div class="form-add-vendor__item">
                <p>Телефон</p><input type="tel" id="phone" name="phone" value="<?= $vendor['phone']; ?>" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                <div class="error-info d-none"></div>
            </div>

            <!-- email -->
            <div class="form-add-vendor__item">
                <p>Email</p><input type="email" id="email" name="email" value="<?= $vendor['email']; ?>" placeholder="example@example.com" required>
                <div class="error-info d-none"></div>
            </div>

            <!-- статус -->
            <div class="form-add-vendor__item">
                <p>Статус</p>
                <select id="is_active" name="is_active" value="" required>
                    <option value="1" <?php if($vendor['is_active'] == 1) {echo 'selected';} ?>>Активен</option>
                    <option value="0" <?php if($vendor['is_active'] == 0) {echo 'selected';} ?>>Не активен</option>
                </select>
                <div class="error-info d-none"></div>
            </div> 

            <!-- координаты -->
            <div class="form-add-vendor__item">
                <p>Координаты: <?php echo $vendor['coordinates']['latitude'] . ', ' . $vendor['coordinates']['longitude']; ?></p>
            </div> 

            <!-- пароль -->
            <div class="form-add-vendor__item">
                <p>Пароль: <?= $vendor['password']; ?></p>
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

            <p>Логин и пароль поставщика <b> <?= $vendor['name']; ?> </b> Скопируйте и отправьте пользователю:</p>
            <br>
            <!-- <p><b>Ссылка для бота:</b></p>
            <div class="vendor-info-text">
                <span class="copy-text">${response['linkBot']}</span>
                <button class="copy-result btn btn-ok" onclick="copyText()">Copy</button>
            </div> -->
            <p><b>Вход в CRM</b></p>
            <div class="vendor-info-text d-flex">
                <div class="copy-text">
                    <p><i>Логин: <?= $vendor['email']; ?> &nbsp&nbsp</i></p>
                    <p><i>Пароль: <?= $vendor['password']; ?></i></p> 
                </div>
                <button class="copy-result btn btn-ok" onclick="copyText()">Copy</button>                             
            </div>
        </div>
    </section>   
 

<!-- подключим футер -->
<?php include('./../components/footer.php'); ?>
