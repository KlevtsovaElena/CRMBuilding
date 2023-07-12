console.log('подключили list-orders');

// определим имеющиеся статусы
let orderStatus = {
    "0": "Новый",
    "1": "Просмотрен",
    "2": "Подтверждён",
    "3": "Отменён",
    "4": "Доставлен"
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

// определим основные переменные
let currentPage = 1;
let vendor_id = document.getElementById('vendor_id').value;
let url = 'http://localhost/api/order-vendors/get-count-with-details.php?vendor_id=' + vendor_id;

let searchEl = document.getElementById('search');
let limitEl = document.getElementById('limit');
let statusEl = document.getElementById('status');
let archiveCheckEl = document.querySelector('.archive-check');
let archiveEl = archiveCheckEl.querySelector('input');

let offsetEl = containerPagination.getAttribute('offset');







let prevButton;
let nextButton;
let totalPages;


let orderby = "";
let filters = "";
let limitParams = "";
let params = "";

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
    getOrdersData(params);

    // 3. отрисуем пагинацию
    renderPagination(totalOrdersCount, limit);

    // 4. отрисуем таблицу с данными
    renderListOrders(orders);

}


/* ---------- СБОР ПАРАМЕТРОВ запроса---------- */
function getParams() {

    // сначала фильтры 
    filters = getFilters();

    // теперь проверим как у нас с сортировкой
    // ищем в каждом заголовке по атрибуту data-sort
    // 1. если нет поля сортировки, то сортируем только по дате desc
    // 2. если есть поле, то сначала по полю, потом по дате desc
    // 3. если есть поле и это дата, то сортируем только по этому полю
    for (let i = 0; i < headTableOrders.length; i++) {

        if (headTableOrders[i].getAttribute('data-sort')) {
            if(headTableOrders[i].getAttribute('data-id') === 'order_date'){
                orderby = "&orderby=order_date:" + headTableOrders[i].getAttribute('data-sort');  
                break;
            } else {
                orderby = "&orderby=" + headTableOrders[i].getAttribute('data-id') + ":" + headTableOrders[i].getAttribute('data-sort') + ";order_date:desc";
                break; 
            }
        }
    }

    if(!orderby) {
        orderby = "&orderby=order_date:desc";
    }

    // добавим лимит
    limit = limitEl.value;
    limitParams = "&limit=" + limit + "&offset=" + offset
    params = filters + orderby + limitParams;

    return params;
}

function getFilters() {

    // сбросим параметры строки запроса
    params = "";

    if(statusEl.value) {
        params += "&status=" + statusEl.value;
    }
    // проверим значение поиска
    if(searchEl.value.trim()) {
        params += "&search=order_id:" + searchEl.value;
    }
    if(archiveEl.value) {
        params += "&archive=" + archiveEl.value;
    }

console.log(params);
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

        // 5. делаем снова запрос на получение другого куска данных
        getOrdersData(params);

}


