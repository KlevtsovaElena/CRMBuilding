//достаем id записи открытого заказа из атрибута
let id = document.querySelector('.orders').getAttribute('data-id');
console.log(id);

//достаем статус открытого заказа из атрибута
let status = document.querySelector('.page-title').getAttribute('data-status');
console.log(status);

// собираем ссылку для запросов
link = mainUrl + '/api/ordervendors.php';

//достаем из атрибута роль
let role = document.getElementById('new-order').getAttribute('data-role');

//отслеживаем открытие страницы с заказом, чтобы поменять в БД статус заказа на "Просмотрен", если заказ новый и если его открыл ПОСТАВЩИК
if (status == 0 && role == 2) {

    document.addEventListener("DOMContentLoaded", () => {

        // соберём json для передачи на сервер
        //статус в таблице order_vendors меняется на 1 - просмотрен
        let obj = JSON.stringify({
            'id': id,
            'status':  1
        });
    
        //передаем параметры на сервер в пост-запросе
        sendRequestPOST(link, obj);

        //меняем на фронте цифру на счетчике 
        let counter = document.getElementById('counter');

        //достаем текущее значение
        let currentNum = counter.innerHTML;
        console.log(currentNum);

        //отнимаем единицу
        let newNum = parseInt(currentNum) - 1;
        console.log(newNum);
        
        //если число больше 0
        if(newNum > 0) {
            //кладем в счетчик новое значение
            counter.innerHTML = newNum;
        //если новых заказов 0
        } else {
            console.log('новых заказов нет');
            counter.classList.toggle('d-none');
        }
        
        //console.log('статус заказа  с id ' + id + ' изменен на ' + 1);
    
      });
    
}

//функция для подтверждения заказа поставщиком
function confirmOrder() {

    // соберём json для передачи на сервер
    //статус в таблице order_vendors меняется на 2 - подтвержден
    let obj = JSON.stringify({
        'id': id,
        'status':  2
    });

    //передаем параметры на сервер в пост-запросе
    sendRequestPOST(link, obj);

    //console.log('статус заказа  с id ' + id + ' изменен на ' + 2);

    //кнопка "Подтвердить заказ" меняется на "Заказ доставлен" с соответствующей функцией по клику на нее
    let btn = document.getElementById('btn-confirm');
    //console.log(btn);
    btn.innerHTML = "ЗАКАЗ ДОСТАВЛЕН";
    btn.onclick = function(){
        confirmDelivery();
    };

    //достаем координаты клиента из дата-атрибута для отрисовки кнопки "Отправить себе координаты"
    let clientLatitude = document.getElementById('new-order').getAttribute('data-client-latitude');
    let clientLongitude = document.getElementById('new-order').getAttribute('data-client-longitude');
    let vendor_id = document.getElementById('new-order').getAttribute('data-tg-vendor-id');

    console.log(clientLatitude);
    console.log(clientLongitude);

    // если до подтверждения статус был "отменен", 
    if(status == 3) {
        //возвращаем кнопку "Отменить" для возможности отмены заказа
        btn.insertAdjacentHTML("afterend", `<button id="cancel-order" class="btn btn-ok d-iblock" onclick="cancelOrder()">ОТМЕНИТЬ ЗАКАЗ</button>`);

        //и отрисовываем кнопку "Отправить себе координаты" для возможности отмены заказа
        //для этого под уже имеющейся кнопкой отмены доставки
        let btn2 = document.getElementById('cancel-order');
        //console.log(btn2);
        //рисуем кнопку "Отправить себе координаты"
        btn2.insertAdjacentHTML("afterend", `<button id="send-location" class="btn btn-ok d-iblock">ОТПРАВИТЬ СЕБЕ КООРДИНАТЫ</button>`);

        //и вешаем на нее онклик с координатами клиента
        let addedBtn = document.getElementById('send-location');
        addedBtn.onclick = function(){
            sendLocation(clientLatitude, clientLongitude, vendor_id);
            console.log('sending location works');
        };
    }

    //в случае, если статус до подтверждения был "новый" или "просмотрен"
    if(status == 0 || status == 1) {
        //скрываем кнопку "НЕ ДОЗВОНИЛИСЬ"
        document.getElementById('btn-out-of-reach').classList.add('d-none');

        //и отрисовываем кнопку "Отправить себе координаты" для возможности отмены заказа
        //для этого под уже имеющейся кнопкой отмены доставки
        let btn2 = document.getElementById('cancel-order');
        //console.log(btn2);
        //рисуем кнопку "Отправить себе координаты"
        btn2.insertAdjacentHTML("afterend", `<button id="send-location" class="btn btn-ok d-iblock">ОТПРАВИТЬ СЕБЕ КООРДИНАТЫ</button>`);

        //и вешаем на нее онклик с координатами клиента
        let addedBtn = document.getElementById('send-location');
        addedBtn.onclick = function(){
            sendLocation(clientLatitude, clientLongitude, vendor_id);
            console.log('sending location works');
        };
    }

    //возвращение на страницу всех заказов
    // backToAllOrders();

}

