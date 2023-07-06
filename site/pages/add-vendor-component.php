<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRMBuilding</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Open+Sans:wght@400;500;600;700&family=Raleway:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel='stylesheet' href='./../assets/css/base.css'>
    <link rel='stylesheet' href='./../assets/css/base-temp.css'>
</head>
<body>


<!-- ---------------------------------------------------------------------------------------------------------------- -->
<!-- НАЧАЛО - ПЕРЕНЕСТИ ВЕСЬ СЛЕДУЮЩИЙ КОД НА СТРАНИЦУ КАТАЛОГА ПОСТАВЩИКОВ -->
<button class="btn btn-ok d-iblock" onclick="addVendorToggle()">+ Добавить поставщика</button>

<!-- Добавление поставщика -->
<section class="add-vendor d-none">

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
                $citiesJson = file_get_contents("http://nginx/api/cities.php");
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

    </form>
<div class="vendor-info"></div>
</section>

<!-- КОНЕЦ - ПЕРЕНЕСТИ ВЕСЬ КОД НА СТРАНИЦУ КАТАЛОГА ПОСТАВЩИКОВ -->
<!-- ---------------------------------------------------------------------------------------------------------------- -->


<script src='./../assets/js/main.js'></script>
    
</body>
</html>

<script>

console.log('подключили add-vendor.js');
// определим основные переменные
const formAddVendor = document.querySelector('.form-add-vendor');
const vendorInfo = document.querySelector('.vendor-info');

// запишем значения полей формы в переменные
const nameVendor = formAddVendor.querySelector('#name');
const cityId = formAddVendor.querySelector('#city_id');
const comment = formAddVendor.querySelector('#comment');
const phone = formAddVendor.querySelector('#phone');
const email = formAddVendor.querySelector('#email');
const is_active = formAddVendor.querySelector('#is_active');

function addVendor() {
    //предотвратить дефолтные действия, отмена отправки формы (чтобы страница не перезагружалась)
    event.preventDefault(); 

    // очистим контенеры для вывода информации
    vendorInfo.innerHTML = "";

    hasError = validationAddVendor();

    // если были ошибки, то выходим
    console.log(hasError);
    if (hasError) {
        return;
    }

    // соберём json для передачи на сервер
    let obj = JSON.stringify({
        'name': nameVendor.value.trim(),
        'city_id': cityId.value,
        'comment': comment.value.trim(),
        'phone': phone.value,
        'email': email.value.trim(),
        'is_active': is_active.value,
        'role': 2 // соответствует роли поставщика
    });

    // передаём данные на сервер
    let responseJson = sendRequestPOST('http://localhost/api/vendors.php', obj);
    let response;
    // получаем ответ с сервера
    if (responseJson) {
       response = JSON.parse(responseJson);
    } else {
        return;
    }


    // показать ссылку на бота, логин и временный пароль
    vendorInfo.innerHTML = `<p>Поставщик <b> ${nameVendor.value} </b> создан! Скопируйте и отправьте пользователю:</p>
                            <br>
                            <p><b>Ссылка для бота:</b></p>
                            <div class="link-bot">${response['linkBot']}</div>
                            <br>
                            <p><b>Вход в CRM</b></p>
                            <div class="vendor-data-temp">
                                <p>Логин: ${response['login']}</p>
                                <p>Временный пароль: ${response['tempPass']}</p>
                            </div>`

    formAddVendor.reset();
}

function validationAddVendor() {
    hasError = false;

    [nameVendor, cityId, email, is_active].forEach(item => {
    
        console.log(item.getAttribute('name') + "    " + item.value);

        const errorInfoContainer = item.closest('.form-add-vendor__item').querySelector('.error-info');
        
        if (!(item.value.trim())) {
            // пустое поле
            item.classList.add('error');   
            errorInfoContainer.innerText = "Заполните данные!";
            errorInfoContainer.classList.remove('d-none');
            hasError = true;                
        } else if (item.id === "email") {
            // если поле email, то проверяем с пом регулярных выражений
            if (!emailValidation(item.value.trim())) {
                item.classList.add('error');   
                errorInfoContainer.innerText = "Неверный формат email!";
                errorInfoContainer.classList.remove('d-none');
                hasError = true; 
            } else {
                item.classList.remove('error');
                errorInfoContainer.innerText = "";
                errorInfoContainer.classList.add('d-none'); 
            }
        } else {
            item.classList.remove('error');
            errorInfoContainer.innerText = "";
            errorInfoContainer.classList.add('d-none');
        }
    
    })

    return hasError;

}

function addVendorToggle() {
    document.querySelector('.add-vendor').classList.toggle('d-none');
}

function emailValidation(emailValue) {
    const EMAIL_REGEXP = /^(([^<>()[\].,;:\s@"]+(\.[^<>()[\].,;:\s@"]+)*)|(".+"))@(([^<>()[\].,;:\s@"]+\.)+[^<>()[\].,;:\s@"]{2,})$/iu;
    console.log("emailValue " + emailValue + "   " + EMAIL_REGEXP.test(emailValue)); 
    return EMAIL_REGEXP.test(emailValue);
}


</script>
