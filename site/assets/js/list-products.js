console.log('подключили list-products');
// найдём шаблон и контейнер для отрисовки товаров
const tmplRowProduct = document.getElementById('template-body-table').innerHTML;
const containerListProducts = document.querySelector('.list-products__body');
// найдём шаблон и контейнер для отрисовки пагинации
const tmplPagination = document.getElementById('template-pagination').innerHTML;
const containerPagination = document.querySelector('.pagination-wrapper');
// контейнер информации внизу страницы
const info = document.querySelector('.info-table');
//получаем все элементы заголовка для отслеживания сортировки
const headTableProducts = document.getElementById('list-products').querySelectorAll('th');

// определим основные переменные
let currentPage = 1;
let vendor_id = document.getElementById('vendor_id').value;
let url = 'http://localhost/api/products.php?vendor_id=' + vendor_id;

let brand_idEl = document.getElementById('brand_id');
let category_idEl = document.getElementById('category_id');
let searchEl = document.getElementById('search');
let limitEl = document.getElementById('limit');
let offsetEl = containerPagination.getAttribute('offset');

let prevButton;
let nextButton;
let totalPages;

let brands = {};
let categories = {};

let orderby = "";
let filters = "";
let limitParams = "";
let params = "";

let paramsTest = "";

// let urlGet = window.location.search;

// // проверим переданы ли параметры фильтрации в get
// if (urlGet) {

//     // если переданы, то нам нужно заменить дефолтные значения элементов, где прописываются
//     // бренд, категория, лимит, поиск, сортировка
//     params = urlGet.replace('?', '&');
//     let urlParams = new URLSearchParams(urlGet);
    
//     if (urlParams.get('brand_id')) {brand_idEl.value = urlParams.get('brand_id')};
//     if (urlParams.get('category_id')) {category_idEl.value = urlParams.get('category_id')};
//     if (urlParams.get('limit')) {limitEl.value = urlParams.get('limit')};

//     if(urlParams.get('search')) {
//         searchEl.value = urlParams.get('search').split(':')[2];
//     }

//     if(urlParams.get('orderby')) {
//         let sortBy = urlParams.get('orderby').split(':')[0];
//         let mark = urlParams.get('orderby').split(':')[1];
//         document.getElementById('list-products').querySelector(`[data-id="${sortBy}"]`).setAttribute('data-sort', mark);
//     }


//     // console.log(urlParams.get('category_id'));
//     // console.log(urlParams.get('orderby'));
//     // console.log(urlParams.get('limit'));
//     // console.log(urlParams.get('offset'));
//     // console.log(urlParams.get('search'));

//     // console.log(params);

    
//     // brand_idEl.value = urlGet.searchParams.get('brand_id');
//     // category_idEl.value = urlGet.searchParams.get('category_id');
//     // searchEl.value = urlGet.searchParams.get('search');;
//     // limitEl.value = urlGet.searchParams.get('limit');;

// } else {
//     params= "";
// }


let limit = limitEl.value;
let offset = containerPagination.getAttribute('offset');
if (offset !==0) {
    currentPage = Math.ceil(offset/limit) + 1;
}

let totalProducts = [];

let garbage;


// закэшируем значения брендов и категорий
brand_idEl.querySelectorAll('option').forEach(item => {
    brands[item.value] = item.innerText;
})
category_idEl.querySelectorAll('option').forEach(item => {
    categories[item.value] = item.innerText;
})

// соберём параметры запроса
params = getParams();

// сделаем запрос с параметрами

// -----------------------------------------------------------------------
//кол-во ПОТОМ ПОЛУЧАТЬ ИЗ АПИ
let test2 = [];
let test = sendRequestGET(url + paramsTest);
if (test) {
    test2 = JSON.parse(test);
}
// подсчёт полученных записей
let totalProductsCount = test2.length;
console.log(totalProductsCount);

// -------------------------------------------------------------------

// сделаем запрос с параметрами
let totalProductsJson = sendRequestGET(url + params);

if (totalProductsJson) {
    totalProducts = JSON.parse(totalProductsJson);
}

// отрисуем пагинацию
renderPagination(totalProductsCount, limit);

// отрисуем товары в таблице 
renderListProducts(totalProducts);


/* ---------- СБОР ПАРАМЕТРОВ запроса---------- */


