console.log('подключили list-orders');

// определим имеющиеся статусы
let orderStatus = {
    "0": "Новый",
    "1": "Просмотрен",
    "2": "Подтверждён",
    "3": "Отменён",
    "4": "Звершён",
    "5": "Не дозвонились"
}

// найдём шаблон и контейнер для отрисовки заказов
const containerListOrders = document.querySelector('.list-orders__body');
const tmplRowOrder = document.getElementById('template-body-table').innerHTML;

// найдём шаблон и контейнер для отрисовки пагинации
const tmplPagination = document.getElementById('template-pagination').innerHTML;
const containerPagination = document.querySelector('.pagination-wrapper');

// контейнер информации внизу страницы
const info = document.querySelector('.info-table');
//получаем все элементы заголовка для отслеживания сортировки
const headTableOrders = document.getElementById('list-orders').querySelectorAll('[data-sort]');
console.log(headTableOrders);


// определим основные переменные
let currentPage = 1;
let vendor_id = document.getElementById('vendor_id').value;
let url = 'http://localhost/api/order-vendors/get-with-details.php?vendor_id=' + vendor_id;

let searchEl = document.getElementById('search');
let limitEl = document.getElementById('limit');

let prevButton;
let nextButton;
let totalPages;


let orderby = "";
let filters = "";
let limitParams = "";
let params = "";

let paramsTest = "";

let limit = limitEl.value;
let offset = containerPagination.getAttribute('offset');
if (offset !==0) {
    currentPage = Math.ceil(offset/limit) + 1;
}

let orders = [];
let totalOrdersCount;
let ordersJson;
// заполним страницу данными
startRenderPage();

/* ---------- НАБОР ФУНКЦИЙ ДЛЯ ОТРИСОВКИ СТРАНИЦЫ---------- */
function startRenderPage() {

    // 1. собрать параметры запросы
    params = getParams();

    // 2. получим данные по указанным параметрам из БД
    getOrdersData(params, paramsTest);

    // 3. отрисуем пагинацию
    renderPagination(totalOrdersCount, limit);

    // 4. отрисуем таблицу с данными
    renderListOrders(orders);

}

/* ---------- СБОР ПАРАМЕТРОВ запроса---------- */
function getParams() {

    paramsTest = "";
    // сначала фильтры 
    filters = getFilters();

    console.log("filters", filters);

    // теперь проверим как у нас с сортировкой
    // ищем в каждом заголовке по атрибуту data-sort
    for (let i = 0; i < headTableOrders.length; i++) {

        if (headTableOrders[i].getAttribute('data-sort')) {
            orderby = "&orderby=" + headTableOrders[i].getAttribute('data-id') + ":" + headTableOrders[i].getAttribute('data-sort');
            break; 
        }
    }

    // добавим лимит
    limitParams = "&limit=" + limit + "&offset=" + offset
    params = filters + orderby + limitParams;
    paramsTest = filters + orderby;
    console.log("params += filters + orderby + limitParams;", params);
    return params;
}

function getFilters() {

    // сбросим параметры строки запроса
    params = "";

    // проверим значение поиска
    if(searchEl.value.trim()) {
        params += "&search=order_id:" + searchEl.value;
    }

    return params;
}

/* ---------- ПРОВЕРКА НАЛИЧИЯ ДАННЫХ В ОТВЕТЕ ---------- */
function changeData() {

        // определим какой offset нам взять 
        // 1. определим сколько всего страниц 
        if ((limit) && limit < totalOrdersCount) {
            totalPages = Math.ceil(totalOrdersCount/limit);
        } else {
            totalPages = 1;
        }

        // 2. сделаем текущей страницей последнюю 
        currentPage = totalPages;

        // 3. определим новый offset
        offset = (currentPage - 1) * limit;
        
        // 4. перепишем параметры
        params = filters + orderby + "&limit=" + limit + "&offset=" + offset;
        paramsTest = filters + orderby

        // 5. делаем снова запрос на получение другого куска данных
        getOrdersData(params, paramsTest);

}

/* ---------- ПОЛУЧЕНИЕ ДАННЫХ ИЗ БД ---------- */
function getOrdersData(params, paramsTest) {
    // -----------------------------------------------------------------------
    //кол-во ПОТОМ ПОЛУЧАТЬ ИЗ АПИ
    let test2 = [];
    let test = sendRequestGET(url + paramsTest);
    if (test) {
        test2 = JSON.parse(test);
    } else {
        test2 = [];
    }

    // подсчёт полученных записей
    totalOrdersCount = test2.length;
    console.log(totalOrdersCount);

    // -------------------------------------------------------------------

    // сделаем запрос с параметрами, запишем данные в переменную orders
    ordersJson = sendRequestGET(url + params);

    if (ordersJson) {
        orders = JSON.parse(ordersJson);
    } else {
        orders = [];
    }
console.log(orders);
    // если записей с таким offset нет, но в бд записи есть, то переделаем запрос с иным offset 
    if (orders.length === 0 && totalOrdersCount > 0) {
        changeData();
        return;
    }

}

