console.log('подключили list-orders');

// определим основные переменные
let searchEl = document.getElementById('search');
let limitEl = document.getElementById('limit');
let statusEl = document.getElementById('status');
let currentPageEl = document.querySelectorAll('.current-page');
let totalPagesEl = document.querySelector('.total-page');
const headTableOrders = document.getElementById('list-orders').querySelectorAll('[data-sort]');

let vendor_id = document.getElementById('vendor_id').value;
let limit = limitEl.value;
let offset = document.querySelector('.pagination-wrapper').getAttribute('offset');


let currentPage = Number(currentPageEl[0].innerText);
let totalPages = Number(totalPagesEl.innerText);
let totalOrdersCount = Number(document.querySelector('.total-orders').innerText); 

let orderby = "";
let filters = "";
let limitParams = "";
let params = "";

let url = 'http://localhost/pages/vendor-list-orders.php?vendor_id=' + vendor_id;

// возможность/невозможность переключения страниц
pageSwitch();
function pageSwitch() {
    let prevButton = document.querySelector('.page-switch__prev');
    let nextButton = document.querySelector('.page-switch__next');

    // настроим возможность/невозможность переключения страниц 
    if (currentPage == 1) {
        prevButton.setAttribute('disabled', '');
        if (totalPages > 1) {
             nextButton.removeAttribute('disabled');
        }
     } else if (currentPage == totalPages) {
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
   
    // перезагружаем страничку
    params = getParams();

    window.location.href = url + params ; 

}


/* ---------- НАЖАТИЕ НА ПРИМЕНИТЬ (Выборка по фильтрам) ---------- */
const sendChangeData = document.querySelector('.form-filters').querySelector('button');

function applyFilters() {

    // сбрасываем нумерацию страниц и офсет
    currentPage = 1;
    offset = 0;

    // перезагружаем страничку
    params = getParams();

    window.location.href = url + params ; 

}
sendChangeData.addEventListener("click", applyFilters);

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

    // перезагружаем страничку
    params = getParams();

    window.location.href = url + params ;
}

// отслеживаем клик по заголовку
headTableOrders.forEach(item => {
    item.addEventListener("click", sortChange);
})


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

    return params;
}

/* ---------- ПЕРЕДАЧА ПАРАМЕТРОВ ФИЛЬТРАЦИИ НА ДРУГУЮ СТРАНИЦУ---------- */
function showOrder(id) {

    // соберём параметры
    params = getParams();

    // заменяем в истории браузера стр на стр с get параметрами
    // для того, чтобы при переходе по кнопке НАЗАД мы увидели контент по параметрам
    history.replaceState(history.length, null, 'vendor-list-products.php?vendor_id=' + vendor_id + params);

    // при переходе на страницу редактирования товара передаём ещё и параметры фильтрации в get
    window.location.href = "http://localhost/pages/vendor-edit-product.php?id=" + id + "&vendor_id=" + vendor_id + params ; 
}



/* ---------- ПЕРЕДАЧА ПАРАМЕТРОВ ФИЛЬТРАЦИИ НА ДРУГУЮ СТРАНИЦУ---------- */
function showOrder(id) {

    // соберём параметры
    params = getParams();

    // заменяем в истории браузера стр на стр с get параметрами
    // для того, чтобы при переходе по кнопке НАЗАД мы увидели контент по параметрам
    history.replaceState(history.length, null, 'vendor-list-orders.php?vendor_id=' + vendor_id + params);

    // при переходе на страницу заказа передаём ещё и параметры фильтрации в get
    document.location.href = "http://localhost/pages/vendor-order.php?id=" + id + "&vendor_id=" + vendor_id + params ; 

}


/* ---------- ФОРМАТИРОВАНИЕ ДАТЫ ---------- */
let orderDate = document.querySelectorAll('[order-date]');
orderDate.forEach(item => {
    let dateTimeOrder = new Date(item.getAttribute('order-date') * 1000);
    //.slice(0, -3) просто обрезает 3 последних символа. Таким образом, получаем время без секунд
    let timeOrder = dateTimeOrder.toLocaleTimeString().slice(0, -3);
    //преобразуем дату с сервера в дату, которая у пользователя
    let dateOrder = dateTimeOrder.toLocaleDateString();

    item.innerText = dateOrder + ' ' + timeOrder;
})
