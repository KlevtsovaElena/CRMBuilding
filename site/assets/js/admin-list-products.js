console.log('подключили admin-list-products', mainUrl);

// основной url для запроса товаров
// urlStaticString - это неменяющиеся параметры поиска, те товары всегда неудаленные, 
// роль поставщика 2, поставщик не удален, город не удален, бренд не удален, категория не удалена
let urlStaticString = "deleted=0&city_deleted=0&vendor_role=2&vendor_deleted=0&category_deleted=0&brand_deleted=0"
let url = mainUrl + '/api/products/products-with-count-for-list-products.php?' + urlStaticString;

// найдём шаблон и контейнер для отрисовки товаров
const tmplRowProduct = document.getElementById('template-body-table-uzs').innerHTML;
const tmplRowProductDollar = document.getElementById('template-body-table-dollar').innerHTML;

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

let city_idEl = document.getElementById('city_id');
let brand_idEl = document.getElementById('brand_id');
let category_idEl = document.getElementById('category_id');
let searchEl = document.getElementById('search');
let limitEl = document.getElementById('limit');
let vendor_idEl = document.getElementById('vendor_id');
let offsetEl = containerPagination.getAttribute('offset');

let vendorCheckName;
let vendorCheckId;

let confirmCheckEl = document.getElementById('is_confirm');
let activeCheckEl = document.getElementById('is_active');
let isOffProduct;

let prevButton;
let nextButton;
let totalPages;

let changePriceEl;
let changePriceInputEl;
let resetPriceEl;
let savePriceEl;
let changeInputsEl;

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

    // 5. добавим параметры в адресную строку
    history.replaceState(history.length, null, 'admin-list-products.php?' + params);
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
    isOffProduct = "";

    // проверим активность товаров (активные те , у которых город, поставщик или товар не отключены)
    if(activeCheckEl.value) {
        params += "&" + activeCheckEl.value;
        isOffProduct = "&" + activeCheckEl.value;
    }

    // перерисуем фильтры
    updateFilters();

    // проверим значение города, бренда, категории, поставщика и поиска
    // проверяем на наличие данных, если есть, то нормализуем (если надо)
    // и добавляем в параметр строки запроса 
    [city_idEl, brand_idEl, category_idEl, searchEl, vendor_idEl].forEach(item => {

        if (item.value.trim()) {
            if  (item.id === 'search') {
                params += "&search=name_front:" + item.value + ";description_front:" + item.value;
            } else {
                params += "&" + item.id + "=" + item.value;
            }
        }
    })

    // проверим утверждённость товаров
    if(confirmCheckEl.value) {
        params += "&" + confirmCheckEl.value;
    }

    // вернём параметры
    console.log(params);
    return params;
}

/* ---------- ПЕРЕРИСОВКА ФИЛЬТРОВ ---------- */
// запуск перерисовки фильтров updateFilters();
function updateFilters() {

    updateCityFilter();

    updateVendorFilter();

    updateCategoryFilter();

    updateBrandFilter();
}

// перерисовка фильтра города
function updateCityFilter() {

    // сохраняем параметры выбранного города во время нажатия кнопки
    cityCheckId = city_idEl.value;
    cityCheckName = city_idEl.querySelector('option[value="' + city_idEl.value + '"]').innerText; 
    console.log("перерисовка городов", cityCheckName);
    // отберём все города, у которых есть товары, удовлетв условию
    // и перерисуем фильтр городов
    let citiesListJSON = sendRequestGET(mainUrl + '/api/cities/get-uniq-cities-by-products.php?' + urlStaticString + isOffProduct + '&orderby=city_name:asc');
    let citiesList;

    // перерисовываем начинку фильтра
    // при этом если выбранный город отсутствует в списке
    // то он будет показан, но его не будет в списке для выбора
    city_idEl.innerHTML = `<option value="">Все</option>
                            <option value="${cityCheckId}" selected hidden>${cityCheckName}</option>`

    if (citiesListJSON) {
        citiesList = JSON.parse(citiesListJSON);
                                 
        for (let i = 0; i < citiesList.length; i++) {
            city_idEl.innerHTML += `<option value="${citiesList[i]['city_id']}">${citiesList[i]['city_name']}</option>`
        }

    }
}

