console.log('подключили list-orders', mainUrl);

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

// определим разницу временной зоны с UTC в миллисекундах (изначально в минутах)
let timeZoneDiff = new Date().getTimezoneOffset() * 60 * 1000;
// данные календаря
let dateFromEl = document.getElementById('date_from');
let dateTillEl = document.getElementById('date_till');
// если в гет параметре были данные, то нужно из формата юникс перевести в yyyy-mm-dd и отобразить в календаре
if (dateFromEl.getAttribute('order-date') > 0) {
    let dateFromCalendar = new Date(dateFromEl.getAttribute('order-date') * 1000).toLocaleDateString().split('.');
    dateFromEl.value = dateFromCalendar[2] + '-' + dateFromCalendar[1] + '-' + dateFromCalendar[0];
} 
if (dateTillEl.getAttribute('order-date') > 0) {
    let dateTillCalendar = new Date(dateTillEl.getAttribute('order-date') * 1000).toLocaleDateString().split('.');
    dateTillEl.value = dateTillCalendar[2] + '-' + dateTillCalendar[1] + '-' + dateTillCalendar[0];
} 

// определим основные переменные
let currentPage = 1;
let vendor_id = document.getElementById('vendor_id').value;
let url = mainUrl + '/api/order-vendors/get-count-with-details.php?vendor_id=' + vendor_id;

let searchEl = document.getElementById('search');
let limitEl = document.getElementById('limit');
let statusEl = document.getElementById('status');
let archiveCheckEl = document.querySelector('.archive-check');
let archiveEl = archiveCheckEl.querySelector('input');

let offsetEl = containerPagination.getAttribute('offset');

let changeOrderEl;
let resetOrderEl;
let saveOrderEl;
let changeSelectEl;

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
        if(statusEl.value !== "archive=1") {
            params += "&status=" + statusEl.value;
            if(archiveEl.value) {
                params += "&" + archiveEl.value;
            }
        } else if(statusEl.value === "archive=1") {
            params += "&" + statusEl.value;
        }
    }  else if(archiveEl.value){
        params += "&" + archiveEl.value;
    }

    // проверим значение поиска
    if(searchEl.value.trim()) {
        params += "&search=order_id:" + searchEl.value;
    }

    // проверим значения дат (начало и конец интервала)

    // начало интервала
    if(dateFromEl.value) {

        // 1. переведём дату из календаря в юникс и получим дату со сдвигом,
        // т.е. если перевести, получим не 01.01.23 00:00:00,  а локальную 01.01.23 03:00:00
        // а нам нужно, чтобы локальная была 00:00:00 
        // 2. для этого добавим разницу во времени timeZoneDiff в миллисекундах (высчитано в начале файла)
        // и получим в результате что надо, эквивалентно 01.01.23 00:00:00 по локали
        // 3. для передачи на сервер убираем миллисекунды и передаём в юникс формате в минутах, как у нас в базе (делим на 1000)

        params += "&date_from=" + ((Date.parse(dateFromEl.value) + timeZoneDiff) / 1000);
    } 

    // конец интервала
    if(dateTillEl.value) {

        // 1 и 3 аналогично началу интервала
        // 2 помимо timeZoneDif мы прибавляем 24 часа к дате и отнимаем 1 секунду (24*60*60*1000 - 1000)
        // и получим в результате что надо, эквивалентно 01.01.23 23:59:59 по локали 

        params += "&date_till=" + ((Date.parse(dateTillEl.value) + timeZoneDiff + 24*60*60*1000 - 1000) / 1000);

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

        for (let j = 0; j < orders['orders'][i]['products'].length; j++){

            productName = orders['orders'][i]['products'][j]['name'];
            
            products += orders['orders'][i]['products'][j]['name'] + " (" + 
                            (orders['orders'][i]['products'][j]['quantity']) + '), ';

                            
        }

        products = products.slice(0, -2);

        // отформатируем дату
   
        let dateTimeOrder = new Date(orders['orders'][i]['order_date'] * 1000);
        //.slice(0, -3) просто обрезает 3 последних символа. Таким образом, получаем время без секунд
        //let timeOrder = dateTimeOrder.toLocaleTimeString().slice(0, -3);

        //преобразуем дату с сервера в дату, которая у пользователя
        let dateOrder = dateTimeOrder.toLocaleDateString();

        let archiveStatus = "";
        let archiveText = "";
        if(orders['orders'][i]['archive'] == '1') {
            archiveStatus = "archive=0";
            archiveText = "Убрать из архива";
        } else {
            archiveStatus = "archive=1";
            archiveText = "В архив";
        }

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
                                                        .replace('${order_date}', dateOrder)
                                                        // .replace('${order_date}', dateOrder + ' ' + timeOrder)
                                                        .replace('${products}', products)
                                                        .replace('${customer_phone}', orders['orders'][i]['customer_phone'])
                                                        .replace('${customer_id}', orders['orders'][i]['customer_id'])
                                                        .replace('${total_price}',  orders['orders'][i]['total_price'].toLocaleString('ru'))
                                                        .replace('${distance}',  orders['orders'][i]['distance'])
                                                        .replace('${archive}', orders['orders'][i]['archive'])
                                                        .replace('${archive_status}', archiveStatus)
                                                        .replace('${archive_text}', archiveText);
                                                        
        
    }  

    info.innerText = "Всего " + totalOrdersCount + declinationWord(totalOrdersCount, [' запись', ' записи', ' записей']);

    // здесь включаем прослушку элементов после отрисовки
    // ДО отрисовки код не сработает, поэтому этот код внутри этой функции
    changeOrderEl = document.querySelectorAll('.list-orders_status');
    changeSelectEl = document.querySelectorAll('.change-status');


    resetOrderEl = document.querySelectorAll('.reset-order');
    saveOrderEl = document.querySelectorAll('.save-order');

    changeOrderEl.forEach(item => {
        item.addEventListener('click', showChangeSelect);
    })

    resetOrderEl.forEach(item => {
        item.addEventListener('click', resetChangeOrder);
    })

    saveOrderEl.forEach(item => {
        item.addEventListener('click', saveChangeOrder);
    })
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

    // проверяем корректность токена
    check();

    // 1. поменяем номер странички
    currentPage = currentPage + variance;

    // и офсет
    offset = (currentPage - 1) * limit;
   
    // отрисуем страничку
    startRenderPage();

}


