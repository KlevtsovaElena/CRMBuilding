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
let hasFilters="";
let vendor_id = document.getElementById('vendor_id').value;
let brand_idEl = document.getElementById('brand_id');
let category_idEl = document.getElementById('category_id');
let searchEl = document.getElementById('search');
let limitEl = document.getElementById('limit');
let limit = limitEl.value
// найдём шаблон и контейнер для отрисовки
const tmplRowProduct = document.getElementById('template-body-table').innerHTML;
const containerListProducts = document.querySelector('.list-products__body');
// найдём шаблон и контейнер для отрисовки
const tmplPagination = document.getElementById('template-pagination').innerHTML;
const containerPagination = document.querySelector('.pagination-wrapper');


// соберём значения брендов и категорий
brand_idEl.querySelectorAll('option').forEach(item => {
    brands[item.value] = item.innerText;
})
category_idEl.querySelectorAll('option').forEach(item => {
    categories[item.value] = item.innerText;
})

// получение записей товаров из БД
let url = 'http://localhost/api/products.php?vendor_id=' + vendor_id;
let totalProductsJson = sendRequestGET(url);
let totalProducts = JSON.parse(totalProductsJson);

// подсчёт полученных записей
let totalProductsCount = totalProducts.length;

// отрисуем пагинацию
renderPagination(totalProductsCount, limit);

// отрисуем товары в таблице 
renderListProducts(totalProducts);


/* ---------- ОТРИСОВКА ТОВАРОВ В ТАБЛИЦЕ---------- */
function renderListProducts(totalProducts) {
    let records = totalProducts.length;


    // очистим контейнер
    containerListProducts.innerHTML = "";

    // если записей нет, то выводим об этом инфо и выходим
    if (totalProducts.length === 0) {
        const info = document.querySelector('.info-table');
        info.innerText = "Записей нет";
        return;
    }

    // заполним данными и отрисуем шаблон
    if ((limit) && (limit < records)) { records = limit; }

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

    // 1. поменяем номер странички
    currentPage = currentPage + variance;

    let containerCurrentPage = document.querySelectorAll('.current-page');
    containerCurrentPage.forEach(item => {
        item.innerText = currentPage;
    })

    // 2. настроим возможность/невозможность переключения страниц 
    if (currentPage === 1) {
       prevButton.setAttribute('disabled', '');
       if (totalPages > 1) {
            nextButton.removeAttribute('disabled');
       }
    } else if (currentPage > 1) {
        prevButton.removeAttribute('disabled');

        if (currentPage === totalPages) {
            nextButton.setAttribute('disabled', '');
        }
    }
   
    // соберём строку запроса
    params = "";

    // если фильтры применялись (была нажата кнопка), то записываем в параметры полученные фильтры
    if (hasFilters) {
        params = hasFilters;
    }

    // если применялась сортировка, то добавляем в параметры
    if (orderby) {
        params += "&orderby=" + orderby;
    }

    // тк фильтруем мы только по нажатии на кнопку Применить,
    // то при нажатии на перекл стр нам не нужно заново узнавать сколько ВСЕГО данных по фильтрам
    // поэтому просто запрашиваем лимит со смещением

    // добавим лимит и смещение в параметры (лимит будет всегда, если страничек больше 1)
    offset = limit*(currentPage-1) + 1;
    params += "&limit=" + limit + "&offset=" + offset; 

    console.log('пагинация ', url+params);


// пагинацию перерисовыать не нужно

    // // отправим запрос
    // totalProductsJson = sendRequestGET('http://localhost/api/products.php?vendor_id=' + vendor_id + params);
    // totalProducts = JSON.parse(totalProductsJson);

    // // отрисуем таблицу
    // renderListProducts(totalProducts)
}


/* ---------- НАЖАТИЕ НА ИМЯ ЗАГОЛОВКА ТАБЛИЦЫ (СОРТИРОВКА) ---------- */

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
    orderby = event.target.getAttribute('data-id') + ":" + event.target.getAttribute('data-sort');
console.log(orderby);
    // соберём строку запроса
    params = "";

    // если фильтры применялись (была нажата кнопка), то записываем в параметры полученные фильтры
    if (hasFilters) {
        params = hasFilters;
    }

    params += "&orderby=" + orderby;

    // добавим лимит
    if (limit) {

        // определим с какой записи брать данные
        offset = limit*(currentPage-1) + 1;

        // добавим лимит и смещение в параметры
        params += "&limit=" + limit + "&offset=" + offset; 
        
    } 

console.log('сортировка', url+params);

// делаем запрос
totalProductsJson = sendRequestGET(url+params);
totalProducts = JSON.parse(totalProductsJson);

// отрисовываем только таблицу (пагинация остаётся прежней)
renderListProducts(totalProducts);



}

// отслеживаем клик по заголовку
headTableProducts.forEach(item => {
    item.addEventListener("click", sortChange);
})


/* ---------- НАЖАТИЕ НА ПРИМЕНИТЬ ---------- */
const sendChangeData = document.querySelector('.form-filters').querySelector('button');

function getChangeDataFilters() {

    // соберём строку запроса     
    hasFilters = getFilters();
    params = hasFilters;


    if (orderby) {
        params += "&orderby=" + orderby;
    }

    console.log('фильры ', url+params);
    // сбрасываем нумерацию страниц
    currentPage = 1;

    // будем запрашивать ВСЕ данные, чтобы знать общее количество отфильтрованных данных

    // вызываем отрисовку таблицы

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
                // params += "&search=name:" + item.value + ";description=" + item.value;
                params += "&search=name:" + item.value;
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
        const info = document.querySelector('.info-table');
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
    if (hasFilters) {
        params = hasFilters;
    }

    // если применялась сортировка, то добавляем в параметры
    if (orderby) {
        params += "&orderby=" + orderby;
    }

    console.log('после удаления элемента (доработать)', url+params);
    // // добавим лимит
    // if (limit) {

    //     // определим с какой записи брать данные
    //     offset = limit*(currentPage-1) + 1;

    //     // добавим лимит и смещение в параметры
    //     params += "&limit=" + limit + "&offset=" + offset; 
        
    // } 

    // console.log(params);

}

// отслеживаем клик по корзине
garbage.forEach(item => {
    item.addEventListener("click", deleteProduct);
})







function test() {
    let url = 'http://localhost/api/products.php?vendor_id=' + vendor_id;
    let totalProductsJson = sendRequestGET(url);
    console.log(JSON.parse(totalProductsJson)); 

    params="&category_id=4";
    totalProductsJson = sendRequestGET(url+params);
    console.log(JSON.parse(totalProductsJson));  

    params="&category_id=4&search=name:фиолетовый";
    totalProductsJson = sendRequestGET(url+params);
    console.log(JSON.parse(totalProductsJson));
    
    params="&category_id=4&search=name:фиолетовый";
    totalProductsJson = sendRequestGET(url+params);
    console.log(url+params);
    console.log(JSON.parse(totalProductsJson));
}
test();