/* ---------- ОТРИСОВКА ТОВАРОВ В ТАБЛИЦЕ---------- */
function renderListOrders(orders) {


    // очистим контейнер
    containerListOrders.innerHTML = "";   
    // сброс инфы внизу страницы
    info.innerText = "";

    // если записей вообще нет                
    if (orders.length === 0) {
        info.innerText = "Записей нет";
        // сбросим офсет
        offset = 0;
        return;
    }

    // количество показываемых записей на странице
    let records;
    
    // если лимит установлен и он меньше кол-ва записей, то records = limit
    // иначе выводим всё records = orders.length
    if ((limit) && (limit < orders.length)) {
        records = limit; 
    } else {
        records = orders.length;
    }


    // собираем данные и отрисовываем в таблице
    for (let i = records - 1; i >= 0; i--) {

        let products = "";
        let totalPrice = 0; 

        // соберём данные заказанных товаров и общую стоимость заказа
        for (let j = 0; j < orders[i]['products'].length; j++){
            products += orders[i]['products'][j]['name'] + " (" + 
                            (orders[i]['products'][j]['quantity']) + '), ';
                            totalPrice = orders[i]['products'][j]['quantity'] * orders[i]['products'][j]['price'];

        }

        products = products.slice(0, -2);

        // отформатируем дату
   
        let dateTimeOrder = new Date(orders[i]['order_date'] * 1000);
        //.slice(0, -3) просто обрезает 3 последних символа. Таким образом, получаем время без секунд
        let timeOrder = dateTimeOrder.toLocaleTimeString().slice(0, -3);
        //преобразуем дату с сервера в дату, которая у пользователя
        let dateOrder = dateTimeOrder.toLocaleDateString();

        // заполним шаблон
        containerListOrders.innerHTML += tmplRowOrder.replace('${order_id}', orders[i]['order_id'])
                                                        .replace('${order_id}', orders[i]['order_id'])
                                                        .replace('${id}', orders[i]['id'])
                                                        .replace('${status}', orders[i]['status'])
                                                        .replace('${status}', orders[i]['status'])
                                                        .replace('${status}', orderStatus[orders[i]['status']])
                                                        .replace('${order_date}', dateOrder + ' ' + timeOrder)
                                                        .replace('${products}', products)
                                                        .replace('${total_price}', totalPrice.toLocaleString('ru'))
                                                        .replace('${complete_date}', '');
        
    }  

}

/* ---------- ОТРИСОВКА ПАГИНАЦИИ ---------- */
function renderPagination(totalOrdersCount, limit) {

    // из полученных переменных получаем кол-во страниц
    if ((limit) && limit < totalOrdersCount) {
        totalPages = Math.ceil(totalOrdersCount/limit);
    } else {
        totalPages = 1;
    }

    // если текущая страница больше,чем всего страниц, то текущей делаем последнюю
    if (currentPage > totalPages) {
        currentPage = totalPages;
    }

    // очистим контейнер
    containerPagination.innerHTML = "";

    // заполним данными и отрисуем шаблон
    containerPagination.innerHTML = tmplPagination  .replace('${currentPage}', currentPage)
                                                    .replace('${currentPage}', currentPage)
                                                    .replace('${totalPages}', totalPages);

    prevButton = document.querySelector('.page-switch__prev');
    nextButton = document.querySelector('.page-switch__next');

    // настроим возможность/невозможность переключения страниц 
    if (currentPage === 1) {
        prevButton.setAttribute('disabled', '');
        if (totalPages > 1) {
            nextButton.removeAttribute('disabled');
        }
    } else if (currentPage === totalPages) {
        prevButton.removeAttribute('disabled');
        nextButton.setAttribute('disabled', '');
    } else {
        prevButton.removeAttribute('disabled');
        nextButton.removeAttribute('disabled');
    }

    console.log('totalPages', totalPages);
}

// тестовая
function searchOrder() {
   let x =  sendRequestGET('http://localhost/api/ordervendors.php?vendor_id=' + vendor_id + "&search=order_id:" + document.getElementById('search').value);
    console.log(x);
}