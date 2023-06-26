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
                                                        .replace('${order_date}', "")
                                                        .replace('${products}', productsListInOrder)
                                                        .replace('${total_price}', "")
                                                        .replace('${complete_date}', "")

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