/* ---------- ПОЛУЧЕНИЕ ДАННЫХ ИЗ БД ---------- */
function getOrdersData(params) {

    // сделаем запрос с параметрами, запишем данные в переменную ordersJson
    ordersJson = sendRequestGET(url + params);

    if (ordersJson) {
        orders = JSON.parse(ordersJson);
    } else {
        orders = {
            'count': 0,
            'orders': []
        };
    }

    // количество записей в базе по указанным параметрам
    totalOrdersCount = orders['count'];
console.log('всего ' + totalOrdersCount + ' выборка ' + orders['orders'].length);
    // если записей с таким offset нет, но в бд записи есть, то переделаем запрос с иным offset 
    if (orders['orders'].length === 0 && totalOrdersCount > 0) {
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

    // количество записей
    let records = orders['orders'].length;

    // если записей вообще нет                
    if (records === 0) {
        info.innerText = "Записей нет";
        // сбросим офсет
        offset = 0;
        return;
    }
    
    // если лимит установлен и он меньше кол-ва записей, то records = limit
    // иначе выводим всё records = orders.length
    if ((limit) && (limit < orders.length)) {
        records = limit; 
    } 

    // собираем данные и отрисовываем в таблице
    for (let i = 0; i < records; i++) {

        let products = "";
        let totalPrice = 0; 
        let productName = "";
        
        // соберём данные заказанных товаров и общую стоимость заказа
        // for (let j = 0; j < orders['orders'][i]['products'].length; j++){
        //     // если вдруг товар в базе не найден,
        //     // т.е. его названия нет в ответе, то установим name="Товар не найден в базе "
        //     if (!orders['orders'][i]['products'][j]['name']) {
        //         productName = "Товар в базе не найден";
        //     } else {
        //         productName = orders['orders'][i]['products'][j]['name'];
        //     }
        //     products += productName + " (" + 
        //                     (orders['orders'][i]['products'][j]['quantity']) + '), ';
        //                     // если вдруг товар в базе не найден,
        //                     // т.е. его цены нет в ответе, то мы её не учитываем
        //                     if (orders['orders'][i]['products'][j]['price']) {
        //                         totalPrice += orders['orders'][i]['products'][j]['quantity'] * orders['orders'][i]['products'][j]['price'];
        //                     }

        // }


        for (let j = 0; j < orders['orders'][i]['products'].length; j++){

            productName = orders['orders'][i]['products'][j]['name'];
            
            products += orders['orders'][i]['products'][j]['name'] + " (" + 
                            (orders['orders'][i]['products'][j]['quantity']) + '), ';
                            totalPrice += orders['orders'][i]['products'][j]['quantity'] * orders['orders'][i]['products'][j]['price'];
                            
        }


        products = products.slice(0, -2);

        // отформатируем дату
   
        let dateTimeOrder = new Date(orders['orders'][i]['order_date'] * 1000);
        //.slice(0, -3) просто обрезает 3 последних символа. Таким образом, получаем время без секунд
        let timeOrder = dateTimeOrder.toLocaleTimeString().slice(0, -3);
        //преобразуем дату с сервера в дату, которая у пользователя
        let dateOrder = dateTimeOrder.toLocaleDateString();

        // заполним шаблон
        containerListOrders.innerHTML += tmplRowOrder.replace('${order_id}', orders['orders'][i]['order_id'])
                                                        .replace('${order_id}', orders['orders'][i]['order_id'])
                                                        .replace('${order_id}', orders['orders'][i]['order_id'])
                                                        .replace('${id}', orders['orders'][i]['id'])
                                                        .replace('${id}', orders['orders'][i]['id'])
                                                        .replace('${id}', orders['orders'][i]['id'])
                                                        .replace('${status}', orders['orders'][i]['status'])
                                                        .replace('${status}', orders['orders'][i]['status'])
                                                        .replace('${status}', orderStatus[orders['orders'][i]['status']])
                                                        .replace('${order_date}', dateOrder + ' ' + timeOrder)
                                                        .replace('${products}', products)
                                                        .replace('${customer_phone}', orders['orders'][i]['customer_phone'])
                                                        .replace('${customer_id}', "id")
                                                        .replace('${total_price}', totalPrice.toLocaleString('ru'))
                                                        .replace('${complete_date}', '')
                                                        .replace('${archive}', orders['orders'][i]['archive']);
                                                        
        
    }  

    info.innerText = "Всего " + totalOrdersCount + declinationWord(totalOrdersCount, [' запись', ' записи', ' записей']);

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

}


/* ---------- ПЕРЕКЛЮЧЕНИЕ СТРАНИЧЕК ---------- */
function switchPage(variance) {

    // 1. поменяем номер странички
    currentPage = currentPage + variance;

    // и офсет
    offset = (currentPage - 1) * limit;
   
    // отрисуем страничку
    startRenderPage();

}


/* ---------- НАЖАТИЕ НА ИМЯ ЗАГОЛОВКА ТАБЛИЦЫ (СОРТИРОВКА по одному ключу) ---------- */
function sortChange() {

    // получим значение атрибута data-sort
    let dataSort = event.target.getAttribute('data-sort');

    if (!dataSort) {

        // если атрибут пуст,
        // то всем заголовкам устанавливаем пустое значение этого атрибута
        headTableOrders.forEach(item => {
            item.setAttribute('data-sort', '');
        })

        // а заголовку, по кот кликнули, устанавливаем asc
        event.target.setAttribute('data-sort', 'asc');

    } else if (dataSort === "asc") {
        // если значение атрибута asc, то меняем его на desc
        event.target.setAttribute('data-sort', 'desc');

    } else if (dataSort === "desc") {
        // если значение атрибута desc, то меняем его на asc
        event.target.setAttribute('data-sort', 'asc');
    }

    // отрисуем страничку
    startRenderPage();
}

// отслеживаем клик по заголовку
headTableOrders.forEach(item => {
    item.addEventListener("click", sortChange);
})


/* ---------- НАЖАТИЕ НА ПРИМЕНИТЬ (Выборка по фильтрам) ---------- */
const sendChangeData = document.querySelector('.form-filters').querySelector('button');

function applyFilters() {

    // сбрасываем нумерацию страниц и офсет
    currentPage = 1;
    offset = 0;

    // заполним страницу данными
    startRenderPage();

}
sendChangeData.addEventListener("click", applyFilters);


/* ---------- ПЕРЕДАЧА ПАРАМЕТРОВ ФИЛЬТРАЦИИ НА ДРУГУЮ СТРАНИЦУ---------- */
function showOrder(id) {

    // заменяем в истории браузера стр на стр с get параметрами
    // для того, чтобы при переходе по кнопке НАЗАД мы увидели контент по параметрам
    history.replaceState(history.length, null, 'vendor-list-orders.php?vendor_id=' + vendor_id + params);

    // при переходе на страницу редактирования товара передаём ещё и параметры фильтрации в get
        document.location.href = "http://localhost/pages/vendor-order.php?id=" + id + params ; 

}


archiveCheckEl.onclick = function(){
    if(archiveEl.checked) {
        console.log("нажат");
        archiveEl.value = ""
    } else {
        console.log("не нажат");
        archiveEl.value = "archive=0";  
    }
}