function getParams() {

    paramsTest = "";
    // сначала фильтры 
    filters = getFilters();

    console.log("filters", filters);

    // теперь проверим как у нас с сортировкой
    // ищем в каждом заголовке по атрибуту data-sort
    for (let i = 0; i < headTableProducts.length; i++) {

        if (headTableProducts[i].getAttribute('data-sort')) {
            orderby = "&orderby=" + headTableProducts[i].getAttribute('data-id') + ":" + headTableProducts[i].getAttribute('data-sort');
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

    // проверим значение бренда, категории и поиска

    // проверяем на наличие данных, если есть, то нормализуем (если надо)
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
    console.log("параметры из фильтра", params);
    return params;
}


/* ---------- ОТРИСОВКА ТОВАРОВ В ТАБЛИЦЕ---------- */
function renderListProducts(totalProducts) {

    // очистим контейнер
    containerListProducts.innerHTML = "";   
    // сброс инфы внизу страницы
    info.innerText = "";

    // если записей с таким offset нет, но в бд записи есть, то переделаем запрос 
    if (totalProducts.length === 0 && totalProductsCount > 0) {
        // определим какой offset нам взять 
        // у нас есть общее кол-во страниц
        // 1. сделаем текущей страницей последнюю 
        currentPage = totalPages;
        // 2. определим offset
console.log("currentPage ", currentPage);
console.log("limit ", limit);

        offset = (currentPage - 1) * limit;
        console.log(offset);
        // 3. перепишем параметры
        params = filters + orderby + "&limit=" + limit + "&offset=" + offset;
        // 4. делаем запрос на получение другого куска 
        let totalProductsJson = sendRequestGET(url + params);
        if (totalProductsJson) {
            totalProducts = JSON.parse(totalProductsJson);
        }

        renderPagination(totalProductsCount, limit)

    // если записей вообще нет                
    } else if (totalProducts.length === 0) {
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

    garbage = document.querySelectorAll('.garbage');
    // отслеживаем клик по корзине
    garbage.forEach(item => {
        item.addEventListener("click", deleteProduct);
    })
    console.log(garbage);

}


/* ---------- ОТРИСОВКА ПАГИНАЦИИ ---------- */
function renderPagination(totalProductsCount, limit) {


    // из полученных переменных получаем кол-во страниц
    if ((limit) && limit < totalProductsCount) {
        totalPages = Math.ceil(totalProductsCount/limit);
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

    console.log('totalPages', totalPages);
}

/* ---------- ПЕРЕКЛЮЧЕНИЕ СТРАНИЧЕК ---------- */
function switchPage(variance) {

    // 1. поменяем номер странички
    currentPage = currentPage + variance;

    // и офсет
    offset = (currentPage - 1) * limit;
   
    // соберём параметры
    params = getParams();


    // отправим запрос
// -----------------------------------------------------------------------
//кол-во ПОТОМ ПОЛУЧАТЬ ИЗ АПИ
test2 = [];
test = sendRequestGET(url + paramsTest);
if (test) {
    test2 = JSON.parse(test);
}
// подсчёт полученных записей
totalProductsCount = test2.length;
console.log(totalProductsCount);

// -------------------------------------------------------------------


    totalProductsJson = sendRequestGET(url + params);
    if (totalProductsJson) {
        totalProducts = JSON.parse(totalProductsJson);
    } else {
        totalProducts = [];
    }

    // отрисуем пагинацию
    renderPagination(totalProductsCount, limit);

    // отрисуем таблицу
    renderListProducts(totalProducts);



}


/* ---------- НАЖАТИЕ НА ИМЯ ЗАГОЛОВКА ТАБЛИЦЫ (СОРТИРОВКА по одному ключу) ---------- */

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

    // соберём строку запроса
    params = getParams();


console.log('сортировка', url+params);

    // отправим запрос
// -----------------------------------------------------------------------
//кол-во ПОТОМ ПОЛУЧАТЬ ИЗ АПИ
test2 = [];
test = sendRequestGET(url + paramsTest);
if (test) {
    test2 = JSON.parse(test);
}
// подсчёт полученных записей
totalProductsCount = test2.length;
console.log(totalProductsCount);

// -------------------------------------------------------------------


    totalProductsJson = sendRequestGET(url + params);
    if (totalProductsJson) {
        totalProducts = JSON.parse(totalProductsJson);
    } else {
        totalProducts = [];
    }
    // отрисуем пагинацию
    renderPagination(totalProductsCount, limit);
    // отрисуем таблицу
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

    limit = limitEl.value;
    offset = 0;
    // соберём строку запроса     
    params = getParams();

    console.log('фильтры ', url+params);

    // отправим запрос
// -----------------------------------------------------------------------
//кол-во ПОТОМ ПОЛУЧАТЬ ИЗ АПИ
test2 = [];
test = sendRequestGET(url + paramsTest);
if (test) {
    test2 = JSON.parse(test);
}
// подсчёт полученных записей
totalProductsCount = test2.length;
console.log(totalProductsCount);

// -------------------------------------------------------------------


    totalProductsJson = sendRequestGET(url + params);
    if (totalProductsJson) {
        totalProducts = JSON.parse(totalProductsJson);
    } else {
        totalProducts = [];
    }
   // отрисуем пагинацию
   renderPagination(totalProductsCount, limit);
    // отрисуем таблицу
    renderListProducts(totalProducts);

 

}
sendChangeData.addEventListener("click", getChangeDataFilters);


/* ---------- УДАЛЕНИЕ ТОВАРА ---------- */

function deleteProduct() {

    console.log("delete");
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

    // соберём параметры
    params = getParams();


    // отправим запрос
// -----------------------------------------------------------------------
//кол-во ПОТОМ ПОЛУЧАТЬ ИЗ АПИ
test2 = [];
test = sendRequestGET(url + paramsTest);
if (test) {
    test2 = JSON.parse(test);
}
// подсчёт полученных записей
totalProductsCount = test2.length;
console.log(totalProductsCount);

// -------------------------------------------------------------------


    totalProductsJson = sendRequestGET(url + params);
    if (totalProductsJson) {
        totalProducts = JSON.parse(totalProductsJson);
    } else {
        totalProducts = [];
    }

    // отрисуем пагинацию
    renderPagination(totalProductsCount, limit);

    // отрисуем таблицу
    renderListProducts(totalProducts);

}


/* ---------- ПЕРЕДАЧА ПАРАМЕТРОВ ФИЛЬТРАЦИИ НА ДРУГУЮ СТРАНИЦУ---------- */

function editProduct(id) {
    document.location.href = "http://localhost/pages/vendor-edit-product.php?id=" + id + params ; 
}

// Погоняем тестово какой приходит ответ при запрсе с параметрами


// function test() {
//     let paramsTest="";
//     let testPrducts = [];

//     // пустые параметры = все товары  
//     // 1. при загрузке страницы
//     // 2. при фильтрации если установлен новый лимит  
//     let url = 'http://localhost/api/products.php?vendor_id=' + vendor_id;
//     let totalProductsJson = sendRequestGET(url);
//     if (totalProductsJson) {
//             testPrducts = JSON.parse(totalProductsJson);
//             console.log(url+paramsTest);
//             console.log(testPrducts); 
//     } else {
//             testPrducts = [];
//             console.log(url+paramsTest);
//             console.log(testPrducts);
//     }

//     // только фильтрация - только brand_id = грузим всё этого бренда
//     paramsTest="&brand_id=2";
//     totalProductsJson = sendRequestGET(url+paramsTest);
//     if (totalProductsJson) {
//             testPrducts = JSON.parse(totalProductsJson);
//             console.log(url+paramsTest);
//             console.log(testPrducts); 
//     } else {
//             testPrducts = [];
//             console.log(url+paramsTest);
//             console.log(testPrducts);
//     }

//     // только фильтрация - только brand_id и категория
//     paramsTest="&brand_id=3&category_id=1";
//     totalProductsJson = sendRequestGET(url+paramsTest);
//     if (totalProductsJson) {
//             testPrducts = JSON.parse(totalProductsJson);
//             console.log(url+paramsTest);
//             console.log(testPrducts); 
//     } else {
//             testPrducts = [];
//             console.log(url+paramsTest);
//             console.log(testPrducts);
//     }

//     // только фильтрация - только category_id = грузим всё этого бренда
//     paramsTest="&category_id=1";
//     totalProductsJson = sendRequestGET(url+paramsTest);
//     if (totalProductsJson) {
//             testPrducts = JSON.parse(totalProductsJson);
//             console.log(url+paramsTest);
//             console.log(testPrducts); 
//     } else {
//             testPrducts = [];
//             console.log(url+paramsTest);
//             console.log(testPrducts);
//     }

//     // только поиск = грузим всё, где есть поисковое слово в описании или наименовании
//     paramsTest="&search=name:стеновой;description:стеновой";
//     totalProductsJson = sendRequestGET(url+paramsTest);
//     if (totalProductsJson) {
//             testPrducts = JSON.parse(totalProductsJson);
//             console.log(url+paramsTest);
//             console.log(testPrducts); 
//     } else {
//             testPrducts = [];
//             console.log(url+paramsTest);
//             console.log(testPrducts);
//     }

//     // только фильтрация -  brand_id, search грузим всё
//     paramsTest="&brand_id=1&search=name:стеновой;description:стеновой";
//     totalProductsJson = sendRequestGET(url+paramsTest);
//     if (totalProductsJson) {
//             testPrducts = JSON.parse(totalProductsJson);
//             console.log(url+paramsTest);
//             console.log(testPrducts); 
//     } else {
//             testPrducts = [];
//             console.log(url+paramsTest);
//             console.log(testPrducts);
//     }

//     // только фильтрация - category_id, brand_id, search грузим всё
//     paramsTest="&category_id=1&brand_id=1&search=name:стеновой;description:стеновой";
//     totalProductsJson = sendRequestGET(url+paramsTest);
//     if (totalProductsJson) {
//             testPrducts = JSON.parse(totalProductsJson);
//             console.log(url+paramsTest);
//             console.log(testPrducts); 
//     } else {
//             testPrducts = [];
//             console.log(url+paramsTest);
//             console.log(testPrducts);
//     }

//     // только сортировка без лимита
//     paramsTest="&orderby=price:desc";
//     totalProductsJson = sendRequestGET(url+paramsTest);
//     if (totalProductsJson) {
//             testPrducts = JSON.parse(totalProductsJson);
//             console.log(url+paramsTest);
//             console.log(testPrducts); 
//     } else {
//             testPrducts = [];
//             console.log(url+paramsTest);
//             console.log(testPrducts);
//     }

//     // только сортировка с лимитом
//     paramsTest="&orderby=price:desc&limit=5&offset=5";
//     totalProductsJson = sendRequestGET(url+paramsTest);
//     if (totalProductsJson) {
//             testPrducts = JSON.parse(totalProductsJson);
//             console.log(url+paramsTest);
//             console.log(testPrducts); 
//     } else {
//             testPrducts = [];
//             console.log(url+paramsTest);
//             console.log(testPrducts);
//     }

//     // сортировка с лимитом и фильтром
//     paramsTest="&orderby=price:desc&limit=5&offset=1&brand_id=2";
//     totalProductsJson = sendRequestGET(url+paramsTest);
//     if (totalProductsJson) {
//             testPrducts = JSON.parse(totalProductsJson);
//             console.log(url+paramsTest);
//             console.log(testPrducts); 
//     } else {
//             testPrducts = [];
//             console.log(url+paramsTest);
//             console.log(testPrducts);
//     }  

//     // сортировка с лимитом и фильтром без поиска
//     paramsTest="&orderby=price:desc&limit=5&offset=1&brand_id=1";
//     totalProductsJson = sendRequestGET(url+paramsTest);
//     if (totalProductsJson) {
//             testPrducts = JSON.parse(totalProductsJson);
//             console.log(url+paramsTest);
//             console.log(testPrducts); 
//     } else {
//             testPrducts = [];
//             console.log(url+paramsTest);
//             console.log(testPrducts);
//     }  

//     // сортировка с лимитом и фильтром с поиском
//     paramsTest="&orderby=price:desc&limit=5&offset=1&brand_id=1&search=name:стеновой;description:стеновой";
//     totalProductsJson = sendRequestGET(url+paramsTest);
//     if (totalProductsJson) {
//             testPrducts = JSON.parse(totalProductsJson);
//             console.log(url+paramsTest);
//             console.log(testPrducts); 
//     } else {
//             testPrducts = [];
//             console.log(url+paramsTest);
//             console.log(testPrducts);
//     }  

//     // лимит и фильтр
//     paramsTest="&limit=5&offset=1&brand_id=1";
//     totalProductsJson = sendRequestGET(url+paramsTest);
//     if (totalProductsJson) {
//             testPrducts = JSON.parse(totalProductsJson);
//             console.log(url+paramsTest);
//             console.log(testPrducts); 
//     } else {
//             testPrducts = [];
//             console.log(url+paramsTest);
//             console.log(testPrducts);
//     }  

//     // лимит и search
//     paramsTest="&limit=5&offset=1&search=name:стеновой;description:стеновой";
//     totalProductsJson = sendRequestGET(url+paramsTest);
//     if (totalProductsJson) {
//             testPrducts = JSON.parse(totalProductsJson);
//             console.log(url+paramsTest);
//             console.log(testPrducts); 
//     } else {
//             testPrducts = [];
//             console.log(url+paramsTest);
//             console.log(testPrducts);
//     }  

// }

// test();
