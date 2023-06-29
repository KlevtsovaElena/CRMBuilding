//достаем id записи открытого заказа из атрибута
let id = document.querySelector('.orders').getAttribute('data-id');
console.log(id);

console.log(document.querySelector('section').innerHTML);

// собираем ссылку для запросов
link = 'http://localhost/api/ordervendors.php';

//отслеживаем открытие страницы с данным новым заказом, чтобы поменять в БД статус заказа на "Просмотрен"
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


//функция конвертации unix времени в стандартное в формате dd.mm.yyyy, hh:ii
// function timeConverter(unixTimestamp){
//     const date = new Date(unixTimestamp * 1000);
//     const options = {
//         day: '2-digit',
//         month: '2-digit',
//         year: 'numeric',
//         hour: '2-digit',
//         minute: '2-digit',
//         hour12: false,
//     };
//     const localTime = date.toLocaleTimeString(undefined, options);
//     return localTime;
// }

// //функция расчета расстояния в км по координатам
// function getDistanceFromLatLonInKm(lat1,lon1,lat2,lon2) {
//     let R = 6371; // радиус Земли в км
//     let dLat = deg2rad(lat2-lat1);  // функция deg2rad прописана ниже
//     let dLon = deg2rad(lon2-lon1); 
//     let a = 
//       Math.sin(dLat/2) * Math.sin(dLat/2) +
//       Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * 
//       Math.sin(dLon/2) * Math.sin(dLon/2); 
//     let c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

//     // получаем расстояние в км
//     let d = R * c; 

//     //возвращаем округленное до целого числа значение
//     return Math.round(d);
// }
  
// function deg2rad(deg) {
//     return deg * (Math.PI/180)
// }

// function sendRequestGET(url) {
//     let xhr = new XMLHttpRequest();
//     xhr.open('GET', url, false);
//     xhr.send();

//     //отдает данные(результат)
//     return xhr.responseText;
// }


// //получаем все контейнеры для шаблонов и сами шаблоны
// let containerNewOrder = document.getElementById('new-order');
// let templateNewOrder = document.getElementById('template-new-order').innerHTML;

// let containerNewOrderProducts = document.getElementById('new-order-products');
// let templateNewOrderProducts = document.getElementById('template-new-order-products').innerHTML;

// let containerNewOrderSum = document.getElementById('new-order-sum');
// let templateNewOrderSum = document.getElementById('template-new-order-sum').innerHTML;

// renderNewOrder();

// //функция для отрисовки таблицы нового заказа
// function renderNewOrder() {

//     //отправляем запрос на получение всех данных по заказу
//     let json = sendRequestGET("http://localhost/api/order-vendors/get-with-details.php");

//     //раскодируем данные
//     let data = JSON.parse(json); 
//     console.log(data);


//     //очистим контейнер
//     containerNewOrder.innerHTML = '';

//     //отрисовываем основную часть, которая не повторяется
//     containerNewOrder.innerHTML += templateNewOrder.replace('${order_id}', data[0]['order_id'])
//                                                         .replace('${date}', timeConverter(data[0]['order_date']))
//                                                         .replace('${phone}', data[0]['customer_phone'])
//                                                         .replace('${distance}', getDistanceFromLatLonInKm(data[0]['vendor_location']['latitude'], data[0]['vendor_location']['longitude'], data[0]['order_location']['latitude'], data[0]['order_location']['longitude']));
                                                        

    

    
//     let products = data[0]['products'];

//     //очистим контейнер
//     containerNewOrderProducts.innerHTML = '';

//     let totalQuantity = 0;
//     let totalSum = 0;

//     //отрисовываем заказанные товары в цикле
//     for (let i = 0; i < products.length; i++) {

   
//         containerNewOrderProducts.innerHTML += templateNewOrderProducts.replace('${name}', products[i]['name'])
//                                                                     .replace('${quantity}', products[i]['quantity'])
//                                                                     .replace('${price}', products[i]['price'])
//                                                                     .replace('${calculated_price}', products[i]['quantity'] * products[i]['price']);
//         totalQuantity += products[i]['quantity'];
//         totalSum += products[i]['quantity'] * products[i]['price'];
//     }

//     //очистим контейнер
//     containerNewOrderSum.innerHTML = '';

//     //отрисовываем основную часть, которая не повторяется
//     containerNewOrderSum.innerHTML += templateNewOrderSum.replace('${total_quantity}', totalQuantity)
//                                                         .replace('${total_sum}', totalSum);

// }






// let data = [
//     {
//         "id": 2,
//         "order_id": 124,
//         "vendor_id": 1,
//         "vendor_location": 
//             {
//                 "latitude": 55.657107,
//                 "longitude": 37.569608
//             },
//         "order_date": 1687924077,
//         "status": 0,
//         "customer_phone": "+998998885566",
//         "order_location": 
//                     {
//                         "latitude": 57.569608,
//                         "longitude": 35.569608
//                     },
//         "products":
//             [
//                 {
//                     "id": 15,
//                     "quantity": 4,
//                     "name": "Гипсокартон1",
//                     "price": 100,
//                     "available": 6
//                 },
//                 {
//                     "id": 1,
//                     "quantity": 2,
//                     "name": "Гипсокартон jhskjfhaskjhfaskjh",
//                     "price": 100,
//                     "available": 3
//                 },
//                 {
//                     "id": 14,
//                     "quantity": 1,
//                     "name": "Штукатурочка",
//                     "price": 100,
//                     "available": 3
//                 },
//                 {
//                     "id": 5,
//                     "quantity": 15,
//                     "name": "Штукатурочка",
//                     "price": 100,
//                     "available": 56
//                 }   
//             ]
//     }]