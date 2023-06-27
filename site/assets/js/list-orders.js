const containerListOrders2 = document.querySelector('.list-orders__body2');
let testStatus = {
    "0": "Новый",
    "1": "Просмотрен",
    "2": "Подтверждён",
    "3": "Отменён",
    "4": "Звершён",
    "5": "Не дозвонились"

}
let testData = [
    {
        "id": 2,
        "order_id": 124,
        "vendor_id": 1,
        "order_date": 1235664544,
        "status": 0,
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
            ],
        "comment": "Комментарий",
        "complete_date": 0,
        "customers": {
            "phone": 42454545,
            "location": 
            {
            
                "latitude": 55.657107, 
                "longitude": 37.569608
            }
        },
        "vendor_location": {
            "latitude": 55.657107, 
            "longitude": 37.569608
        }
    },

    {
        "id": 3,
        "order_id": 134,
        "vendor_id": 1,
        "order_date": 1235664544,
        "status": 1,
        "products":
            [
                {
                    "id": 15,
                    "quantity": 4,
                    "name": "Гипсокартон2",
                    "price": 100,
                    "available": 6
                },
                {
                    "id": 1,
                    "quantity": 2,
                    "name": "Гtdytetyuety",
                    "price": 100,
                    "available": 3
                },
                {
                    "id": 14,
                    "quantity": 1,
                    "name": "Штукатурочка2",
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
            ],
        "comment": "Комментарий",
        "complete_date": 0,
        "customers": {
            "phone": 42454545,
            "location": 
            {
            
                "latitude": 55.657107, 
                "longitude": 37.569608
            }
        },
        "vendor_location": {
            "latitude": 55.657107, 
            "longitude": 37.569608
        }
    },

    {
        "id": 5,
        "order_id": 126,
        "vendor_id": 1,
        "order_date": 1235664544,
        "status": 2,
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
            ],
        "comment": "Комментарий",
        "complete_date": 0,
        "customers": {
            "phone": 42454545,
            "location": 
            {
            
                "latitude": 55.657107, 
                "longitude": 37.569608
            }
        },
        "vendor_location": {
            "latitude": 55.657107, 
            "longitude": 37.569608
        }
    },

    {
        "id": 14,
        "order_id": 154,
        "vendor_id": 1,
        "order_date": 1235664544,
        "status": 3,
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
            ],
        "comment": "Комментарий",
        "complete_date": 0,
        "customers": {
            "phone": 42454545,
            "location": 
            {
            
                "latitude": 55.657107, 
                "longitude": 37.569608
            }
        },
        "vendor_location": {
            "latitude": 55.657107, 
            "longitude": 37.569608
        }
    },

    {
        "id": 14,
        "order_id": 154,
        "vendor_id": 1,
        "order_date": 1235664544,
        "status": 4,
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
            ],
        "comment": "Комментарий",
        "complete_date": 45346546546,
        "customers": {
            "phone": 42454545,
            "location": 
            {
            
                "latitude": 55.657107, 
                "longitude": 37.569608
            }
        },
        "vendor_location": {
            "latitude": 55.657107, 
            "longitude": 37.569608
        }
    },

    {
        "id": 14,
        "order_id": 154,
        "vendor_id": 1,
        "order_date": 1235664544,
        "status": 5,
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
            ],
        "comment": "Комментарий",
        "complete_date": 45346546546,
        "customers": {
            "phone": 42454545,
            "location": 
            {
            
                "latitude": 55.657107, 
                "longitude": 37.569608
            }
        },
        "vendor_location": {
            "latitude": 55.657107, 
            "longitude": 37.569608
        }
    }

]




console.log('подключили list-orders');
let vendor_id = document.getElementById('vendor_id').value;
const containerListOrders = document.querySelector('.list-orders__body');

// найдём шаблон и контейнер для отрисовки
const tmplRowOrder = document.getElementById('template-body-table').innerHTML;

// найдём шаблон и контейнер для отрисовки
const tmplPagination = document.getElementById('template-pagination').innerHTML;
const containerPagination = document.querySelector('.pagination-wrapper');

let limit = 10;
let currentPage = 1;
let params;
let prevButton;
let nextButton;
let totalPages;

let productsListInOrder;

// получение записей заказов из БД
let url = 'http://localhost/api/ordervendors.php?vendor_id=' + vendor_id;
let totalOrdersJson = sendRequestGET(url);
let totalOrders = JSON.parse(totalOrdersJson);