// перерисовка фильтра поставщика в зависимости от города
function updateVendorFilter() {

    // сохраняем параметры выбранного поставщика во время нажатием кнопки
    vendorCheckId = vendor_idEl.value;
    vendorCheckName = vendor_idEl.querySelector('option[value="' + vendor_idEl.value + '"]').innerText; 
    console.log("перерисовка поставщиков", vendorCheckName);
    let paramsTemp = "";

    if (city_idEl.value.trim()) {paramsTemp += "&city_id=" + city_idEl.value.trim();}

    // отберём всех поставщиков, у которых есть товары, удовлетв условию
    // и перерисуем фильтр поставщика

    // сделаем запрос с параметрами, запишем данные в переменную vendorsList
    let vendorsListJSON = sendRequestGET(mainUrl + '/api/vendors/get-uniq-vendors-by-products.php?' + urlStaticString + paramsTemp + isOffProduct + '&orderby=vendor_name:asc');
    let vendorsList;

    // перерисовываем начинку фильтра
    // при этом если выбранный поставщик отсутствует в списке
    // то он будет показан, но его не будет в списке для выбора
    vendor_idEl.innerHTML = `   <option value="">Все</option>
                                <option value="${vendorCheckId}" selected hidden>${vendorCheckName}</option>`

    if (vendorsListJSON) {
        vendorsList = JSON.parse(vendorsListJSON);
                               
        for (let i = 0; i < vendorsList.length; i++) {
            vendor_idEl.innerHTML += `<option value="${vendorsList[i]['vendor_id']}">${vendorsList[i]['vendor_name']}</option>`
        }

    } 
}

// перерисовка фильтра категории в зависимости от города и поставщика
function updateCategoryFilter() {

    // сохраняем параметры выбранной категории во время нажатия кнопки
    categoryCheckId = category_idEl.value;
    categoryCheckName = category_idEl.querySelector('option[value="' + category_idEl.value + '"]').innerText; 
    console.log("перерисовка категории", categoryCheckName);
    let paramsTemp = "";

    if (city_idEl.value.trim()) {paramsTemp += "&city_id=" + city_idEl.value.trim();}
    if (vendor_idEl.value.trim()) {paramsTemp += "&vendor_id=" + vendor_idEl.value.trim();}

    // отберём все категории, у которых есть товары, удовлетв условию
    // и перерисуем фильтр категорий

    // сделаем запрос с параметрами
    let categoriesListJSON = sendRequestGET(mainUrl + '/api/categories/get-uniq-categories-by-products.php?' + urlStaticString + paramsTemp + isOffProduct + '&orderby=category_name:asc');
    let categoriesList;

    // перерисовываем начинку фильтра
    // при этом если выбранная категория отсутствует в списке
    // то она будет показана, но её не будет в списке для выбора
    category_idEl.innerHTML = `<option value="">Все</option>
                                <option value="${categoryCheckId}" selected hidden>${categoryCheckName}</option>`

    if (categoriesListJSON) {
        categoriesList = JSON.parse(categoriesListJSON);
                                  
        for (let i = 0; i < categoriesList.length; i++) {
            category_idEl.innerHTML += `<option value="${categoriesList[i]['category_id']}">${categoriesList[i]['category_name']}</option>`
        }

    } 

}

