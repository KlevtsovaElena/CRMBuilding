console.log('подключили list-units.js');
// найдём шаблон и контейнер для отрисовки товаров
const tmplRowUnits = document.getElementById('template-body-table').innerHTML;
const containerListUnits = document.querySelector('.list-units__body');
// найдём шаблон и контейнер для отрисовки пагинации
const tmplPagination = document.getElementById('template-pagination').innerHTML;
const containerPagination = document.querySelector('.pagination-wrapper');
// контейнер информации внизу страницы
const info = document.querySelector('.info-table');
//получаем все элементы заголовка для отслеживания сортировки
const headTableUnits = document.getElementById('list-units').querySelectorAll('th');

// определим основные переменные
let currentPage = 1;
let prevButton;
let nextButton;
let totalPages;

let url = mainUrl + '/api/units/units-with-count.php?deleted=0';

let searchEl = document.getElementById('search');
let limitEl = document.getElementById('limit');
let offsetEl = containerPagination.getAttribute('offset');

let orderby = "";
let filters = "";
let limitParams = "";
let params = "";

// кол-во записей на странице и смещение, рассчёт номера страницы
let limit = limitEl.value;
let offset = containerPagination.getAttribute('offset');
if (offset !==0) {
    currentPage = Math.ceil(offset/limit) + 1;
}

let totalUnits = [];
let totalUnitsCount;
let totaUnitsJson;

let garbage;

// let priceConfirmedEl = document.querySelector('.price-confirm-container');
// let priceConfirmed;
// let tmplPriceConfirm = document.getElementById('tmpl-price-confirm').innerHTML;
// let tmplPriceNotConfirm = document.getElementById('tmpl-price-not-confirm').innerHTML;


// let changePriceEl;
// let changePriceInputEl;
// let changeInputsEl;


// заполним страницу данными
startRenderPage();


/* ---------- НАБОР ФУНКЦИЙ ДЛЯ ОТРИСОВКИ СТРАНИЦЫ---------- */
function startRenderPage() {

    // 1. собрать параметры запроса
    params = getParams();

    // 2. получим данные по указанным параметрам из БД
    getUnitsData(params);

    // 3. отрисуем пагинацию
    renderPagination(totalUnitsCount, limit);

    // 4. отрисуем таблицу с данными
    renderListUnits(totalUnits);

    // 5. добавим параметры в адресную строку
    history.replaceState(history.length, null, 'admin-unit-product.php?&deleted=0' + params);

}


