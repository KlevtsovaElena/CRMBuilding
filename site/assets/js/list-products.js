console.log('подключили list-products', mainUrl);
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

let url = mainUrl + '/api/products/products-with-count.php?deleted=0&category_deleted=0&brand_deleted=0&vendor_deleted=0&vendor_id=' + vendor_id;

let brand_idEl = document.getElementById('brand_id');
let category_idEl = document.getElementById('category_id');
let searchEl = document.getElementById('search');
let limitEl = document.getElementById('limit');
let offsetEl = containerPagination.getAttribute('offset');

let priceConfirmedEl = document.querySelector('.price-confirm-container');
let priceConfirmed;
let tmplPriceConfirm = document.getElementById('tmpl-price-confirm').innerHTML;
let tmplPriceNotConfirm = document.getElementById('tmpl-price-not-confirm').innerHTML;

let activeCheckEl = document.querySelector('.active-check');
let activeEl = activeCheckEl.querySelector('input');

let prevButton;
let nextButton;
let totalPages;


let changePriceEl;
let changePriceInputEl;
let changeInputsEl;

let brands = {};
let categories = {};
let units = {};

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

// // закэшируем значения брендов и категорий
// brand_idEl.querySelectorAll('option').forEach(item => {
//     brands[item.value] = item.innerText;
// })
// category_idEl.querySelectorAll('option').forEach(item => {
//     categories[item.value] = item.innerText;
// })
// // закэшируем значения единиц измерения (временно, пока нет апишки)
// let unitsJson = sendRequestGET(mainUrl + '/api/units.php');
// let unitsData = JSON.parse(unitsJson);
// unitsData.forEach(item => {
//    units[item['id']] = item['name_short'];
// })
// заполним страницу данными
startRenderPage(priceConfirmedEl.getAttribute('confirm-price'));


