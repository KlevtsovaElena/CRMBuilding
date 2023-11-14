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
const headTableUnits = document.getElementById('list-units').querySelectorAll('[data-sort]');

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
    history.replaceState(history.length, null, 'admin-unit-product.php?deleted=0' + params);

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

    // посчитаем с какой цифры начать нумерацию в таблице в зависимости от лимита и номера страницы
    let numbering;
    if(currentPage == 1) {
        numbering = 1;
    } else {
        numbering = (currentPage-1)*limit + 1;
    }

    // заполним данными и отрисуем шаблон
    for (i = 0; i < records; i++) {

        containerListUnits.innerHTML += tmplRowUnits.replace('${id}', totalUnits['units'][i]['id'])
                                                        .replace('${count_unit}', numbering)
                                                        .replace('${name}', totalUnits['units'][i]['name'])
                                                        .replace('${name}',  totalUnits['units'][i]['name'])
                                                        .replace('${name_short}', totalUnits['units'][i]['name_short'])
                                                        .replace('${name_short}', totalUnits['units'][i]['name_short']);    
        numbering += 1;                                              
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


/* ---------- УДАЛЕНИЕ ЕДИНИЦЫ ТОВАРА ---------- */
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


/* ---------- ДОБАВЛЕНИЕ ЕДИНИЦЫ ТОВАРОВ ---------- */
function addUnit() {
    // проверяем корректность токена
    let role = check()['role'];

    if (role !== 1) {return;}

    //предотвратить дефолтные действия, отмена отправки формы (чтобы страница не перезагружалась)
    event.preventDefault(); 
    
    // определим переменные для названия и сокращённого названия единицы товара
    let nameUnit = document.getElementById('name');
    let nameShortUnit = document.getElementById('name_short');

    // если оба поля пустые, то выходим из функции
    // если какое-то одно пустое, то его приравниваем к заполненному
    if (!(nameUnit.value.trim()) && !(nameShortUnit.value.trim())) {
        alert('Оба поля пустые!');
        return;
    } else if (!(nameUnit.value.trim())) {
        nameUnit.value = nameShortUnit.value;
    } else if (!(nameShortUnit.value.trim())) {
        nameShortUnit.value = nameUnit.value;
    }

    // соберём json для передачи на сервер
    let obj = JSON.stringify({
        'name':  nameUnit.value,
        'name_short':  nameShortUnit.value,
    });

    // передаём данные на сервер
    sendRequestPOST(mainUrl + '/api/units.php', obj);

    // перезагрузим страницу
    window.location.href = window.location.href;

}


/* ---------- ИЗМЕНЕНИЕ ЕДИНИЦЫ ТОВАРОВ---------- */

// показать инпуты для редактирвоания
function showEditInput() {
    // найдём строку записи, которую будут редактировать, родительский элемнт
    let rowUnit = event.target.closest('.list-units__row');

    // скроем ненужные поля и покажем поля для редактирования, кнопки сброса и сохранения
    // скроем значок редактирования
    rowUnit.querySelector('.edit-unit').style.display = 'none';
    // скроем название и сокращ название
    rowUnit.querySelector('.unit_name').classList.add('d-none');
    rowUnit.querySelector('.unit_name_short').classList.add('d-none');

    // покажем иконки сохранения и сброса
    rowUnit.querySelector('.save-unit').style.display = 'inline-block';
    rowUnit.querySelector('.reset-unit').style.display = 'inline-block';
    // покажем инпуты для редактирования
    rowUnit.querySelector('.unit_name-change').classList.remove('d-none');
    rowUnit.querySelector('.unit_name_short-change').classList.remove('d-none');

}

// сбросить инпуты для редактирвоания
function reset() {
    // найдём строку записи, которую будут редактировать, родительский элемнт
    let rowUnit = event.target.closest('.list-units__row');

    // покажем значок редактирования
    rowUnit.querySelector('.edit-unit').style.display = 'inline-block';
    // покажем название и сокращ название
    rowUnit.querySelector('.unit_name').classList.remove('d-none');
    rowUnit.querySelector('.unit_name_short').classList.remove('d-none');

    // скроем иконки сохранения и сброса
    rowUnit.querySelector('.save-unit').style.display = 'none';
    rowUnit.querySelector('.reset-unit').style.display = 'none';
    // очистим и скроем инпуты для редактирования
    rowUnit.querySelector('.unit_name-change').value = "";
    rowUnit.querySelector('.unit_name-change').classList.add('d-none');
    rowUnit.querySelector('.unit_name_short-change').value = "";
    rowUnit.querySelector('.unit_name_short-change').classList.add('d-none');
}

// сохранить изменения
function saveUnit() {
    // проверяем корректность токена
    let role = check()['role'];
    if (role !== 1) {return;}

    // найдём строку записи, которую будут редактировать, родительский элемнт
    let rowUnit = event.target.closest('.list-units__row');
    let obj = {};
    // найдём inputs
    let newName = rowUnit.querySelector('.unit_name-change');
    let newNameShort = rowUnit.querySelector('.unit_name_short-change');

    // id unit
    let idUnit = rowUnit.getAttribute('unit-id');

    // если оба значения не внесены, то ничего на сервер не отправляем
    if (!(newName.value.trim()) && !(newNameShort.value.trim())) {
        return;
    // если оба значения внесены, то собираем данные
    } else if (newName.value.trim() && newNameShort.value.trim()) {
        obj['name'] = newName.value;
        obj['name_short'] = newNameShort.value;
    } else if (newName.value.trim() && !(newNameShort.value.trim())) {
        obj['name'] = newName.value;
    } else if (!(newName.value.trim()) && newNameShort.value.trim()) {
        obj['name_short'] = newNameShort.value;
    }

    obj['id'] = idUnit;
    let objJson = JSON.stringify(obj);

    // отправка запроса на запись (изменение едницы товара)
    sendRequestPOST(mainUrl + '/api/units.php', objJson);

    // перерисовка страницы
    startRenderPage();

}