/* ---------- СБОР ПАРАМЕТРОВ запроса---------- */
function getParams() {

    // сначала фильтры 
    filters = getFilters();

    // теперь проверим как у нас с сортировкой
    // ищем в каждом заголовке по атрибуту data-sort
    for (let i = 0; i < headTableUnits.length; i++) {

        if (headTableUnits[i].getAttribute('data-sort')) {
            orderby = "&orderby=" + headTableUnits[i].getAttribute('data-id') + ":" + headTableUnits[i].getAttribute('data-sort');
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

    // проверим значение  поиска
    // проверяем на наличие данных, если есть, то нормализуем (если надо)
    // и добавляем в параметр строки запроса 
    [searchEl].forEach(item => {
        if (item.value.trim()) {
            if  (item.id === 'search') {
                params += "&search=name:" + item.value + ";name_short:" + item.value;
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
    if ((limit) && limit < totalUnitsCount) {
        totalPages = Math.ceil(totalUnitsCount/limit);
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
    getUnitsData(params);

}


/* ---------- ПОЛУЧЕНИЕ ДАННЫХ ИЗ БД ---------- */
function getUnitsData(params) {

    // сделаем запрос с параметрами, запишем данные в переменную totalUnits
    totaUnitsJson = sendRequestGET(url + params);

    if (totaUnitsJson) {
        totalUnits = JSON.parse(totaUnitsJson);
    } else {
        totalUnits = {
            'count': 0,
            'units': []
        };
    }

    // количество записей в базе по указанным параметрам
    totalUnitsCount = totalUnits['count'];

    // если записей с таким offset нет, но в бд записи есть, то переделаем запрос с иным offset 
    if (totalUnits['units'].length === 0 && totalUnitsCount > 0) {
        changeData();
        return;
    }

}


/* ---------- ОТРИСОВКА ТОВАРОВ В ТАБЛИЦЕ---------- */
function renderListUnits(totalUnits) {

    // очистим контейнер
    containerListUnits.innerHTML = "";   
    // сброс инфы внизу страницы
    info.innerText = "";

    // количество записей
    let records = totalUnits['units'].length;

    // если записей вообще нет                
    if (records === 0) {
        info.innerText = "Записей нет";
        // сбросим офсет
        offset = 0;
        return;
    }
    
    // если лимит установлен и он меньше кол-ва записей, то records = limit
    // иначе выводим всё records = totalUnits['units].length
    if ((limit) && (limit < records)) {
        records = limit; 
    } 







    // заполним данными и отрисуем шаблон
    for (i = 0; i < records; i++) {

        containerListUnits.innerHTML += tmplRowUnits.replace('${id}', totalUnits['units'][i]['id'])
                                                        .replace('${count_unit}', i+1)
                                                        .replace('${name}', totalUnits['units'][i]['name'])
                                                        .replace('${name}',  totalUnits['units'][i]['name'])
                                                        .replace('${name_short}', totalUnits['units'][i]['name_short'])
                                                        .replace('${name_short}', totalUnits['units'][i]['name_short']);
                                                        
    }


    // выведем внизу таблицы сколько всего записей 
    info.innerText = "Всего " + totalUnitsCount.toLocaleString('ru') + declinationWord(totalUnitsCount, [' запись', ' записи', ' записей']);

    // этот кусок кода относится к мусорке
    // здесь, тк изначально таких элементов на стр нет,
    // и они появляются только после отрисовки таблицы
    garbage = document.querySelectorAll('.garbage');
    // отслеживаем клик по корзине
    garbage.forEach(item => {
        item.addEventListener("click", deleteUnit);
    })

    // changePriceEl = document.querySelectorAll('.change-price');
    // changePriceInputEl = document.querySelectorAll('.change-price-el');
    // changeInputsEl = document.querySelectorAll('.change-price-input');

    // changePriceEl.forEach(item => {
    //     item.addEventListener('click', showChangeInput);
    // })

}


/* ---------- ОТРИСОВКА ПАГИНАЦИИ ---------- */
function renderPagination(totalUnitsCount, limit) {

    // из полученных переменных получаем кол-во страниц
    if ((limit) && limit < totalUnitsCount) {
        totalPages = Math.ceil(totalUnitsCount/limit);
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
        headTableUnits.forEach(item => {
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
headTableUnits.forEach(item => {
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


/* ---------- УДАЛЕНИЕ ТОВАРА ---------- */
function deleteUnit() {

    // проверяем корректность токена
    check();

    // запрашиваем подтверждение удаления
    let isDelete = false;

    isDelete = window.confirm('Вы действительно хотите удалить эту запись?');

    if(!isDelete) {
        return;
    }

    // найдём id единицы товара  по атрибуту unit-id
    const unitId = event.target.closest('.list-units__row').getAttribute('unit-id');

    // соберём json
    let obj = JSON.stringify({
        'id': unitId,
        'deleted':  1
    });

    // делаем запрос на удаление товара по id
    sendRequestPOST(mainUrl + '/api/units.php', obj);

    // заполним страницу данными
    startRenderPage();

}


// /* ---------- ПЕРЕХОД И ПЕРЕДАЧА ПАРАМЕТРОВ ФИЛЬТРАЦИИ НА СТРАНИЦУ редактирования---------- */
// function editProduct(id) {

//     // заменяем в истории браузера стр на стр с get параметрами
//     // для того, чтобы при переходе по кнопке НАЗАД мы увидели контент по параметрам
//     history.replaceState(history.length, null, 'vendor-list-products.php?vendor_id=' + vendor_id + "&deleted=0" + params);

//     // при переходе на страницу редактирования товара передаём ещё и параметры фильтрации в get
//     window.location.href = mainUrl + "/pages/vendor-edit-product.php?id=" + id + "&vendor_id=" + vendor_id + "&deleted=0" + params ; 
// }

/* ---------- добавлениe единицы товара---------- */
function addUnit() {

    // заменяем в истории браузера стр на стр с get параметрами
    // для того, чтобы при переходе по кнопке НАЗАД мы увидели контент по параметрам
    history.replaceState(history.length, null, 'vendor-list-products.php?vendor_id=' + vendor_id + "&deleted=0" + params);

    // при переходе на страницу добавления товара передаём ещё и параметры фильтрации в get
    window.location.href = mainUrl + "/pages/vendor-add-product.php?vendor_id="  + vendor_id + "&deleted=0" + params ; 
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
function resetChangePrice(currency_dollar) {
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

    // если долларовый товар, то сбросим ещё и контейнер с пересчётом доллары в сумы на цену в атрибуте
    if(currency_dollar == "1") {

        // найдём divы с ценами в сум
        let priceUzs = event.target.closest('.list-products__row').querySelectorAll('.price-uzs'); 

        // перезапишем цену в соответсвии с данными из атрибута data-price-num
        priceUzs.forEach(item => {

            let priceTmp = Number(item.getAttribute('data-price-num'));
            item.innerText = '(' + priceTmp.toLocaleString('ru') + ' Сум)';

        })

    }
}

// сохранить изменения
function saveChangePrice(currency_dollar, rate) {

    // проверяем корректность токена
    check();

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

    // если в json что-то есть:
    if (Object.keys(obj).length > 0) {
        // если цены в сумах
        if (currency_dollar == "0") {

            // если цена больше среднерыночной, то выходим
            if (obj.price && obj.max_price) {
                if (Number(obj.price) >= Number(obj.max_price)) {
                    alert("Цена товара должна быть меньше среднерыночной цены!")
                    return;
                }
            } else if (obj.price && !obj.max_price) {
                if (Number(obj.price) >= Number(changePrice[1].getAttribute('data-price-num'))) {
                    alert("Цена товара должна быть меньше среднерыночной цены!")
                    return;
                }
            } else if (!obj.price && obj.max_price) {
                if (Number(obj.max_price) <= Number(changePrice[0].getAttribute('data-price-num'))) {
                    alert("Цена товара должна быть меньше среднерыночной цены!")
                    return;
                }
            } 


        // если цены в долларах    
        } else {
            // все divs с атрибутом старой цены в долларах
            let oldPriceDollarEl = rowProduct.querySelectorAll('.price-uzs');

            // если цена больше среднерыночной, то выходим
            if (obj.price_dollar && obj.max_price_dollar) {
                if (Number(obj.price_dollar) >= Number(obj.max_price_dollar)) {
                    alert("Цена товара должна быть меньше среднерыночной цены!")
                    return;
                } else {
                    obj['price'] = obj.price_dollar * rate;
                    obj['max_price'] = obj.max_price_dollar * rate;
                }
            } else if (obj.price_dollar && !obj.max_price_dollar) {
                if (Number(obj.price_dollar) >= Number(oldPriceDollarEl[1].getAttribute('data-price-dollar'))) {
                    alert("Цена товара должна быть меньше среднерыночной цены!")
                    return;
                } else {
                    obj['price'] = obj.price_dollar * rate;
                }
            } else if (!obj.price_dollar && obj.max_price_dollar) {
                if (Number(obj.max_price_dollar) <= Number(oldPriceDollarEl[0].getAttribute('data-price-dollar'))) {
                    alert("Цена товара должна быть меньше среднерыночной цены!")
                    return;
                } else {
                    obj['max_price'] = obj.max_price_dollar * rate;
                }
            }  
        }

        // если всё ок, то собираем данные и отправляем в БД (изменение цены)
        obj['id'] = idProduct;
        let objJson = JSON.stringify(obj);

        // отправка запроса на запись (изменение цены)
        sendRequestPOST(mainUrl + '/api/products.php', objJson);

        // отправим запрос на изменение статуса подтверждения цен поставщика
        // (при любом изменении цены поставщику устанавливаем подверждение цен в 0)
        let objVendor = JSON.stringify({
            'id': vendor_id,
            'price_confirmed':  0
        });
        sendRequestPOST(mainUrl + '/api/vendors.php', objVendor);

        // перерисовка страницы
        startRenderPage(0);

        return;
    }

    resetChangePrice(currency_dollar);

}