/* ---------- НАБОР ФУНКЦИЙ ДЛЯ ОТРИСОВКИ СТРАНИЦЫ---------- */
function startRenderPage(priceConfirmed) {

    // 1. собрать параметры запроса
    params = getParams();

    // 2. получим данные по указанным параметрам из БД
    getProductsData(params);

    // 3. отрисуем пагинацию
    renderPagination(totalProductsCount, limit);

    // 4. отрисуем таблицу с данными
    renderListProducts(totalProducts);

    // 5. добавим параметры в адресную строку
    history.replaceState(history.length, null, 'vendor-list-products.php?vendor_id=' + vendor_id + "&deleted=0" + params);

    // 6. покажем инфу подтверждены ли цены
    if (priceConfirmed == 0) {
        priceConfirmedEl.innerHTML = tmplPriceNotConfirm;
    } else if (priceConfirmed == 1) {
        priceConfirmedEl.innerHTML = tmplPriceConfirm;
    }

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

    // проверим чекбокс неактивных товаров
    if(activeEl.value) {
        params += "&" + activeEl.value;
    }

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

        let isConfirmProduct;
        if (totalProducts['products'][i]['is_confirm'] == '1') {
            isConfirmProduct = '<svg fill="#009933" width="20px" height="20px" viewBox="0 0 32.00 32.00" enable-background="new 0 0 32 32" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" stroke="#009933" transform="rotate(0)matrix(1, 0, 0, 1, 0, 0)"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#66FF99" stroke-width="0.256"></g><g id="SVGRepo_iconCarrier"> <g id="Approved"></g> <g id="Approved_1_"> <g> <path d="M30,1H2C1.448,1,1,1.448,1,2v28c0,0.552,0.448,1,1,1h28c0.552,0,1-0.448,1-1V2C31,1.448,30.552,1,30,1z M29,29H3V3h26V29z "></path> <path d="M12.629,21.73c0.192,0.18,0.438,0.27,0.683,0.27s0.491-0.09,0.683-0.27l10.688-10c0.403-0.377,0.424-1.01,0.047-1.413 c-0.377-0.404-1.01-0.425-1.413-0.047l-10.004,9.36l-4.629-4.332c-0.402-0.377-1.035-0.356-1.413,0.047 c-0.377,0.403-0.356,1.036,0.047,1.413L12.629,21.73z"></path> </g> </g> <g id="File_Approve"></g> <g id="Folder_Approved"></g> <g id="Security_Approved"></g> <g id="Certificate_Approved"></g> <g id="User_Approved"></g> <g id="ID_Card_Approved"></g> <g id="Android_Approved"></g> <g id="Privacy_Approved"></g> <g id="Approved_2_"></g> <g id="Message_Approved"></g> <g id="Upload_Approved"></g> <g id="Download_Approved"></g> <g id="Email_Approved"></g> <g id="Data_Approved"></g> </g></svg>';
        } else {
            isConfirmProduct = '<span title="товар на рассмотрении у Администратора"><svg  fill="#d2323d" width="20px" height="20px" viewBox="0 0 128 128" id="Layer_1" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <polygon points="82.4,40 64,58.3 45.6,40 40,45.6 58.3,64 40,82.4 45.6,88 64,69.7 82.4,88 88,82.4 69.7,64 88,45.6 "></polygon> <path d="M1,127h126V1H1V127z M9,9h110v110H9V9z"></path> </g> </g></svg></span>';
        }

        containerListProducts.innerHTML += tmplRowProduct.replace('${article}', totalProducts['products'][i]['article'])
                                                        .replace('${id}', totalProducts['products'][i]['id'])
                                                        .replace('${id}', totalProducts['products'][i]['id'])
                                                        .replace('${id}', totalProducts['products'][i]['id'])
                                                        .replace('${photo}',  totalProducts['products'][i]['photo'])
                                                        .replace('${name}', totalProducts['products'][i]['name'])
                                                        .replace('${category_id}', totalProducts['products'][i]['category_name'])
                                                        .replace('${brand_id}', totalProducts['products'][i]['brand_name'])
                                                        .replace('${quantity_available}', totalProducts['products'][i]['quantity_available'].toLocaleString('ru'))
                                                        .replace('${unit}', totalProducts['products'][i]['unit_name_short'])
                                                        .replace('${is_active}', totalProducts['products'][i]['is_active'])
                                                        .replace('${checked}', checked)
                                                        .replace('${price_format}', totalProducts['products'][i]['price'].toLocaleString('ru'))
                                                        .replace('${price_format}', totalProducts['products'][i]['price'].toLocaleString('ru'))
                                                        .replace('${price_format}', totalProducts['products'][i]['price'].toLocaleString('ru'))
                                                        .replace('${price_dollar_format}', totalProducts['products'][i]['price_dollar'].toLocaleString('ru'))
                                                        .replace('${price_dollar_format}', totalProducts['products'][i]['price_dollar'].toLocaleString('ru'))
                                                        .replace('${price}', totalProducts['products'][i]['price'])
                                                        .replace('${price}', totalProducts['products'][i]['price'])
                                                        .replace('${price}', totalProducts['products'][i]['price'])
                                                        .replace('${price_dollar}', totalProducts['products'][i]['price_dollar'])
                                                        .replace('${max_price}', totalProducts['products'][i]['max_price'])
                                                        .replace('${max_price}', totalProducts['products'][i]['max_price'])
                                                        .replace('${max_price}', totalProducts['products'][i]['max_price'])
                                                        .replace('${max_price_dollar}', totalProducts['products'][i]['max_price_dollar'])
                                                        .replace('${max_price_format}', totalProducts['products'][i]['max_price'].toLocaleString('ru'))
                                                        .replace('${max_price_format}', totalProducts['products'][i]['max_price'].toLocaleString('ru'))
                                                        .replace('${max_price_format}', totalProducts['products'][i]['max_price'].toLocaleString('ru'))
                                                        .replace('${max_price_dollar_format}', totalProducts['products'][i]['max_price_dollar'].toLocaleString('ru'))
                                                        .replace('${max_price_dollar_format}', totalProducts['products'][i]['max_price_dollar'].toLocaleString('ru'))
                                                        .replace('${rate}', totalProducts['products'][i]['vendor_rate'])
                                                        .replace('${rate}', totalProducts['products'][i]['vendor_rate'])
                                                        .replace('${is_confirm_product}', totalProducts['products'][i]['is_confirm'])
                                                        .replace('${is_confirm}', isConfirmProduct);
                                                        
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
    priceConfirmed = check()['price_confirmed'];

    // 1. поменяем номер странички
    currentPage = currentPage + variance;

    // и офсет
    offset = (currentPage - 1) * limit;
   
    // отрисуем страничку
    startRenderPage(priceConfirmed);

}


/* ---------- НАЖАТИЕ НА ИМЯ ЗАГОЛОВКА ТАБЛИЦЫ (СОРТИРОВКА по одному ключу) ---------- */
function sortChange() {

    // проверяем корректность токена
    priceConfirmed = check()['price_confirmed'];

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
    startRenderPage(priceConfirmed);
}

// отслеживаем клик по заголовку
headTableProducts.forEach(item => {
    item.addEventListener("click", sortChange);
})


