//достаем id записи открытого заказа из атрибута
let id = document.querySelector('.orders').getAttribute('data-id');
console.log(id);

//достаем статус открытого заказа из атрибута
let status = document.querySelector('.page-title').getAttribute('data-status');
console.log(status);

// собираем ссылку для запросов
link = 'http://localhost/api/ordervendors.php';

//отслеживаем открытие страницы с заказом, чтобы поменять в БД статус заказа на "Просмотрен", если заказ новый
if (status == 0) {

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
        //кладем в счетчик новое значение
        counter.innerHTML = newNum;
    
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
    console.log(btn);
    btn.innerHTML = "ЗАКАЗ ДОСТАВЛЕН";
    btn.onclick = function(){
        confirmDelivery();
    };

    // если до подтверждения статус был "отменен", 
    if(status == 3) {
        //возвращаем кнопку "Отменить" для возможности отмены заказа
        btn.insertAdjacentHTML("afterend", `<button class="btn btn-ok d-iblock" onclick="cancelOrder()">ОТМЕНИТЬ ЗАКАЗ</button>`);
    }

    //в случае, если статус до подтверждения был "новый" или "просмотрен"
    if(status == 0 || status == 1) {
        //скрываем кнопку "НЕ ДОЗВОНИЛИСЬ"
        document.getElementById('btn-out-of-reach').classList.add('d-none');
    }

    
    //возвращение на страницу всех заказов
    // backToAllOrders();

}

//функция для подтверждения доставки поставщиком
function confirmDelivery() {

    // соберём json для передачи на сервер
    //статус в таблице order_vendors меняется на 4 - доставлен (завершен)
    let obj = JSON.stringify({
        'id': id,
        'status':  4
    });

    //передаем параметры на сервер в пост-запросе
    sendRequestPOST(link, obj);

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
