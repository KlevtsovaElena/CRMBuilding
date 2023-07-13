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
let url = 'http://localhost/api/products/products-with-count.php?vendor_id=' + vendor_id + '&deleted=0';

let brand_idEl = document.getElementById('brand_id');
let category_idEl = document.getElementById('category_id');
let searchEl = document.getElementById('search');
let limitEl = document.getElementById('limit');
let offsetEl = containerPagination.getAttribute('offset');

let prevButton;
let nextButton;
let totalPages;

let changePriceEl;
let changePriceInputEl;
let resetPriceEl;
let savePriceEl;
let changeInputsEl;

let brands = {};
let categories = {};

let orderby = "";
let filters = "";
let limitParams = "";
let params = "";

let limit = limitEl.value;
let offset = containerPagination.getAttribute('offset');
if (offset !==0) {
    currentPage = Math.ceil(offset/limit) + 1;
}

let totalProducts = [];
let totalProductsCount;
let totalProductsJson;

let garbage;

// закэшируем значения брендов и категорий
brand_idEl.querySelectorAll('option').forEach(item => {
    brands[item.value] = item.innerText;
})
category_idEl.querySelectorAll('option').forEach(item => {
    categories[item.value] = item.innerText;
})

// заполним страницу данными
startRenderPage();


/* ---------- НАБОР ФУНКЦИЙ ДЛЯ ОТРИСОВКИ СТРАНИЦЫ---------- */
function startRenderPage() {

    // 1. собрать параметры запроса
    params = getParams();

    // 2. получим данные по указанным параметрам из БД
    getProductsData(params);

    // 3. отрисуем пагинацию
    renderPagination(totalProductsCount, limit);

    // 4. отрисуем таблицу с данными
    renderListProducts(totalProducts);

}


/* ---------- СБОР ПАРАМЕТРОВ запроса---------- */
function getParams() {

    // сначала фильтры 
    filters = getFilters();

    // теперь проверим как у нас с сортировкой
    // ищем в каждом заголовке по атрибуту data-sort
    for (let i = 0; i < headTableProducts.length; i++) {

        if (headTableProducts[i].getAttribute('data-sort')) {
            orderby = "&orderby=" + headTableProducts[i].getAttribute('data-id') + ":" + headTableProducts[i].getAttribute('data-sort');
            break; 
        }
    }

    // добавим лимит
    limit = limitEl.value;
    limitParams = "&limit=" + limit + "&offset=" + offset;
    params = filters + orderby + limitParams;

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
    return params;
}

