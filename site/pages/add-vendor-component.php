<?php 
    // собираем массив из подключаемых файлов css и js
    $styleSrc = [
        "<link rel='stylesheet' href='./../assets/css/base.css'>"
    ];
    $scriptsSrc = [
        "<script src='./../assets/js/main.js'></script>",
    ];
?>

<!-- подключим хэдер -->
<?php include('./../components/header.php'); ?>  


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

<!-- <div class="vendor-info2"><p>Поставщик <b> gdgdg </b> создан! Скопируйте и отправьте пользователю:</p>
                            <br>
                            <p><b>Ссылка для бота:</b></p>
                            <div class="vendor-info-text">
                                <span class="copy-text">https://t.me/Uzstroibot?start=haqw8.QCKT2uI</span>
                                <button class="copy-result btn btn-ok" onclick="copyText()">Copy</button>
                            </div>
                            <br>
                            <p><b>Вход в CRM</b></p>
                            <div class="vendor-info-text d-flex">
                                <div class="copy-text">
                                    <p><i>Логин: jfhtgh@fgfgnn.lk &nbsp&nbsp</i></p>
                                    <p><i>Временный пароль: crhTHYW2Fqo2o</i></p> 
                                </div>
                                <button class="copy-result btn btn-ok" onclick="copyText()">Copy</button>                             
                            </div></div>
</section> -->

<!-- КОНЕЦ - ПЕРЕНЕСТИ ВЕСЬ КОД НА СТРАНИЦУ КАТАЛОГА ПОСТАВЩИКОВ -->
<!-- ---------------------------------------------------------------------------------------------------------------- -->

<!-- подключим футер -->
<?php include('./../components/footer.php'); ?>
    
</body>
</html>

<script>

console.log('подключили add-vendor.js');
// определим основные переменные
const formAddVendor = document.querySelector('.form-add-vendor');
const vendorInfo = document.querySelector('.vendor-info');
const copyBtn = document.querySelectorAll('.copy-result');

console.log(copyBtn);

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
                            <div class="vendor-info-text">
                                <span class="copy-text">${response['linkBot']}</span>
                                <button class="copy-result btn btn-ok" onclick="copyText()">Copy</button>
                            </div>
                            <br>
                            <p><b>Вход в CRM</b></p>
                            <div class="vendor-info-text d-flex">
                                <div class="copy-text">
                                    <p><i>Логин: ${response['login']} &nbsp&nbsp</i></p>
                                    <p><i>Временный пароль: ${response['tempPass']}</i></p> 
                                </div>
                                <button class="copy-result btn btn-ok" onclick="copyText()">Copy</button>                             
                            </div>`

    formAddVendor.reset();
}

function validationAddVendor() {
    hasError = false;

    [nameVendor, cityId, email, is_active].forEach(item =>  {
    
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
    vendorInfo.innerHTML = "";
    document.querySelector('.add-vendor').classList.toggle('d-none');
}

function emailValidation(emailValue) {
    const EMAIL_REGEXP = /^(([^<>()[\].,;:\s@"]+(\.[^<>()[\].,;:\s@"]+)*)|(".+"))@(([^<>()[\].,;:\s@"]+\.)+[^<>()[\].,;:\s@"]{2,})$/iu;
    console.log("emailValue " + emailValue + "   " + EMAIL_REGEXP.test(emailValue)); 
    return EMAIL_REGEXP.test(emailValue);
}

function copyText() {
    const copyTextEl = event.target.closest('.vendor-info-text').querySelector('.copy-text');

    const tempInput = document.createElement('input');
    tempInput.setAttribute('value', copyTextEl.innerText);

    document.body.appendChild(tempInput);

    tempInput.select();
    tempInput.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(tempInput.value);

    document.body.removeChild(tempInput);

    const alert = document.createElement('div');
    alert.classList.add('alert');
    alert.textContent = "Скопировано";

    document.body.appendChild(alert);

    setTimeout(() => {

        document.body.removeChild(alert);

    }, 1500);

}

</script>