//функция для подтверждения доставки поставщиком
function confirmDelivery() {

    // соберём json для передачи на сервер
    //статус в таблице order_vendors меняется на 4 - доставлен (завершен) + вешаем флаг о том, что нужна проверка для расчета задолженности поставщика
    let obj = JSON.stringify({
        'id': id,
        'status':  4,
        'with_debt_recalc': true
    });

    console.log(obj);

    //передаем параметры на сервер в пост-запросе
    sendRequestPOST(link, obj);

    // //достаем сумму данного заказа из атрибута
    // let new_sum = document.getElementById('total-sum').getAttribute('data-total-sum');
    // console.log(new_sum);

    // // соберём json для передачи на сервер
    // //отправляем новую сумму за успешный заказ
    // let obj2 = JSON.stringify({
    //     'id': vendor_id,
    //     'total_sum':  new_sum
    // });

    //передаем параметры на сервер в пост-запросе
    // sendRequestPOST(linkX, obj2);

    //возвращение на страницу всех заказов
    backToAllOrders();

}

//функция для отмены заказа поставщиком
function cancelOrder() {

    //статус в таблице order_vendors меняется на 3 - отменен
    let obj = JSON.stringify({
        'id': id,
        'status':  3
    });

    //передаем параметры на сервер в пост-запросе
    sendRequestPOST(link, obj);

    //console.log('статус заказа  с id ' + id + ' изменен на ' + 3);

    //возвращение на страницу всех заказов
    backToAllOrders();
}

//функция для проставления поставщиком статуса заказа "не дозвонились"
function customerOutOfReach() {

    //статус в таблице order_vendors меняется на ???

    //в тг покупателю отправляется сообщение о том, что не дозвонились
    //создание объекта, отправляющего запросы

    tg_id = 224039891; //здесь должен быть tg_id покупателя из БД!!!

    //формирование ссылки
    let link = 'https://api.telegram.org/bot6251938024:AAG84w6ZyxcVqUxmRRUW0Ro8d4ej7FpU83o/sendMessage?chat_id=' + tg_id + '&text=' + "Мы не смогли до Вас дозвониться.  Введите, пожалуйста, Ваш номер телефона вручную";

    //отправляем гет-запрос
    sendRequestGET(link);

    //возвращение на страницу всех заказов
    backToAllOrders();
}

//функция для возвращения на страницу всех заказов
function backToAllOrders() {
    window.location.href = '/pages/vendor-list-orders.php';
}

//функция отправки координат клиента в телеграм поставщика
function sendLocation(latitude, longitude, id) {

    let link = mainUrl + '/api/notification/telegram-send-location.php';

    //формируем параметры для передачи в апишку
    let obj = JSON.stringify({
        "chat_id" : id,
        "latitude" : latitude,
        "longitude" : longitude
    });

    //передаем параметры на сервер в пост-запросе
    sendRequestPOST(link, obj);

    console.log('локация отправлена');

}

//записываем в куки локальный часовой пояс
let timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
document.cookie = 'time_zone=' + timeZone;
console.log(document.cookie);
