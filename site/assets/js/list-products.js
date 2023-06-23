console.log('подключили list-products');
let currentPage = 1;
let vendor_id = document.getElementById('vendor_id').value;
let limitEl = document.getElementById('limit');
let totalProductsEl = document.querySelectorAll('.list-products__row');


let limit = limitEl.value
let totalProducts = totalProductsEl.length;

let brand_idEl = document.getElementById('brand_id');
let category_idEl = document.getElementById('category_id');
let searchEl = document.getElementById('search');
let offsetEl = document.getElementById('offset');
let orderbyEl = document.getElementById('orderby');

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
    let params = "filters&vendor_id=" + vendor_id;

    [brand_idEl, category_idEl, searchEl, limitEl, orderbyEl].forEach(item => {
currentPage=2;

        if(item.value.trim()) {

            if (item.id === "search") {
                params += "&search=name:" + searchEl.value + ";description:" + searchEl.value;
            } else if (item.id === "orderby") {
                params += "&orderby=" + item.value + ":asc"; 
            }else {
                    params += "&" + item.id + "=" + item.value;  
            }
        }

        if ((item.id === "limit") && !(currentPage===1)) {
            params += "&offset=" + ((currentPage-1)*limitEl.value + 1);
        }
    })

console.log(params);
    // if(!(searchEl.value.trim())) {
    //     let search = "name:" + searchEl.value + ";description:" + searchEl.value;
    // }

    // if(!(limit.value)) {
        
    // }
    



    totalProducts = totalProductsEl.length;
    







    // разбираем значения полей

    // получаем строку get параметров запроса
    sendRequestGET('http://localhost/api/products?' + params);
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
