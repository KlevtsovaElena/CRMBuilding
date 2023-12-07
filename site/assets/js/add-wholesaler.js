console.log('подключили add-wholesaler.js');

function addWholesaler() {
        
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
    let priceConf = 1;
    const radio = document.getElementsByName('currency_dollar');
    radio.forEach(item => {
        if (item.checked) {
            currencyDollar = item.value;
        }
    })
    if (currencyDollar == '1') priceConf = 0;

    // если есть значение телефона (не пустое)
    // проверим кол-во символов числового значения (998888888888) телефона должно равняться 12
    // если нет, то предупреждаем, что телефон записан не будет
    if (phone.value) {
        if (phone.value.replace(/\D/g, "").length == 12 && phone.value.replace(/\D/g, "").substr(0,3) == '998') {
            phoneNumber = phone.value.replace(/\D/g, "");

        } else if (phone.value.replace(/\D/g, "").length == 3 && phone.value.replace(/\D/g, "").substr(0,3) == '998') {
            phoneNumber = "";

        } else {
            let x = window.confirm('Телефон Поставщика некорректен. Отправить данные без телефона?');
            // если отказ, то не отправляем
            // если ок, то в базу пойдет пустое значение
            if (!x) {
                return; 
            } else {
                phoneNumber = "";
            }
        }

    } else {
        phoneNumber = "";
    }

    //категории
    //вынимаем данные по категориям в 2 индексных массива
    let checkboxes = document.querySelectorAll('input[type="checkbox"]');
    let checkboxesChecked = []; // массив для id категорий
    let categoriesNames = []; // массив для названий категорий
    for (let i = 0; i< checkboxes.length; i++) {
        if (checkboxes[i].checked) {
            checkboxesChecked.push(checkboxes[i].value);
            categoriesNames.push(document.getElementsByClassName('category')[i].getAttribute('data-category'));
        }
    }

    //объединяем id и названия в единый ассотиативный массив
    let categoriesArr = {};

    for (let m = 0; m < checkboxesChecked.length; m++) {
        categoriesArr[checkboxesChecked[m]] = categoriesNames[m];
    }

    let categories = JSON.stringify(categoriesArr);
    console.log(categories);

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
        'price_confirmed': priceConf,
        'role': 3, // соответствует роли оптовика
        'categories': categories
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
    vendorInfo.innerHTML = `<p>Оптовик <b> ${nameVendor.value} </b> создан! Скопируйте и отправьте пользователю:</p>
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

function editWholesaler1(id) {
        
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

    // проверяем изменился ли телефон (класс change)
    // если да, если есть значение телефона (не пустое)
    // проверим кол-во символов числового значения (998888888888) телефона должно равняться 12
    // если нет, то предупреждаем, что телефон записан не будет
 
    let phoneDb = phone.getAttribute('data-phone');

    if (phone.classList.contains('change')) {
        if (phone.value) {
            if (phone.value.replace(/\D/g, "").length == 12 && phone.value.replace(/\D/g, "").substr(0,3) == '998') {
                phoneNumber = phone.value.replace(/\D/g, "");
    
            } else if (phone.value.replace(/\D/g, "").length == 3 && phone.value.replace(/\D/g, "").substr(0,3) == '998') {
                phoneNumber = "";
    
            } else {
                alert('Телефон поставщика некорректен и не будет изменён!');
                phoneNumber = phoneDb.replace(/\D/g, "");
            }
            
        } else {
            phoneNumber = "";
        }

    } else {
        phoneNumber = phoneDb.replace(/\D/g, "");
    }

    //категории
    //вынимаем данные по категориям в 2 индексных массива
    let checkboxes = document.querySelectorAll('input[type="checkbox"]');
    let checkboxesChecked = []; // массив для id категорий
    let categoriesNames = []; // массив для названий категорий
    for (let i = 0; i< checkboxes.length; i++) {
        if (checkboxes[i].checked) {
            checkboxesChecked.push(checkboxes[i].value);
            categoriesNames.push(document.getElementsByClassName('category')[i].getAttribute('data-category'));
        }
    }

    //объединяем id и названия в единый ассотиативный массив
    let categoriesArr = {};

    for (let m = 0; m < checkboxesChecked.length; m++) {
        categoriesArr[checkboxesChecked[m]] = categoriesNames[m];
    }

    let categories = JSON.stringify(categoriesArr);
    console.log(categories);

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
        'is_active': is_active.value,
        'categories': categories
    });

    // передаём данные на сервер
    sendRequestPOST(mainUrl + '/api/vendors.php', obj);

    // если меняем Сум на $, то цены П уводим на рассмотрение и обнуляем их в базе
    // и на каждый товар вешаем is_confirm = 0 
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

// удаление оптовика админом
function deleteWholesalerFromEditForm(id) {
        
    // проверяем корректность токена
    check();

    // запрашиваем подтверждение удаления
    let isDelete = false;

    isDelete = window.confirm('Вы действительно хотите удалить этого оптовика?');

    if(!isDelete) {
        return;
    }

    // соберём json
    let obj = JSON.stringify({
        'id': id,
        'is_active': 0,
        'deleted':  1
    });

    // делаем запрос на удаление оптовика по id
    sendRequestPOST(mainUrl + '/api/vendors/delete-vendor-with-products.php', obj);

    // делаем запрос на удаление оптовика по id
    // sendRequestDELETE(mainUrl + '/api/products.php?id=' + id);

    alert("Оптовик удалён");

    // получим гет параметры страницы без id
    let paramsArr = window.location.href.split('?')[1].split('&');
    paramsArr.splice(0, 1);
    let params = paramsArr.join('&');

    // переход обратно на странницу списка оптовиков с прежними параметрами
    window.location.href = mainUrl + '/pages/admin-wholesalers.php?' + params;
    
}