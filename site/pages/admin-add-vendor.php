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
                
    <p class="page-title">Добавление поставщика</p>

    <!-- Добавление поставщика -->
    <section class="add-vendor">
        <form class="form-add-vendor form-elements-container">

            <!-- название -->
            <div class="form-add-vendor__item">
                <p>Название</p><input type="text" id="name" name="name" value="" required>
                <div class="error-info d-none"></div>
            </div>

            <!-- город -->
            <div class="form-add-vendor__item">
                <p>Город</p>
                <select id="city_id" name="city_id" value="" required>
                    <option value="" selected hidden></option>

                    <?php 
                    $citiesJson = file_get_contents("http://nginx/api/cities.php?deleted=0&is_active=1");
                    $cities = json_decode($citiesJson, true);

                    foreach($cities as $city) { ?>
                        <option value="<?= $city['id']; ?>"><?= $city['name']; ?></option>
                    <?php }; ?>

                </select>
                <div class="error-info d-none"></div> 
            </div>

            <!-- комментарий -->
            <div class="form-add-vendor__item">
                <p>Комментарий</p><textarea id="comment" name="comment"></textarea>
                <div class="error-info d-none"></div> 
            </div>

            <!-- телефон -->
            <div class="form-add-vendor__item">
                <p>Телефон</p><input type="tel" id="phone" name="phone" value="" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                <div class="error-info d-none"></div>
            </div>

            <!-- email -->
            <div class="form-add-vendor__item">
                <p>Email</p><input type="email" id="email" name="email" value="" placeholder="example@example.com" required>
                <div class="error-info d-none"></div>
            </div>

            <!-- статус -->
            <div class="form-add-vendor__item">
                <p>Статус</p>
                <select id="is_active" name="is_active" value="" required>
                    <option value="1">Активен</option>
                    <option value="0">Не активен</option>
                </select>
                <div class="error-info d-none"></div>
            </div> 

            <div>
                <button class="btn btn-ok" onclick="addVendor()">Сохранить</button>
            </div>
            <div class="vendor-info-error"></div>           
        </form>
        <div class="vendor-info"></div>
    </section>                    

<!-- подключим футер -->
<?php include('./../components/footer.php'); ?>
