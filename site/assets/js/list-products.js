console.log('подключили list-products');
// определим основные переменные
let currentPage = 1;
let params;
let prevButton;
let nextButton;
let totalPages;
let brands = {};
let categories = {};
let vendor_id = document.getElementById('vendor_id').value;
let brand_idEl = document.getElementById('brand_id');
let category_idEl = document.getElementById('category_id');
let searchEl = document.getElementById('search');
let limitEl = document.getElementById('limit');
let limit = limitEl.value

// соберём значения брендов и категорий
brand_idEl.querySelectorAll('option').forEach(item => {
    brands[item.value] = item.innerText;
})
category_idEl.querySelectorAll('option').forEach(item => {
    categories[item.value] = item.innerText;
})

// получение записей товаров из БД
let totalProductsJson = sendRequestGET('http://localhost/api/products.php?vendor_id=' + vendor_id);
let totalProducts = JSON.parse(totalProductsJson);

// подсчёт полученных записей
let totalProductsCount = totalProducts.length;

// отрисуем пагинацию
renderPagination(totalProductsCount, limit);

// отрисуем товары в таблице 
renderListProducts(totalProducts);


/* ---------- ОТРИСОВКА ТОВАРОВ В ТАБЛИЦЕ---------- */
function renderListProducts(totalProducts) {
    
    // найдём шаблон и контейнер для отрисовки
    const tmplRowProduct = document.getElementById('template-body-table').innerHTML;
    const containerListProducts = document.querySelector('.list-products__body');

    // очистим контейнер
    containerListProducts.innerHTML = "";

    // если записей нет, то выводим об этом инфо и выходим
    if (totalProducts.length === 0) {
        const info = document.querySelector('.info-table');
        info.innerText = "Записей с такими параметрами нет";
        return;
    }

    // заполним данными и отрисуем шаблон
    for (i = 0; i < limit; i++) {
        containerListProducts.innerHTML += tmplRowProduct.replace('${article}', totalProducts[i]['article'])
                                                        .replace('${photo}',  totalProducts[i]['photo'])
                                                        .replace('${name}', totalProducts[i]['name'])
                                                        .replace('${brand_id}', brands[totalProducts[i]['brand_id']])
                                                        .replace('${category_id}', categories[totalProducts[i]['category_id']])
                                                        .replace('${quantity_available}', totalProducts[i]['quantity_available'])
                                                        .replace('${price}', totalProducts[i]['price'])
                                                        .replace('${max_price}', totalProducts[i]['max_price']);
    }
}


/* ---------- ОТРИСОВКА ПАГИНАЦИИ ---------- */
function renderPagination(totalProductsCount, limit) {

    // из полученных переменных получаем кол-во страниц
    totalPages = Math.ceil(totalProductsCount/limit);

    // найдём шаблон и контейнер для отрисовки
    const tmplPagination = document.getElementById('template-pagination').innerHTML;
    const containerPagination = document.querySelector('.pagination-wrapper');

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
    params = "&brand_id=2"

    // отправим запрос
    totalProductsJson = sendRequestGET('http://localhost/api/products.php?vendor_id=111' + params);
    totalProducts = JSON.parse(totalProductsJson);

    // отрисуем таблицу
    renderListProducts(totalProducts)
}


/* ---------- СОБЕРЁМ СТРОКУ ЗАПРОСА ---------- */
function getRequestParams() {
    // собираем все значения полей
    params = "vendor_id=" + vendor_id;

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
    



    // totalProducts = totalProductsEl.length;
    







    // разбираем значения полей

    // получаем строку get параметров запроса
    sendRequestGET('http://localhost/api/products?' + params);
    // отправляем запрос на сервер


    // получаем данные

    // перепишем totalProducts 
}


/* ---------- НАЖАТИЕ НА ПРИМЕНИТЬ ---------- */
const sendChangeData = document.querySelector('.form-filters').querySelector('button');
function getChangeData() {

    // сбрасываем нумерацию страниц
    currentPage = 1;

    // вызываем отрисовку таблицы
    renderListProducts()
}
sendChangeData.addEventListener("click", getChangeData);

/* ---------- НАЖАТИЕ НА ИМЯ ЗАГОЛОВКА ТАБЛИЦЫ ---------- */
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

}

headTableProducts.forEach(item => {
    item.addEventListener("click", sortChange);
})

