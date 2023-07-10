console.log('подключили list-products');

// определим основные переменные
let brand_idEl = document.getElementById('brand_id');
let category_idEl = document.getElementById('category_id');
let searchEl = document.getElementById('search');
let limitEl = document.getElementById('limit');
let currentPageEl = document.querySelectorAll('.current-page');
let totalPagesEl = document.querySelector('.total-page');
let garbageEl = document.querySelectorAll('.garbage');
const headTableProducts = document.getElementById('list-products').querySelectorAll('th');

let vendor_id = document.getElementById('vendor_id').value;
let limit = limitEl.value;
let offset = document.querySelector('.pagination-wrapper').getAttribute('offset');


let currentPage = Number(currentPageEl[0].innerText);
let totalPages = Number(totalPagesEl.innerText);
let totalProductsCount = Number(document.querySelector('.total-product').innerText); 

let orderby = "";
let filters = "";
let limitParams = "";
let params = "";

let url = "http://localhost/pages/vendor-list-products.php?vendor_id=" + vendor_id;

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

    // перезагружаем страничку
    params = getParams();

    window.location.href = url + params ;
}

// отслеживаем клик по заголовку
headTableProducts.forEach(item => {
    item.addEventListener("click", sortChange);
})


/* ---------- УДАЛЕНИЕ ТОВАРА ---------- */
function deleteProduct() {

    // запрашиваем подтверждение удаления
    let isDelete = false;

    isDelete = window.confirm('Вы действительно хотите удалить этот товар?');

    if(!isDelete) {
        return;
    }

    // найдём id товара по атрибуту product-id
    const productId = event.target.closest('.list-products__row').getAttribute('product-id');

    // делаем запрос на удаление товара по id
    sendRequestDELETE('http://localhost/api/products.php?id=' + productId);

    // перезагружаем страничку
    params = getParams();

    window.location.href = url + params ; 

}

// отслеживаем клик по корзине
garbageEl.forEach(item => {
    item.addEventListener("click", deleteProduct);
})


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


/* ---------- ПЕРЕДАЧА ПАРАМЕТРОВ ФИЛЬТРАЦИИ НА ДРУГУЮ СТРАНИЦУ---------- */
function editProduct(id) {

    // соберём параметры
    params = getParams();

    // заменяем в истории браузера стр на стр с get параметрами
    // для того, чтобы при переходе по кнопке НАЗАД мы увидели контент по параметрам
    history.replaceState(history.length, null, 'vendor-list-products.php?vendor_id=' + vendor_id + params);

    // при переходе на страницу редактирования товара передаём ещё и параметры фильтрации в get
    window.location.href = "http://localhost/pages/vendor-edit-product.php?id=" + id + "&vendor_id=" + vendor_id + params ; 
}