/* ---------- НАЖАТИЕ НА ПРИМЕНИТЬ (Выборка по фильтрам) ---------- */
const sendChangeData = document.querySelector('.form-filters').querySelector('button');

function applyFilters() {

    // проверяем корректность токена
    priceConfirmed = check()['price_confirmed'];

    // сбрасываем нумерацию страниц и офсет
    currentPage = 1;
    offset = 0;

    // заполним страницу данными
    startRenderPage(priceConfirmed);

}
sendChangeData.addEventListener("click", applyFilters);


/* ---------- УДАЛЕНИЕ ТОВАРА ---------- */
function deleteProduct() {

    // проверяем корректность токена
    priceConfirmed = check()['price_confirmed'];

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

    // sendRequestDELETE(mainUrl + '/api/products.php?id=' + productId);


    // заполним страницу данными
    startRenderPage(priceConfirmed);

}

/* ---------- НАЖАТИЕ НА ГАЛОЧКУ НЕАКТИВНЫЕ В МЕНЮ ФИЛЬТРАЦИИ ---------- */

// если выбрана галочка, то не нужен параметр is_active
// если же галочки нет, то запрашиваем только is_active=1
// для этого меняем значение атрибута value  у чекбокса
activeCheckEl.onclick = function(){
    if(activeEl.checked) {
        activeEl.value = ""
        
    } else {
        activeEl.value = "is_active=1";  
    }

    console.log(activeEl.value);
}

/* ---------- ПЕРЕХОД И ПЕРЕДАЧА ПАРАМЕТРОВ ФИЛЬТРАЦИИ НА СТРАНИЦУ редактирования---------- */
function editProduct(id) {

    // заменяем в истории браузера стр на стр с get параметрами
    // для того, чтобы при переходе по кнопке НАЗАД мы увидели контент по параметрам
    history.replaceState(history.length, null, 'vendor-list-products.php?vendor_id=' + vendor_id + "&deleted=0" + params);

    // при переходе на страницу редактирования товара передаём ещё и параметры фильтрации в get
    window.location.href = mainUrl + "/pages/vendor-edit-product.php?id=" + id + "&vendor_id=" + vendor_id + "&deleted=0" + params ; 
}

/* ---------- ПЕРЕХОД И ПЕРЕДАЧА ПАРАМЕТРОВ ФИЛЬТРАЦИИ НА СТРАНИЦУ добавления товара---------- */
function addProduct() {

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
    priceConfirmed = check()['price_confirmed'];

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
        obj['is_confirm'] = 0;
        let objJson = JSON.stringify(obj);

        // отправка запроса на запись (изменение цены)
        sendRequestPOST(mainUrl + '/api/products.php', objJson);

        // отправим запрос на изменение статуса подтверждения цен поставщика
        // (при любом изменении цены поставщику устанавливаем подверждение цен в 0)
        // let objVendor = JSON.stringify({
        //     'id': vendor_id,
        //     'price_confirmed':  0
        // });
        // sendRequestPOST(mainUrl + '/api/vendors.php', objVendor);

        // перерисовка страницы
        startRenderPage(priceConfirmed);

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
    priceConfirmed = check()['price_confirmed'];

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

    console.log(obj);

    // отправим запрос на изменение 
    sendRequestPOST(mainUrl + '/api/products.php', obj);

    // перерисовка страницы
    startRenderPage(priceConfirmed);

}

/* ---------- ПОКАЗАТЬ ИНФОРМАЦИЮ О ЕЖЕДНЕВНОМ ПОДТВЕРЖДЕНИИ ЦЕН ---------- */
function showConfirmPriceDailyInfo() {
    let infoConfirmPriceDailyText = document.querySelector('.confirm-price-daily__info-text');
    infoConfirmPriceDailyText.style.opacity = '1';
    setTimeout(() => {

        infoConfirmPriceDailyText.style.opacity = '0';

    }, 3000);
}



// ВРЕМЕННО при нажатии на кнопку Подтвердить Цены  в поле price_confirmed таблицы vendors записываем 1
// потом заменить на передачу id поставщика в новую таблицу

function confirmPriceDaily() {
    // проверка корректности токена
    check(); 

    //собираем параметры для передачи в бд
    obj = JSON.stringify({
        'id': vendor_id,
        'price_confirmed': 1
    });

    // отправим запрос на изменение 
    sendRequestPOST(mainUrl + '/api/vendors.php', obj);

    // перезагрузим страницу
    window.location.href = window.location.href;

}