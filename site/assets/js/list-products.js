console.log('подключили list-products');


// определим основные переменные
let currentPage = 1;
let params;
let prevButton;
let nextButton;
let totalPages;
let brands = {};
let categories = {};
let orderby = "";
let offset;
let filters="";
let vendor_id = document.getElementById('vendor_id').value;
let brand_idEl = document.getElementById('brand_id');
let category_idEl = document.getElementById('category_id');
let searchEl = document.getElementById('search');
let limitEl = document.getElementById('limit');
let limit = limitEl.value;
let totalProducts = [];

// найдём шаблон и контейнер для отрисовки товаров
const tmplRowProduct = document.getElementById('template-body-table').innerHTML;
const containerListProducts = document.querySelector('.list-products__body');
// найдём шаблон и контейнер для отрисовки пагинации
const tmplPagination = document.getElementById('template-pagination').innerHTML;
const containerPagination = document.querySelector('.pagination-wrapper');
// контейнер информации внизу страницы
const info = document.querySelector('.info-table');

// закэшируем брендов и категорий
brand_idEl.querySelectorAll('option').forEach(item => {
    brands[item.value] = item.innerText;
})
category_idEl.querySelectorAll('option').forEach(item => {
    categories[item.value] = item.innerText;
})

// получение записей товаров из БД
let url = 'http://localhost/api/products.php?vendor_id=' + vendor_id;
console.log(url);
let totalProductsJson = sendRequestGET(url);
if (totalProductsJson) {
    totalProducts = JSON.parse(totalProductsJson);
}

// подсчёт полученных записей
let totalProductsCount = totalProducts.length;
console.log(totalProductsCount);
// отрисуем пагинацию
renderPagination(totalProductsCount, limit);

// отрисуем товары в таблице 
renderListProducts(totalProducts);


