console.log("Файл main.js подключен");

/* ****** URL, используемый на js, просто поменять на серверный с локального ****** */
// пропишем URL, используемый на js, чтобы в файлах js не менять кучу ссылок вручную
// вместо http://localhost используем везде mainUrl
// для сервера меняем http://localhost на актуальное значение

var protocol = window.location.protocol;

// Получение хоста текущего URL (например, 'example.com' или 'example.com:8080')
var host = window.location.host;

let mainUrl = protocol + "//" + host;

console.log(mainUrl)

/* ****** Функции для отправки запросов и получения апишки ****** */

//функция для отправки запросов GET
function sendRequestGET(url){

    let requestObj = new XMLHttpRequest();
    requestObj.open('GET', url, false);
    requestObj.send();
    return requestObj.responseText;
}

//функция для отправки запросов POST json`ном
function sendRequestPOST(url, params) {

    let requestObj = new XMLHttpRequest();
    requestObj.open('POST', url, false);
    requestObj.setRequestHeader('Content-Type', 'application/json');
    requestObj.send(params);
    return requestObj.responseText;

}

//функция для отправки запросов POST json`ном
function sendRequestTruePOST(url, params) {

    let requestObj = new XMLHttpRequest();
    requestObj.open('POST', url, true);
    requestObj.setRequestHeader('Content-Type', 'application/json');
    requestObj.send(params);
    return requestObj.responseText;

}

//функция для отправки запросов POST
function sendRequestFormUrlPOST(url, params){

    let requestObj = new XMLHttpRequest();
    requestObj.open('POST', url, false);
    requestObj.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    requestObj.send(params);
    return requestObj.responseText;

}

//функция для отправки запросов DELETE
function sendRequestDELETE(url) {

    let requestObj = new XMLHttpRequest();
    requestObj.open('DELETE', url, false);
    requestObj.send();
    return requestObj.responseText;

}


/* ---------- СОХРАНЕНИЕ РЕЖИМА МЕНЮ ПРИ КЛИКЕ НА ПМ (СВЕРНУТО/НЕ СВЕРНУТО) ---------- */
document.addEventListener('DOMContentLoaded', restoreMenuState);

// при загрузки страницы проверяем статус меню (свернуто или нет )
// и добавляем соответсвующий класс меню
function restoreMenuState() {
    const menuLeft = document.querySelector('.menu-left');
    const mainContent = document.querySelector('.main-content');

    if (sessionStorage.getItem('menuState')) {
        if (sessionStorage.getItem('menuState') == 'collapsed') {
            menuLeft.classList.add('collapsed');
            mainContent.classList.add('collapsed');
        } else {
            menuLeft.classList.remove('collapsed');
            mainContent.classList.remove('collapsed');
        }
    }
}


/* ---------- TOGGLE MENU LEFT ---------- */
// по кнопке Свернуть меню
function toggleMenu() {
    const menuLeft = document.querySelector('.menu-left');
    const mainContent = document.querySelector('.main-content');

    menuLeft.classList.toggle('collapsed');
    mainContent.classList.toggle('collapsed');

    // запишем статус в sessionStorage
    let isCollapsed = menuLeft.classList.contains('collapsed');
    if (isCollapsed) {
        sessionStorage.setItem('menuState', 'collapsed');
    } else {
        sessionStorage.setItem('menuState', 'notCollapsed');

    }
}

/* ---------- СКЛОНЕНИЕ СЛОВА ---------- */
// функция для склонения слова в зависимотси от числа
// вызывать так declinationWord(число, [' запись', ' записи', ' записей']);
function declinationWord(n, word) {  
    n = Math.abs(n) % 100; 
    let n1 = n % 10;
    if (n > 10 && n < 20) { return word[2]; }
    if (n1 > 1 && n1 < 5) { return word[1]; }
    if (n1 == 1) { return word[0]; }
    return word[2];
}

/* ---------- ВЫДЕЛЕНИЕ АКТИВНОГО ПУНКТА МЕНЮ---------- */
let pageName = window.location.pathname + window.location.search;

if(pageName.includes('vendor-add-product') || pageName.includes('vendor-edit-product')) {
    pageName = "/pages/vendor-list-products.php";
} else if(pageName.includes('admin-add-product') || pageName.includes('admin-edit-product')) {
    pageName = "/pages/admin-list-products.php";
} else if(pageName.includes('admin-add-vendor') || pageName.includes('admin-edit-vendor')) {
    pageName = "/pages/admin-vendors.php";
} else if (pageName.includes('vendor-order')) {
    if (document.querySelector('.menu-top__profile').getAttribute('data-role') == '2') {
        pageName = "/pages/vendor-list-orders.php"; 
    } else if (document.querySelector('.menu-top__profile').getAttribute('data-role') == '1') {
        pageName = "/pages/admin-orders.php";
    }
}
let itemNav = document.querySelectorAll('[data-page-name]');

itemNav.forEach( item => {
    if (pageName.includes(item.getAttribute('data-page-name'))) {
        item.classList.add('active');
    }
})


//функция оповещения админа о неутвержденных товарах
// сделаем true , чтобы не ждать ответ с сервера и не зависала страница sendRequestTruePOST
function notifyAdminInactiveGoods() {

    //ссылка на страницу с неодобренными товарами, которую передадим в пост-запросе
    let text = mainUrl + '/pages/admin-list-products.php?off_product=0&is_confirm=0&limit=10&offset=0';

    //собираем ссылку на нужный эндпойнт
    let link = mainUrl + '/api/notification/telegram-send-inactive-goods.php';

    //формируем параметры для передачи в апишку
    let obj = JSON.stringify({
        "text" : text
    });

    //передаем параметры на сервер в пост-запросе
    sendRequestTruePOST(link, obj);

}