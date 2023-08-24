console.log('подключили add-vendor.js', mainUrl);

// определим основные переменные
const formAddVendor = document.querySelector('.form-add-vendor');
const vendorInfo = document.querySelector('.vendor-info');
const copyBtn = document.querySelectorAll('.copy-result');
let errorVendor = document.querySelector('.vendor-info-error');

// запишем значения полей формы в переменные
const nameVendor = formAddVendor.querySelector('#name');
const cityId = formAddVendor.querySelector('#city_id');
const comment = formAddVendor.querySelector('#comment');
const phone = formAddVendor.querySelector('#phone');
const email = formAddVendor.querySelector('#email');
const percent = formAddVendor.querySelector('#percent');
const is_active = formAddVendor.querySelector('#is_active');

// подтверждение цены инфа
let priceConfirmedEl;
let tmplPriceConfirm;
let tmplPriceNotConfirm;

if (document.querySelector('.price-confirm-container')) {
    priceConfirmedEl = document.querySelector('.price-confirm-container');
    tmplPriceConfirm = document.getElementById('tmpl-price-confirm').innerHTML;
    tmplPriceNotConfirm = document.getElementById('tmpl-price-not-confirm').innerHTML;
}

function addVendor() {
        
    // проверяем корректность токена
    check();

    //предотвратить дефолтные действия, отмена отправки формы (чтобы страница не перезагружалась)
    event.preventDefault(); 

    // очистим контенеры для вывода информации
    vendorInfo.innerHTML = "";
    errorVendor.innerText = "";

    hasError = validationAddVendor();

    // если были ошибки, то выходим
    if (hasError) {
        return;
    }

    let currencyDollar;
    const radio = document.getElementsByName('currency_dollar');
    radio.forEach(item => {
        if (item.checked) {
            currencyDollar = item.value;
        }
    })

    // соберём json для передачи на сервер
    let obj = JSON.stringify({
        'name': nameVendor.value.trim(),
        'city_id': cityId.value,
        'comment': comment.value.trim(),
        'phone': phone.value,
        'email': email.value.trim(),
        'is_active': is_active.value,
        'percent': percent.value, 
        'currency_dollar': currencyDollar,
        'price_confirmed': 1,
        'role': 2 // соответствует роли поставщика
    });

    // передаём данные на сервер
    let responseJson = sendRequestPOST(mainUrl + '/api/vendors.php', obj);
    let response;
    // получаем ответ с сервера
    if (responseJson) {
       response = JSON.parse(responseJson);
       if (response['error']) {
        errorVendor.innerText = response['error'];
        return;
       }
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
                                    <p><i>Пароль: ${response['pass']}</i></p> 
                                </div>
                                <button class="copy-result btn btn-ok" onclick="copyText()">Copy</button>                             
                            </div>`

    formAddVendor.reset();
}

function validationAddVendor() {
    hasError = false;

    [nameVendor, cityId, email, percent, is_active].forEach(item =>  {
    
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

// function addVendorToggle() {
//     vendorInfo.innerHTML = "";
//     document.querySelector('.add-vendor').classList.toggle('d-none');
// }

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
    document.execCommand('copy');

    document.body.removeChild(tempInput);

    const alert = document.createElement('div');
    alert.classList.add('alert');
    alert.textContent = "Скопировано";

    document.body.appendChild(alert);

    setTimeout(() => {

        document.body.removeChild(alert);

    }, 1500);

}

function editVendor(id) {
        
    // проверяем корректность токена
    check();

    //предотвратить дефолтные действия, отмена отправки формы (чтобы страница не перезагружалась)
    event.preventDefault(); 

    // очистим контенеры для вывода информации
    vendorInfo.innerHTML = "";

    hasError = validationAddVendor();

    // если были ошибки, то выходим
    if (hasError) {
        return;
    }

    let currencyDollar;
    let currencyDB;
    const radio = document.getElementsByName('currency_dollar');
    radio.forEach(item => {
        if (item.checked) {
            currencyDollar = item.value;
            currencyDB = item.closest('.form-add-vendor__item').getAttribute('currency');    
        }
    })

    // соберём json для передачи на сервер
    let obj = JSON.stringify({
        'id': id,
        'name': nameVendor.value.trim(),
        'city_id': cityId.value,
        'comment': comment.value.trim(),
        'phone': phone.value,
        'email': email.value.trim(),
        'percent': percent.value,
        'currency_dollar': currencyDollar,
        'price_confirmed':  priceConfirmedEl.getAttribute('confirm-price'),
        'is_active': is_active.value
    });

    // передаём данные на сервер
    sendRequestPOST(mainUrl + '/api/vendors.php', obj);

    // если меняем Сум на $, то цены П уводим на рассмотрение и обнуляем их в базе
    if (currencyDollar == '1' && currencyDB == '0') {
        let objPrice  = JSON.stringify({
            'id': id,
            'rate': 0,
            'price_confirmed': 0
        })
    
        // передаём данные на сервер
        sendRequestPOST(mainUrl + '/api/price/change-price-rate.php', objPrice);
    }
     
    // // перезагрузим страницу
    // window.location.href = window.location.href;
}

// вывести предупреждение при смене Сум на $
function checkCurrency() {
    const parentEl = event.target.closest('.form-add-vendor__item');
    if (parentEl.getAttribute('currency') !== '1') {
        const infoCurrency = parentEl.querySelector('.error-info');
        infoCurrency.classList.remove('d-none');
        infoCurrency.innerText = "! При изменении Сум на $ цены обнулятся";
    }
}

// удаление поставщика админом

function deleteVendorFromEditForm(id) {
        
    // проверяем корректность токена
    check();

    // запрашиваем подтверждение удаления
    let isDelete = false;

    isDelete = window.confirm('Вы действительно хотите удалить этого поставщика?');

    if(!isDelete) {
        return;
    }


    // соберём json
    let obj = JSON.stringify({
        'id': id,
        'is_active': 0,
        'deleted':  1
    });

    // делаем запрос на удаление поставщика по id
    sendRequestPOST(mainUrl + '/api/vendors/delete-vendor-with-products.php', obj);


    // делаем запрос на удаление поставщика по id
    // sendRequestDELETE(mainUrl + '/api/products.php?id=' + id);

    alert("Поставщик удалён");

    // получим гет параметры страницы без id
    let paramsArr = window.location.href.split('?')[1].split('&');
    paramsArr.splice(0, 1);
    let params = paramsArr.join('&');

    // переход обратно на странницу списка поставщиков с прежними параметрами
    window.location.href = mainUrl + '/pages/admin-vendors.php?' + params;
    
}

// проверка поля с процентом
function percentValid(obj) {
    if (obj.value < 0) {
        obj.value = 0; 
    } else if (obj.value > 100) {
        obj.value = 100;
    } 
}

// меняем отображение подтверждения цен при нажатии на галочку или крестик
function changePriceConfirm() {

    let confirmPrice = priceConfirmedEl.getAttribute('confirm-price');

    if (confirmPrice == 1) {
        priceConfirmedEl.setAttribute('confirm-price', '0');
        priceConfirmedEl.innerHTML = tmplPriceNotConfirm;
    } else {
        priceConfirmedEl.setAttribute('confirm-price', '1');
        priceConfirmedEl.innerHTML = tmplPriceConfirm;
    }

}