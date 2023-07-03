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
    
        //console.log('статус заказа  с id ' + id + ' изменен на ' + 1);
    
      });
    
}

//функция для отображения контакта покупателя
function showContact() {

    //показываем телефон и расстояние до покупателя в шапке заказа
    document.querySelector('.contact-data').classList.remove('d-none');

    //скрываем кнопку
    document.querySelector('.show-contact').classList.add('d-none');

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
