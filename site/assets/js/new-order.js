console.log('new-order.js подключен');

let data = [
    {
        "id": 2,
        "order_id": 124,
        "vendor_id": 1,
        "vendor_location": 
            {
                "latitude": 55.657107,
                "longitude": 37.569608
            },
        "order_date": 1687924077,
        "status": 0,
        "customer_phone": "+998998885566",
        "order_location": 
                    {
                        "latitude": 57.569608,
                        "longitude": 35.569608
                    },
        "products":
            [
                {
                    "id": 15,
                    "quantity": 4,
                    "name": "Гипсокартон1",
                    "price": 100,
                    "available": 6
                },
                {
                    "id": 1,
                    "quantity": 2,
                    "name": "Гипсокартон jhskjfhaskjhfaskjh",
                    "price": 100,
                    "available": 3
                },
                {
                    "id": 14,
                    "quantity": 1,
                    "name": "Штукатурочка",
                    "price": 100,
                    "available": 3
                },
                {
                    "id": 5,
                    "quantity": 15,
                    "name": "Штукатурочка",
                    "price": 100,
                    "available": 56
                }   
            ]
    }]

//функция конвертации unix времени в стандартное
function timeConverter(unixTimestamp){
    let a = new Date(unixTimestamp * 1000);
    console.log(a);
    let months = ['01','02','03','04','05','06','07','08','09','10','11','12'];
    let year = a.getFullYear();
    let month = months[a.getMonth()];
    let date = a.getDate();
    let hour = a.getHours().toString().padStart(2, '0');
    let min = a.getMinutes().toString().padStart(2, '0');
    let time = date + '.' + month + '.' + year + ' (' + hour + ':' + min + ')';
    return time;
}

//функция расчета расстояния в км по координатам
function getDistanceFromLatLonInKm(lat1,lon1,lat2,lon2) {
    let R = 6371; // радиус Земли в км
    let dLat = deg2rad(lat2-lat1);  // функция deg2rad прописана ниже
    let dLon = deg2rad(lon2-lon1); 
    let a = 
      Math.sin(dLat/2) * Math.sin(dLat/2) +
      Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * 
      Math.sin(dLon/2) * Math.sin(dLon/2); 
    let c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

    // получаем расстояние в км
    let d = R * c; 

    //возвращаем округленное до целого числа значение
    return Math.round(d);
}
  
function deg2rad(deg) {
    return deg * (Math.PI/180)
}


//получаем все контейнеры для шаблонов и сами шаблоны
let containerNewOrder = document.getElementById('new-order');
let templateNewOrder = document.getElementById('template-new-order').innerHTML;

let containerNewOrderProducts = document.getElementById('new-order-products');
let templateNewOrderProducts = document.getElementById('template-new-order-products').innerHTML;

let containerNewOrderSum = document.getElementById('new-order-sum');
let templateNewOrderSum = document.getElementById('template-new-order-sum').innerHTML;

// отрисуем товары в таблице 
renderNewOrder();

//функция для отрисовки таблицы нового заказа
function renderNewOrder() {

    

    //очистим контейнер
    containerNewOrder.innerHTML = '';

    //отрисовываем основную часть, которая не повторяется
    containerNewOrder.innerHTML += templateNewOrder.replace('${order_id}', data[0]['order_id'])
                                                        .replace('${date}', timeConverter(data[0]['order_date']))
                                                        .replace('${phone}', data[0]['customer_phone'])
                                                        .replace('${distance}', getDistanceFromLatLonInKm(data[0]['vendor_location']['latitude'], data[0]['vendor_location']['longitude'], data[0]['order_location']['latitude'], data[0]['order_location']['longitude']));
                                                        

    

    
    let products = data[0]['products'];

    //очистим контейнер
    containerNewOrderProducts.innerHTML = '';

    let totalQuantity = 0;
    let totalSum = 0;

    //отрисовываем заказанные товары в цикле
    for (let i = 0; i < products.length; i++) {

   
        containerNewOrderProducts.innerHTML += templateNewOrderProducts.replace('${name}', products[i]['name'])
                                                                    .replace('${quantity}', products[i]['quantity'])
                                                                    .replace('${price}', products[i]['price'])
                                                                    .replace('${calculated_price}', products[i]['quantity'] * products[i]['price']);
        totalQuantity += products[i]['quantity'];
        totalSum += products[i]['quantity'] * products[i]['price'];
    }

    //очистим контейнер
    containerNewOrderSum.innerHTML = '';

    //отрисовываем основную часть, которая не повторяется
    containerNewOrderSum.innerHTML += templateNewOrderSum.replace('${total_quantity}', totalQuantity)
                                                        .replace('${total_sum}', totalSum);

}




//функция для отображения контакта покупателя
function showContact() {

    //отслеживаем нажатие на кнопку
    //статус в таблице order_vendors меняется на 1 - открыт

    //показываем телефон и расстояние до покупателя в шапке заказа

}

//функция для подтверждения заказа поставщиком
function confirmOrder() {

    //статус в таблице order_vendors меняется на 2 - подтвержден
    // изменение статуса заказа в БД
    // соберём json для передачи на сервер
    let id = data['id'];

    let obj = JSON.stringify({
        'id': id,
        'status':  2
    });

    // передаём данные на сервер
    sendRequestPOST('http://localhost/api/ordervendors.php', obj);

    // получаем ответ с сервера

    // imagePreview.innerHTML = "<img>";
    // alert("Статус заказа обновлен");

    //возвращение на страницу всех заказов

}

//функция для отмены заказа поставщиком
function cancelOrder() {

    //статус в таблице order_vendors меняется на 3 - отменен
    //возвращение на страницу всех заказов

}

//функция для проставления поставщиком статуса заказа "не дозвонились"
function customerOutOfReach() {

    //статус в таблице order_vendors меняется на ???
    //возвращение на страницу всех заказов

}

let testData = [
    {
        "id": 2,
        "order_id": 124,
        "vendor_id": 1,
        "vendor_location": 
            {
                "latitude": 55.657107,
                "longitude": 37.569608
            },
        "order_date": 1235664544,
        "status": 0,
        "customer":
            {
                "phone": "+998998885566",
                "location": 
                    {
                        "latitude": 57.569608,
                        "longitude": 35.569608
                    }
            },
        "products":
            [
                {
                    "id": 15,
                    "quantity": 4,
                    "name": "Гипсокартон1",
                    "price": 100,
                    "available": 6
                },
                {
                    "id": 1,
                    "quantity": 2,
                    "name": "Гипсокартон jhskjfhaskjhfaskjh",
                    "price": 100,
                    "available": 3
                },
                {
                    "id": 14,
                    "quantity": 1,
                    "name": "Штукатурочка",
                    "price": 100,
                    "available": 3
                },
                {
                    "id": 5,
                    "quantity": 15,
                    "name": "Штукатурочка",
                    "price": 100,
                    "available": 56
                }   
            ]
    }]