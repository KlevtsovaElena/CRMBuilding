console.log('подключили list-products');
let currentPage = 1;
let vendor_id=document.getElementById('vendor_id');
let limit=document.getElementById('limit').value;

let totalProducts=document.querySelectorAll('.list-products__row').length;

let brand_id;
let category_id;
let search;
let offset;
let orderby;



renderPagination(totalProducts, limit);

/* ---------- НАЖАТИЕ НА ПРИМЕНИТЬ ---------- */
const sendChangeData = document.querySelector('.form-filters').querySelector('button');
function getChangeData() {

    // сбрасываем нумерацию страниц
    currentPage = 1;

    // вызываем отрисовку таблицы
    renderListProducts()
}

sendChangeData.addEventListener("click", getChangeData);

/* ---------- ОТРИСОВКА ТАБЛИЦЫ ТОВАРОВ ---------- */
function renderListProducts() {
    // собираем все значения полей
    limit=document.getElementById('limit').value;
    
    let totalProducts=document.querySelectorAll('.list-products__row').length;
    
    let brand_id;
    let category_id;
    let search;
    let offset;
    let orderby;







    // разбираем значения полей

    // получаем строку get параметров запроса

    // отправляем запрос на сервер

    // получаем данные

    // перепишем totalProducts 
}


/* ---------- ОТРИСОВКА ПАГИНАЦИИ ---------- */
function renderPagination(totalProducts, limit) {

    // из полученных переменных получаем кол-во страниц
    let totalPages = Math.ceil(totalProducts/limit);

    // найдём шаблон и контейнер для отрисовки
    const tmplPagination = document.getElementById('template-pagination').innerHTML;
    const containerPagination = document.querySelector('.pagination-wrapper');

    // очистим контейнер
    containerPagination.innerHTML = "";

    // заполним данными и отрисуем шаблон
    containerPagination.innerHTML = tmplPagination  .replace('${currentPage}', currentPage)
                                                    .replace('${currentPage}', currentPage)
                                                    .replace('${totalPages}', totalPages);

    

    console.log('totalPages', totalPages);
}

/* ---------- ПЕРЕКЛЮЧЕНИЕ СТРАНИЧЕК ---------- */
function switchPage(variance) {

    let containerCurrentPage = document.querySelector('.current-page');

    currentPage = currentPage + variance;

    containerCurrentPage.innerText = currentPage;
    
    renderListProducts();


}