/* ---------- ОТРИСОВКА ТОВАРОВ В ТАБЛИЦЕ---------- */
function renderListProducts(totalProducts) {

    // очистим контейнер
    containerListProducts.innerHTML = "";

    // если записей нет, то выводим об этом инфо и выходим
    if (totalProducts.length === 0) {
        info.innerText = "Записей нет";
        return;
    }

    // количество показываемых записей на странице
    let records;
    
    // если лимит установлен и он меньше кол-ва записей, то records = limit
    // иначе выводим всё records = totalProducts.length
    if ((limit) && (limit < totalProducts.length)) {
        records = limit; 
    } else {
        records = totalProducts.length;
    }

    console.log("покажем" + records);
    // заполним данными и отрисуем шаблон
    for (i = 0; i < records; i++) {
        containerListProducts.innerHTML += tmplRowProduct.replace('${article}', totalProducts[i]['article'])
                                                        .replace('${photo}',  totalProducts[i]['photo'])
                                                        .replace('${name}', totalProducts[i]['name'])
                                                        .replace('${brand_id}', brands[totalProducts[i]['brand_id']])
                                                        .replace('${category_id}', categories[totalProducts[i]['category_id']])
                                                        .replace('${quantity_available}', totalProducts[i]['quantity_available'])
                                                        .replace('${price}', totalProducts[i]['price'])
                                                        .replace('${id}', totalProducts[i]['id'])
                                                        .replace('${id}', totalProducts[i]['id'])
                                                        .replace('${id}', totalProducts[i]['id'])
                                                        .replace('${max_price}', totalProducts[i]['max_price']);
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


/* ---------- ПЕРЕКЛЮЧЕНИЕ СТРАНИЧЕК ---------- */
function switchPage(variance) {
    // сброс инфы внизу страницы
    info.innerText = "";

    // 1. поменяем номер странички
    currentPage = currentPage + variance;

    let containerCurrentPage = document.querySelectorAll('.current-page');
    containerCurrentPage.forEach(item => {
        item.innerText = currentPage;
    })
    console.log("totalPages ",  totalPages, "currentPage ",  currentPage);
    // 2. настроим возможность/невозможность переключения страниц 
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
   
    // соберём строку запроса
    params = "";

    // если фильтры применялись (была нажата кнопка), то записываем в параметры полученные фильтры
    if (filters) {
        params = filters;
    }

    // если применялась сортировка, то добавляем в параметры
    if (orderby) {
        params += orderby;
    }

    // тк фильтруем мы только по нажатии на кнопку Применить,
    // то при нажатии на перекл стр нам не нужно заново узнавать сколько ВСЕГО данных по фильтрам
    // поэтому просто запрашиваем лимит со смещением

    // добавим лимит и смещение в параметры (лимит будет всегда, если страничек больше 1)
    offset = limit*(currentPage-1);
    params += "&limit=" + limit + "&offset=" + offset; 

    console.log('пагинация ', url+params);


    // отправим запрос
    totalProductsJson = sendRequestGET(url + params);
    if (totalProductsJson) {
        totalProducts = JSON.parse(totalProductsJson);
    } else {
        totalProducts = [];
    }

    // отрисуем таблицу
    renderListProducts(totalProducts);

    // пагинацию перерисовывать не нужно
}


/* ---------- НАЖАТИЕ НА ИМЯ ЗАГОЛОВКА ТАБЛИЦЫ (СОРТИРОВКА по одному ключу) ---------- */

//получаем все элементы заголовка для отслеживания клика
const headTableProducts = document.getElementById('list-products').querySelectorAll('th');

function sortChange() {

    // получим значение атрибута data-sort
    let dataSort = event.target.getAttribute('data-sort');

    if (!dataSort) {

        // если атрибут пуст,
        // то всем заголовкам устанавливаем пустое значение этого атрибута
        headTableProducts.forEach(item => {
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

    // собираем значение для параметра orderby
    orderby = "&orderby=" + event.target.getAttribute('data-id') + ":" + event.target.getAttribute('data-sort');

    // соберём строку запроса
    params = "";

    // если фильтры применялись (была нажата кнопка), то записываем в параметры полученные фильтры
    if (filters) {
        params = filters;
    }

    params += orderby;

    // если есть лимит, добавим лимит
    if (limit) {

        // определим с какой записи брать данные
        offset = limit*(currentPage-1);

        // добавим лимит и смещение в параметры
        params += "&limit=" + limit + "&offset=" + offset; 
        
    } 

console.log('сортировка', url+params);

    // делаем запрос
    totalProductsJson = sendRequestGET(url+params);

    if (totalProductsJson) {
        totalProducts = JSON.parse(totalProductsJson);
    } else {
        totalProducts = [];
    }

// отрисовываем только таблицу (пагинация остаётся прежней)
renderListProducts(totalProducts);

}

// отслеживаем клик по заголовку
headTableProducts.forEach(item => {
    item.addEventListener("click", sortChange);
})


/* ---------- НАЖАТИЕ НА ПРИМЕНИТЬ (Выборка по фильтрам) ---------- */
const sendChangeData = document.querySelector('.form-filters').querySelector('button');

function getChangeDataFilters() {

    // сбрасываем нумерацию страниц
    currentPage = 1;

    // соберём строку запроса     
    filters = getFilters();
    params = filters;

    if(orderby) {
        params += orderby;
    }

    console.log('фильтры ', url+params);

    // будем запрашивать ВСЕ данные, чтобы знать общее количество отфильтрованных данных
    totalProductsJson = sendRequestGET(url+params);

    if (totalProductsJson) {
        totalProducts = JSON.parse(totalProductsJson);
    } else {
        totalProducts = [];
    }


    // подсчёт полученных записей
    let totalProductsCount = totalProducts.length;

    // отрисуем пагинацию
    renderPagination(totalProductsCount, limit);

    // отрисуем таблицу
    renderListProducts(totalProducts);

}
sendChangeData.addEventListener("click", getChangeDataFilters);


/* ---------- СОБЕРЁМ СТРОКУ ЗАПРОСА ФИЛЬТРАЦИИ---------- */
function getFilters() {

    // сбросим параметры строки запроса
    params = "";

    limit = limitEl.value;

    // проверяем на наличие данных, если есть, о нормализуем (если надо)
    // и добавляем в параметр строки запроса 
    [brand_idEl, category_idEl, searchEl].forEach(item => {
        if (item.value.trim()) {
            if  (item.id === 'search') {
                params += "&search=name:" + item.value + ";description:" + item.value;
            } else {
                params += "&" + item.id + "=" + item.value;
            }
        }
    })

    // вернём параметры
    return params;

}



/* ---------- УДАЛЕНИЕ ТОВАРА ---------- */
const garbage = document.querySelectorAll('.garbage');

function deleteProduct() {

    // запрашиваем подтверждение удаления
    let isDelete = false;

    isDelete = window.confirm('Вы действительно хотите удалить этот товар?');

    if(!isDelete) {
        console.log(" ни в коем случае");
        return;
    }

    // если подтвердили удаление
    console.log("удаляем");

    // найдём id товара по атрибуту product-id
    const productId = event.target.closest('.list-products__row').getAttribute('product-id');

    // делаем запрос на удаление товара по id
    sendRequestDELETE('http://localhost/api/products.php?id=' + productId);

    // теперь перерисуем таблицу с учётом удалённого товара

    // то при нажатии на удаление товара нам не нужно заново узнавать сколько ВСЕГО данных по фильтрам
    // но нужно из общего количества удалить 1 
    totalProductsCount = totalProductsCount - 1;

    if (totalProductsCount === 0) {
        info.innerText = "Записей нет";
        // очистим контейнер
        containerListProducts.innerHTML = "";
        return;
    }

    // отрисуем пагинацию
    renderPagination(totalProductsCount, limit);

    // соберём строку запроса
    params = "";

    // если фильтры применялись (была нажата кнопка), то записываем в параметры полученные фильтры
    if (filters) {
        params = filters;
    }

    // если применялась сортировка, то добавляем в параметры
    if (orderby) {
        params += orderby;
    }

    console.log('после удаления элемента (доработать)', url+params);
    // // добавим лимит
    // if (limit) {

    //     // определим с какой записи брать данные
    //     offset = limit*(currentPage-1);

    //     // добавим лимит и смещение в параметры
    //     params += "&limit=" + limit + "&offset=" + offset; 
        
    // } 

    // console.log(params);

}

// отслеживаем клик по корзине
garbage.forEach(item => {
    item.addEventListener("click", deleteProduct);
})







// function test() {
//     let url = 'http://localhost/api/products.php?vendor_id=' + vendor_id;
//     let totalProductsJson = sendRequestGET(url);
//     console.log(JSON.parse(totalProductsJson)); 

//     params="&category_id=4";
//     totalProductsJson = sendRequestGET(url+params);
//     console.log(JSON.parse(totalProductsJson));  

//     params="&category_id=4&search=name:фиолетовый";
//     totalProductsJson = sendRequestGET(url+params);
//     console.log(JSON.parse(totalProductsJson));
    
//     params="&category_id=4&search=name:фиолетовый";
//     totalProductsJson = sendRequestGET(url+params);
//     console.log(url+params);
//     console.log(JSON.parse(totalProductsJson));
// }
// test();