// подсчёт полученных записей
let totalOrdersCount = totalOrders.length;

// отрисуем пагинацию
renderPagination(totalOrdersCount, limit);

// отрисуем товары в таблице 
renderListOrders(totalOrders);


/* ---------- ОТРИСОВКА ТОВАРОВ В ТАБЛИЦЕ---------- */
function renderListOrders(totalOrders) {
    let records = totalOrders.length;


    // очистим контейнер
    containerListOrders.innerHTML = "";

    // если записей нет, то выводим об этом инфо и выходим
    if (totalOrders.length === 0) {
        const info = document.querySelector('.info-table');
        info.innerText = "Записей нет";
        return;
    }

    // заполним данными и отрисуем шаблон
    if ((limit) && (limit < records)) { records = limit; }



    for (let i = 0; i < records; i++) {
        console.log(totalOrders[i]['products']);
        let keys = Object.keys(totalOrders[i]['products']);
        let productsListInOrder = "";
        console.log(keys);
        for (let j = 0; j < keys.length; j++) { 
            console.log(keys[j] + " - " + totalOrders[i]['products'][keys[j]]);
            productsListInOrder += keys[j] + " - " + totalOrders[i]['products'][keys[j]] + ", ";
            
        };

        containerListOrders.innerHTML += tmplRowOrder.replace('${order_id}', totalOrders[i]['order_id'])
                                                        .replace('${order_id}', totalOrders[i]['order_id'])
                                                        .replace('${id}', totalOrders[i]['id'])
                                                        .replace('${status}', totalOrders[i]['status'])
                                                        .replace('${status}', totalOrders[i]['status'])
                                                        .replace('${status}', totalOrders[i]['status'])
                                                        .replace('${order_date}', "")
                                                        .replace('${products}', productsListInOrder)
                                                        .replace('${total_price}', "")
                                                        .replace('${complete_date}', "");

    }
}


/* ---------- ОТРИСОВКА ПАГИНАЦИИ ---------- */
function renderPagination(totalProductsCount, limit) {

    // из полученных переменных получаем кол-во страниц
    if ((limit) && limit < totalProductsCount) {
        totalPages = Math.ceil(totalProductsCount/limit);
    } else {
        totalPages = 1;
    }

    // очистим контейнер
    containerPagination.innerHTML = "";

    // заполним данными и отрисуем шаблон
    containerPagination.innerHTML = tmplPagination  .replace('${currentPage}', currentPage)
                                                    .replace('${currentPage}', currentPage)
                                                    .replace('${totalPages}', totalPages);

    prevButton = document.querySelector('.page-switch__prev');
    nextButton = document.querySelector('.page-switch__next');

    // если количество страниц>1, то делаем активной кнопку далее
    if (totalPages > 1) {
        nextButton.removeAttribute('disabled');
    }

    console.log('totalPages', totalPages);
}

test() ;

function test() {

    console.log(testData);
    for (let i = 0; i < testData.length; i++) {

        let productsTest = "";
        let totalPriceTest = 0; 

        for (let j = 0; j < testData[i]['products'].length; j++){
            productsTest += testData[i]['products'][j]['name'] + " (" + 
                            (testData[i]['products'][j]['quantity']) + '), ';
            totalPriceTest = testData[i]['products'][j]['quantity'] * testData[i]['products'][j]['price'];

        }



        containerListOrders2.innerHTML += tmplRowOrder.replace('${order_id}', testData[i]['order_id'])
                                                        .replace('${order_id}', testData[i]['order_id'])
                                                        .replace('${id}', testData[i]['id'])
                                                        .replace('${status}', testData[i]['status'])
                                                        .replace('${status}', testData[i]['status'])
                                                        .replace('${status}', testStatus[testData[i]['status']])
                                                        .replace('${order_date}', testData[i]['order_date'])
                                                        .replace('${products}', productsTest)
                                                        .replace('${total_price}', totalPriceTest)
                                                        .replace('${complete_date}', testData[i]['complete_date']);
        
        




    }  
}

function searchOrder() {
   let x =  sendRequestGET('http://localhost/api/ordervendors.php?vendor_id=' + vendor_id + "&search=order_id:" + document.getElementById('search').value);
    console.log(x);
}