/* ---------- НАЖАТИЕ НА ИМЯ ЗАГОЛОВКА ТАБЛИЦЫ (СОРТИРОВКА по одному ключу) ---------- */
function sortChange() {

    // проверяем корректность токена
    check();

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

    // проверяем корректность токена
    check();

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
    window.location.href = mainUrl + "/pages/vendor-order.php?id=" + id + params ; 

}


/* ---------- ИЗМЕНЕНИЕ статуса или архива---------- */
// при нажатии на чекбокс, меняем value чекбокса
archiveCheckEl.onclick = function(){
    if(archiveEl.checked) {
        archiveEl.value = ""
    } else {
        archiveEl.value = "archive=0";  
    }
}

// показать селекты
function showChangeSelect() {

    let rowOrder = event.target.closest('.list-orders__row');

    // общий контейнер селекта
    let changeStatus = rowOrder.querySelector('.change-status');

    // все остальные селекты скрыть
    changeSelectEl.forEach(item => {
        if (item !== changeStatus) {
            item.classList.add('d-none');
        }
    })

    changeStatus.classList.toggle('d-none');

}

// сбросить изменения без сохранения
function resetChangeOrder() {
    // вся строка заказа
    let rowOrder = event.target.closest('.list-orders__row');

    // общий контейнер селекта
    let changeStatus = rowOrder.querySelector('.change-status');

    changeStatus.classList.add('d-none');
}

// сохранить изменения
function saveChangeOrder() {
    
    // проверяем корректность токена
    check();

    // вся строка заказа
    let rowOrder = event.target.closest('.list-orders__row');

    // сам селект
    let changeOrderSelect = rowOrder.querySelector('.change-order-select');

    // id заказа, который надо изменить
    let idOrder = rowOrder.getAttribute('order-id');

    let obj = {};
    obj['id'] = idOrder;
    obj[changeOrderSelect.value.split('=')[0]] = changeOrderSelect.value.split('=')[1];
    let objJson = JSON.stringify(obj);

    // меняем статус в базе
    sendRequestPOST(mainUrl + '/api/ordervendors.php', objJson);

    // если статус был 0, то пересчитаем кол-во новых заказов и отрисуем новую цифру
    if (rowOrder.classList.contains('row-status0')) {
        changeCountNewOrders();
    } 

    // перерисовка страницы
    startRenderPage();

}

// получение кол-ва новых заказов и отрисовка
function changeCountNewOrders() {
    // делаем запрос на получение количества новых заказов поставщика
    let countNewOrders = sendRequestGET(mainUrl + '/api/order-vendors/get-count.php?status=0&vendor_id=' + vendor_id);
    countNewOrders = JSON.parse(countNewOrders);

    let newOrdersContainer = document.getElementById('counter');

    // если новых заказов нет, то скрываем кружок
    if (countNewOrders['count'] == 0) {
        newOrdersContainer.classList.add('d-none');
    } else {
        // иначе перерисуем кол-во заказов в кружочке
        newOrdersContainer.innerText = countNewOrders['count'];
        newOrdersContainer.classList.remove('d-none');
    }

}