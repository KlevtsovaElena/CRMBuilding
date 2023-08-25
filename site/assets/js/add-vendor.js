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
let phoneNumber = "";


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

    // значение валюты
    let currencyDollar;
    const radio = document.getElementsByName('currency_dollar');
    radio.forEach(item => {
        if (item.checked) {
            currencyDollar = item.value;
        }
    })

    // кол-во символов числового значения (998888888888) телефона должно равняться 12
    // если нет, то предупреждаем, что телефон записан не будет
    if (phone.value) {
        if (phone.value.replace(/\D/g, "").length !== 12) {
            let x = window.confirm('Телефон Поставщика некорректен. Отправить данные без телефона?');
            // если отказ, то не отправляем
            // если ок, то в базу пойдет пустое значение
            if (!x) {
                return; 
            } else {
                phoneNumber = "";
            }
        } else {
            phoneNumber = phone.value.replace(/\D/g, "");
        }
    }

    // соберём json для передачи на сервер
    let obj = JSON.stringify({
        'name': nameVendor.value.trim(),
        'city_id': cityId.value,
        'comment': comment.value.trim(),
        'phone': phoneNumber,
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

    // проверяем изменился ли телефон 
    // если да, то проверяем кол-во символов
    // кол-во символов числового значения (998888888888) телефона должно равняться 12
    let phoneDb = phone.getAttribute('data-phone');
    if (phoneDb.replace(/\D/g, "") !== phone.value.replace(/\D/g, "")) {
        if (phone.value) {
        
            if (phone.value.replace(/\D/g, "").length !== 12) {
                alert('Телефон поставщика не будет изменён');
                phoneNumber = phoneDb.replace(/\D/g, "");
                
            } else {
                phoneNumber = phone.value.replace(/\D/g, "");
            }
        } else {
            phoneNumber = "";
        }
    } else {
        phoneNumber = phoneDb.replace(/\D/g, "");
    }


    // соберём json для передачи на сервер
    let obj = JSON.stringify({
        'id': id,
        'name': nameVendor.value.trim(),
        'city_id': cityId.value,
        'comment': comment.value.trim(),
        'phone': phoneNumber,
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
     
    // перезагрузим страницу
    window.location.href = window.location.href;
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


/* ---------- МАСКА ДЛЯ ТЕЛЕФОНА ---------- */

function maskTel(event) {
console.log(phone.value.length);
    console.log(event.type);
    let keyCode;
    
    // определим какая клавиша нажата
    if (event.keyCode) {
        keyCode = event.keyCode;
    }

    // selectionStart - позиция начала выделенного текста или курсора
    let position = phone.selectionStart;
    // запретим удалять символы +998-
    if (position < 5) event.preventDefault();

    // зададим параметры маски
    let mask = "+998-__-___-__-__";
    // счётчик
    let count = 0;
    // отформатированное значение маски (только цифры)
    let maskOnlyNumber = mask.replace(/\D/g, "");
    // отформатированное значение инпута (только цифры)
    let phoneValue = phone.value.replace(/\D/g, "");
  
    // Здесь будет записывать результат, соответственно маске
    // +998-__-___-__-__, +998-7_-___-__-__, +998-78_-___-__-__ и так далее

    // берём маску и проходимся по символам
    // если символ _ или \d цифра  [_\d] - ([]один из)
    // то заменяем этот символ в соответвии со след ф-цией
    let  resultPhoneValue = mask.replace(/[_\d]/g, function(a) {

        if (count < phoneValue.length) {
            // если счётчик меньше, чем символов в инпуте
            // то заменяем на символ из инпута или из маски (если там число)
            // прибавляем к счётчику 1 только в случае, если в маске нет цифры
            return phoneValue.charAt(count++) || maskOnlyNumber.charAt(count)
        } else {
            // если сount больше или равен символам в инпуте
            // заменяем на введённый символ
            return a
        }
    });

    // перезапишем значение count на индекс первого вхождения символа _
    count = resultPhoneValue.indexOf("_");

    // если есть незаполненные _ (т.е != -1)
    if (count != -1) {
        // если count символ _ стоит где-то на месте +998-, то присв инпту +998- 
        count < 5 && (count = 3);
        resultPhoneValue = resultPhoneValue.slice(0, count)
    }

    // запишем регулярку        
    let reg = mask.substring(0, phone.value.length).replace(/_+/g, function(a) {
        
            return "\\d{1," + a.length + "}"
    }).replace(/[+()]/g, "\\$&");
    reg = new RegExp("^" + reg + "$");
    // на выходе получаем такое /^\+998-\d{1,2}-\d{1,3}-\d{1,2}-\d{1,2}$/ (когда заполнены ВСЕ данные) постепенно


    // если значение инпут не подходит к регулярке ИЛИ кол-во символов <6 ИЛИ нажата нецифровая клавиша, то заменяем 
    // внесённое значение на значение resultPhoneValue (где мы заменяли символы по маске)
    if (!reg.test(phone.value) || phone.value.length < 6 || keyCode > 47 && keyCode < 58) phone.value = resultPhoneValue;

    // если тип события blur (снятие фокуса с инпута????)
    if (event.type == "blur" && phone.value.length < 6)  {
        phone.value = "";
    }
}

// // 
// function onChangePhone() {
//     let info = phone.closest('.form-add-vendor__item').querySelector('.error-info');
//     if (phone.value.replace(/\D/g, "").length !== 12) {
//         info.innerText = 'Данные будут записаны в базу без телефона Поставщика';
//         info.classList.remove('d-none')
//     } else {
//         info.innerText = '';
//         info.classList.add('d-none')
//     }
// } 

// события для применения маски
phone.addEventListener("input", maskTel);
phone.addEventListener("focus", maskTel);
phone.addEventListener("blur", maskTel);
phone.addEventListener("keydown", maskTel);
// phone.addEventListener("change", onChangePhone);