/* ---------- ПРОВЕРКА НАЛИЧИЯ ДАННЫХ В ОТВЕТЕ ---------- */
function changeData() {

    // определим какой offset нам взять 
    // 1. определим сколько всего страниц 
    if ((limit) && limit < totalProductsCount) {
        totalPages = Math.ceil(totalProductsCount/limit);
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
    getProductsData(params);

}


/* ---------- ПОЛУЧЕНИЕ ДАННЫХ ИЗ БД ---------- */
function getProductsData(params) {

    // сделаем запрос с параметрами, запишем данные в переменную totalProducts
    totalProductsJson = sendRequestGET(url + params);

    if (totalProductsJson) {
        totalProducts = JSON.parse(totalProductsJson);
    } else {
        totalProducts = {
            'count': 0,
            'products': []
        };
    }

    // количество записей в базе по указанным параметрам
    totalProductsCount = totalProducts['count'];

    console.log('всего ' + totalProducts['count'] + ' выборка ' + totalProducts['products'].length);

    // если записей с таким offset нет, но в бд записи есть, то переделаем запрос с иным offset 
    if (totalProducts['products'].length === 0 && totalProductsCount > 0) {
        changeData();
        return;
    }

}


/* ---------- ОТРИСОВКА ТОВАРОВ В ТАБЛИЦЕ---------- */
function renderListProducts(totalProducts) {

    // очистим контейнер
    containerListProducts.innerHTML = "";   
    // сброс инфы внизу страницы
    info.innerText = "";

    // количество записей
    let records = totalProducts['products'].length;

    // если записей вообще нет                
    if (records === 0) {
        info.innerText = "Записей нет";
        // сбросим офсет
        offset = 0;
        return;
    }
    
    // если лимит установлен и он меньше кол-ва записей, то records = limit
    // иначе выводим всё records = totalProducts['products].length
    if ((limit) && (limit < records)) {
        records = limit; 
    } 

    // заполним данными и отрисуем шаблон
    for (i = 0; i < records; i++) {
        containerListProducts.innerHTML += tmplRowProduct.replace('${article}', totalProducts['products'][i]['article'])
                                                        .replace('${photo}',  totalProducts['products'][i]['photo'])
                                                        .replace('${name}', totalProducts['products'][i]['name'])
                                                        .replace('${brand_id}', brands[totalProducts['products'][i]['brand_id']])
                                                        .replace('${category_id}', categories[totalProducts['products'][i]['category_id']])
                                                        .replace('${quantity_available}', totalProducts['products'][i]['quantity_available'].toLocaleString('ru'))
                                                        .replace('${price}', totalProducts['products'][i]['price'])
                                                        .replace('${price}', totalProducts['products'][i]['price'].toLocaleString('ru'))
                                                        .replace('${price}', totalProducts['products'][i]['price'].toLocaleString('ru'))
                                                        .replace('${unit}', totalProducts['products'][i]['unit_id'])
                                                        .replace('${id}', totalProducts['products'][i]['id'])
                                                        .replace('${id}', totalProducts['products'][i]['id'])
                                                        .replace('${id}', totalProducts['products'][i]['id'])
                                                        .replace('${max_price}', totalProducts['products'][i]['max_price'])
                                                        .replace('${max_price}', totalProducts['products'][i]['max_price'].toLocaleString('ru'))
                                                        .replace('${max_price}', totalProducts['products'][i]['max_price'].toLocaleString('ru'));
    }


    // выведем внизу таблицы сколько всего записей 
    info.innerText = "Всего " + totalProductsCount.toLocaleString('ru') + declinationWord(totalProductsCount, [' запись', ' записи', ' записей']);

    // этот кусок кода относится к мусорке
    // здесь, тк изначально таких элементов на стр нет,
    // и они появляются только после отрисовки таблицы
    garbage = document.querySelectorAll('.garbage');
    // отслеживаем клик по корзине
    garbage.forEach(item => {
        item.addEventListener("click", deleteProduct);
    })

    changePriceEl = document.querySelectorAll('.change-price');
    changePriceInputEl = document.querySelectorAll('.change-price-el');
    resetPriceEl = document.querySelectorAll('.reset-price');
    savePriceEl = document.querySelectorAll('.save-price');
    changeInputsEl = document.querySelectorAll('.change-price-input');

    changePriceEl.forEach(item => {
        item.addEventListener('click', showChangeInput);
    })

    resetPriceEl.forEach(item => {
        item.addEventListener('click', resetChangePrice);
    })

    savePriceEl.forEach(item => {
        item.addEventListener('click', saveChangePrice);
    })

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

    // отрисуем страничку
    startRenderPage();
}

// отслеживаем клик по заголовку
headTableProducts.forEach(item => {
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


/* ---------- УДАЛЕНИЕ ТОВАРА ---------- */
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

    // соберём json
    let obj = JSON.stringify({
        'id': productId,
        'deleted':  1
    });

    // делаем запрос на удаление товара по id
    sendRequestPOST('http://localhost/api/products.php', obj);

    // sendRequestDELETE('http://localhost/api/products.php?id=' + productId);


    // заполним страницу данными
    startRenderPage();

}


/* ---------- ПЕРЕХОД И ПЕРЕДАЧА ПАРАМЕТРОВ ФИЛЬТРАЦИИ НА СТРАНИЦУ редактирования---------- */
function editProduct(id) {

    // заменяем в истории браузера стр на стр с get параметрами
    // для того, чтобы при переходе по кнопке НАЗАД мы увидели контент по параметрам
    history.replaceState(history.length, null, 'vendor-list-products.php?vendor_id=' + vendor_id + "&deleted=0" + params);

    // при переходе на страницу редактирования товара передаём ещё и параметры фильтрации в get
    window.location.href = "http://localhost/pages/vendor-edit-product.php?id=" + id + "&vendor_id=" + vendor_id + "&deleted=0" + params ; 
}

/* ---------- ПЕРЕХОД И ПЕРЕДАЧА ПАРАМЕТРОВ ФИЛЬТРАЦИИ НА СТРАНИЦУ добавления товара---------- */
function addProduct() {

    // заменяем в истории браузера стр на стр с get параметрами
    // для того, чтобы при переходе по кнопке НАЗАД мы увидели контент по параметрам
    history.replaceState(history.length, null, 'vendor-list-products.php?vendor_id=' + vendor_id + "&deleted=0" + params);

    // при переходе на страницу добавления товара передаём ещё и параметры фильтрации в get
    window.location.href = "http://localhost/pages/vendor-add-product.php?vendor_id="  + vendor_id + "&deleted=0" + params ; 
}


/* ---------- ИЗМЕНЕНИЕ ЦЕНЫ---------- */

// показать инпуты
function showChangeInput() {
    // строка продукта
    let rowProduct = event.target.closest('.list-products__row');
    
    // скрываем все инпуты, которые были открыты 
    changePriceInputEl.forEach(item => {
        item.classList.add('d-none');
    })

    // инпуты этого продукта
    let changePriceInput = rowProduct.querySelectorAll('.change-price-el');
    // показываем их
    changePriceInput.forEach(item => {
        item.classList.remove('d-none');
    })


    // показываем все div со старыми ценами
    changePriceEl.forEach(item => {
        item.classList.remove('d-none');
    })

    // divы со старыми ценами этого продукта
    let changePrice = rowProduct.querySelectorAll('.change-price');
    // скрываем их
    changePrice.forEach(item => {
        item.classList.add('d-none');
    })

}

// сбросить изменения без сохранения
function resetChangePrice() {
    // сбросим все значения всех инпутов
    changeInputsEl.forEach(item => {
        item.value = "";
    });

    // скрываем все инпуты, которые были открыты 
    changePriceInputEl.forEach(item => {
        item.classList.add('d-none');
    })

    // показываем все div со старыми ценами
    changePriceEl.forEach(item => {
        item.classList.remove('d-none');
    })
}

// сохранить изменения
function saveChangePrice() {
    // строка продукта
    let rowProduct = event.target.closest('.list-products__row');

    // id продукта
    let idProduct = rowProduct.getAttribute('product-id');

    // все инпуты этого продукта
    let changePriceInput = rowProduct.querySelectorAll('.change-price-input');

    // все divs со старыми ценами продукта
    let changePrice = rowProduct.querySelectorAll('.change-price');

    // собираем json для отправки на сервер
    let obj = {};
    changePriceInput.forEach(item => {
        
        if (item.value) {
            obj[item.name] = item.value;
        }

    })

    if (Object.keys(obj).length > 0) {

        // если цена больше среднерыночной, то выходим
        if (obj.price && obj.max_price) {
            if (Number(obj.price) >= Number(obj.max_price)) {
                alert("Цена товара должна быть меньше среднерыночной цены!")
                return;
            };
        } else if (obj.price && !obj.max_price) {
            if (Number(obj.price) >= Number(changePrice[1].getAttribute('data-price-num'))) {
                alert("Цена товара должна быть меньше среднерыночной цены!")
                return;
            };
        } else if (!obj.price && obj.max_price) {
            if (Number(obj.max_price) <= Number(changePrice[0].getAttribute('data-price-num'))) {
                alert("Цена товара должна быть меньше среднерыночной цены!")
                return;
            };
        }        

        // если всё ок, то собираем данные и отправляем в БД
        obj['id'] = idProduct;
        let objJson = JSON.stringify(obj);

        // отправка запроса на запись 
        sendRequestPOST('http://localhost/api/products.php', objJson);

        // перерисовка страницы
        startRenderPage();

        return;
    }

    resetChangePrice();

}