// перерисовка фильтра бренда в зависимости от города и поставщика и категории
function updateBrandFilter() {

    // сохраняем параметры выбранного бренда во время нажатия кнопки
    brandCheckId = brand_idEl.value;
    brandCheckName = brand_idEl.querySelector('option[value="' + brand_idEl.value + '"]').innerText; 
    console.log("перерисовка бренда", brandCheckName);

    let paramsTemp = "";

    if (city_idEl.value.trim()) {paramsTemp += "&city_id=" + city_idEl.value.trim();}
    if (vendor_idEl.value.trim()) {paramsTemp += "&vendor_id=" + vendor_idEl.value.trim();}
    if (category_idEl.value.trim()) {paramsTemp += "&category_id=" + category_idEl.value.trim();}

    // отберём все бренды, у которых есть товары, удовлетв условию
    // и перерисуем фильтр брендов

    // сделаем запрос с параметрами
    let brandsListJSON = sendRequestGET(mainUrl + '/api/brands/get-uniq-brands-by-products.php?' + urlStaticString + paramsTemp + isOffProduct + '&orderby=brand_name:asc');
    let brandsList;

    // перерисовываем начинку фильтра
    // при этом если выбранная категория отсутствует в списке
    // то он будет показан, но его не будет в списке для выбора
    brand_idEl.innerHTML = `<option value="">Все</option>
                            <option value="${brandCheckId}" selected hidden>${brandCheckName}</option>`

    if (brandsListJSON) {
        brandsList = JSON.parse(brandsListJSON);
                                     
        for (let i = 0; i < brandsList.length; i++) {
            brand_idEl.innerHTML += `<option value="${brandsList[i]['brand_id']}">${brandsList[i]['brand_name']}</option>`
        }

    } 

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
        let checked;
        if (totalProducts['products'][i]['is_active'] == '1') {
            checked = 'checked';
        } else {
            checked = '';
        }

        let checkedConfirm;
        if (totalProducts['products'][i]['is_confirm'] == '1') {
            checkedConfirm = 'checked';
        } else {
            checkedConfirm = '';
        }

        if (totalProducts['products'][i]['vendor_currency_dollar'] == "0") {
            //  для сумовых поставщиков
            containerListProducts.innerHTML += tmplRowProduct   .replace('${id}', totalProducts['products'][i]['id'])
                                                                .replace('${id}', totalProducts['products'][i]['id'])
                                                                .replace('${id}', totalProducts['products'][i]['id'])
                                                                .replace('${id}', totalProducts['products'][i]['id'])
                                                                .replace('${vendor_id}', totalProducts['products'][i]['vendor_id'])
                                                                .replace('${vendor_name}', totalProducts['products'][i]['vendor_name'])
                                                                .replace('${photo}',  totalProducts['products'][i]['photo'])
                                                                .replace('${name}', totalProducts['products'][i]['name_front'])
                                                                .replace('${category_id}', totalProducts['products'][i]['category_name'])
                                                                .replace('${brand_id}', totalProducts['products'][i]['brand_name'])
                                                                .replace('${city_name}', totalProducts['products'][i]['city_name'])
                                                                .replace('${quantity_available}', totalProducts['products'][i]['quantity_available'].toLocaleString('ru'))
                                                                .replace('${unit}', totalProducts['products'][i]['unit_name_short'])
                                                                .replace('${is_active}', totalProducts['products'][i]['is_active'])
                                                                .replace('${checked}', checked)
                                                                .replace('${price}', totalProducts['products'][i]['price'])
                                                                .replace('${price_format}', totalProducts['products'][i]['price'].toLocaleString('ru'))
                                                                .replace('${price_format}', totalProducts['products'][i]['price'].toLocaleString('ru'))
                                                                .replace('${max_price}', totalProducts['products'][i]['max_price'])
                                                                .replace('${max_price_format}', totalProducts['products'][i]['max_price'].toLocaleString('ru'))
                                                                .replace('${max_price_format}', totalProducts['products'][i]['max_price'].toLocaleString('ru'))
                                                                .replace('${checked-confirm}', checkedConfirm)
                                                                .replace('${is_confirm}', totalProducts['products'][i]['is_confirm']);
                                                                
        
        } else if (totalProducts['products'][i]['vendor_currency_dollar'] == "1"){
            // для долларовых поставщиков
            containerListProducts.innerHTML += tmplRowProductDollar.replace('${id}', totalProducts['products'][i]['id'])
                                                                .replace('${id}', totalProducts['products'][i]['id'])
                                                                .replace('${id}', totalProducts['products'][i]['id'])
                                                                .replace('${id}', totalProducts['products'][i]['id'])
                                                                .replace('${vendor_id}', totalProducts['products'][i]['vendor_id'])
                                                                .replace('${vendor_name}', totalProducts['products'][i]['vendor_name'])
                                                                .replace('${photo}',  totalProducts['products'][i]['photo'])
                                                                .replace('${name}', totalProducts['products'][i]['name_front'])
                                                                .replace('${category_id}', totalProducts['products'][i]['category_name'])
                                                                .replace('${brand_id}', totalProducts['products'][i]['brand_name'])
                                                                .replace('${city_name}', totalProducts['products'][i]['city_name'])
                                                                .replace('${quantity_available}', totalProducts['products'][i]['quantity_available'].toLocaleString('ru'))
                                                                .replace('${unit}', totalProducts['products'][i]['unit_name_short'])
                                                                .replace('${is_active}', totalProducts['products'][i]['is_active'])
                                                                .replace('${checked}', checked)
                                                                .replace('${price}', totalProducts['products'][i]['price'])
                                                                .replace('${price_format}', totalProducts['products'][i]['price'].toLocaleString('ru'))
                                                                .replace('${price_format}', totalProducts['products'][i]['price'].toLocaleString('ru'))
                                                                .replace('${max_price}', totalProducts['products'][i]['max_price'])
                                                                .replace('${max_price_format}', totalProducts['products'][i]['max_price'].toLocaleString('ru'))
                                                                .replace('${max_price_format}', totalProducts['products'][i]['max_price'].toLocaleString('ru'))
                                                                .replace('${rate}', totalProducts['products'][i]['vendor_rate'])
                                                                .replace('${rate}', totalProducts['products'][i]['vendor_rate'])
                                                                .replace('${rate}', totalProducts['products'][i]['vendor_rate'])
                                                                .replace('${price}', totalProducts['products'][i]['price'])
                                                                .replace('${max_price}', totalProducts['products'][i]['max_price'])
                                                                .replace('${price_dollar}', totalProducts['products'][i]['price_dollar'])
                                                                .replace('${price_dollar_format}', totalProducts['products'][i]['price_dollar'].toLocaleString('ru'))
                                                                .replace('${price_dollar_format}', totalProducts['products'][i]['price_dollar'].toLocaleString('ru'))
                                                                .replace('${max_price_dollar}', totalProducts['products'][i]['max_price_dollar'])
                                                                .replace('${max_price_dollar_format}', totalProducts['products'][i]['max_price_dollar'].toLocaleString('ru'))
                                                                .replace('${max_price_dollar_format}', totalProducts['products'][i]['max_price_dollar'].toLocaleString('ru'))
                                                                .replace('${checked-confirm}', checkedConfirm)
                                                                .replace('${is_confirm}', totalProducts['products'][i]['is_confirm']);
 
        }

        // если totalProducts['products'][i]['max_price'] меньше, чем totalProducts['products'][i]['price'], то выделим цены красным
        if(Number(totalProducts['products'][i]['max_price']) <= Number(totalProducts['products'][i]['price'])) {
            let priceElColor =  document.querySelectorAll('.price-mark');
            let lengthPriceElColor = priceElColor.length;

            priceElColor[lengthPriceElColor-1].style.color = "red";
            priceElColor[lengthPriceElColor-2].style.color = "red";  

        }
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
    changeInputsEl = document.querySelectorAll('.change-price-input');

    changePriceEl.forEach(item => {
        item.addEventListener('click', showChangeInput);
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
function deleteProduct() {
    
    // проверяем корректность токена
    check();

    // запрашиваем подтверждение удаления
    let isDelete = false;

    isDelete = window.confirm('Вы действительно хотите удалить этот товар?');

    if(!isDelete) {
        return;
    }

    // найдём id товара по атрибуту product-id
    const productId = event.target.closest('.list-products__row').getAttribute('product-id');

    // соберём json
    let obj = JSON.stringify({
        'id': productId,
        'deleted':  1
    });

    // делаем запрос на удаление товара по id
    sendRequestPOST(mainUrl + '/api/products.php', obj);

    // заполним страницу данными
    startRenderPage();

}

/* ---------- ПЕРЕХОД И ПЕРЕДАЧА ПАРАМЕТРОВ ФИЛЬТРАЦИИ НА СТРАНИЦУ редактирования---------- */
function editProduct(id) {

    // заменяем в истории браузера стр на стр с get параметрами
    // для того, чтобы при переходе по кнопке НАЗАД мы увидели контент по параметрам
    history.replaceState(history.length, null, 'admin-list-products.php?' + params);

    // при переходе на страницу редактирования товара передаём ещё и параметры фильтрации в get
    window.location.href = mainUrl + "/pages/admin-edit-product.php?id=" + id + params ; 
}


/* ---------- ПЕРЕХОД И ПЕРЕДАЧА ПАРАМЕТРОВ ФИЛЬТРАЦИИ НА СТРАНИЦУ добавления товара---------- */
function addProduct() {

    // заменяем в истории браузера стр на стр с get параметрами
    // для того, чтобы при переходе по кнопке НАЗАД мы увидели контент по параметрам
    history.replaceState(history.length, null, 'admin-list-products.php?' + params);

    // при переходе на страницу добавления товара передаём ещё и параметры фильтрации в get
    window.location.href = mainUrl + "/pages/admin-add-product.php?" + params; 
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

        // если всё ок, то собираем данные и отправляем в БД
        obj['id'] = idProduct;
        let objJson = JSON.stringify(obj);

        // отправка запроса на запись 
        sendRequestPOST(mainUrl + '/api/products.php', objJson);

        // перерисовка страницы
        startRenderPage();

        return;
    }

    resetChangePrice(currency_dollar);

}

/* ---------- ПЕРЕСЧЁТ ЦЕН В СУМЫ ---------- */

function calcPriceUzs(rate) {

    // куда записывать пересчитанную сумму в сумах
    let priceUzsEl = event.target.closest('td').querySelector('.price-uzs');

    // пересчитаем цену в сумы
    let priceUzs = rate * event.target.value;

    // если value нет (пустой инпут), то запишем Сум из атрибута
    // если не пустой, то запишем высчитанное значение
    if (event.target.value) {
        priceUzsEl.innerText = '(' + priceUzs.toLocaleString('ru') + " Сум)";
    } else {
        priceUzsEl.innerText = '(' + Number(priceUzsEl.getAttribute('data-price-num')).toLocaleString('ru') + " Сум)";
    }
    
}

/* ---------- РЕДАКТИРОВАНИЕ ТОВАРА ЧЕРЕЗ ЧЕКБОКС (АКТИВЕН/НЕАКТИВЕН) ---------- */

function checkboxChangedProductActive(id) {
    // проверяем корректность токена
    check()

    let isChecked = window.confirm('Вы действительно хотите изменить статус активности товара?');

    if(!isChecked) {
        //чтобы визуально не менялась галочка
        if(event.target.checked) {
            event.target.checked = false;
        } else {
            event.target.checked = true;
        }
        return;
    }

    //если при нажатии чекбокс активировн
    if (event.target.checked) {

        //собираем параметры для передачи в бд
        obj = JSON.stringify({
            'id': id,
            'is_active': 1
        });

    //если при нажатии чекбокс деактивирован
    } else {

        obj = JSON.stringify({
            'id': id,
            'is_active': 0
        });
    }

    // отправим запрос на изменение 
    sendRequestPOST(mainUrl + '/api/products.php', obj);

    // перерисовка страницы
    startRenderPage();

}

/* ---------- РЕДАКТИРОВАНИЕ ТОВАРА ЧЕРЕЗ ЧЕКБОКС (утверждён/неутверждён) ---------- */

function checkboxChangedProductConfirm(id) {
    // проверяем корректность токена
    check();

    let isChecked = window.confirm('Вы действительно хотите изменить статус товара?');

    if(!isChecked) {
        //чтобы визуально не менялась галочка
        if(event.target.checked) {
            event.target.checked = false;
        } else {
            event.target.checked = true;
        }
        return;
    }

    //если при нажатии чекбокс активировн
    if (event.target.checked) {

        
        // доп проверим , чтобы среднерын цена была больше, чем цена поставщика
        // если это не так, то запрещаем одобрять товар ии выводим сообщение

        // найдем родителя-строку таблицы
        let parentRow = event.target.closest('.list-products__row');
        // и элементы, где прописаны цены
        let tdPriceEl = parentRow.querySelectorAll('.price-mark');

        //сравним цены
        if(Number(tdPriceEl[0].getAttribute('data-price-num')) >= Number(tdPriceEl[1].getAttribute('data-price-num')))  {
            alert('У данного товара цена больше среднерыночной! Товар не будет подтверждён');
            event.target.checked = false;
            return;
        }   
        
        //собираем параметры для передачи в бд
        obj = JSON.stringify({
            'id': id,
            'is_confirm': 1
        });
        

    //если при нажатии чекбокс деактивирован
    } else {

        obj = JSON.stringify({
            'id': id,
            'is_confirm': 0
        });
    }

    // отправим запрос на изменение 
    sendRequestPOST(mainUrl + '/api/products.php', obj);

    // перерисовка страницы
    startRenderPage();

}