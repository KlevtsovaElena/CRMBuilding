console.log("Файл main.js подключен");

/* ****** URL, используемый на js, просто поменять на серверный с локального ****** */
// пропишем URL, используемый на js, чтобы в файлах js не менять кучу ссылок вручную
// вместо http://localhost используем везде mainUrl
// для сервера меняем http://localhost на актуальное значение

let mainUrl = "http://localhost";

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

/* ---------- TOGGLE MENU LEFT ---------- */
const menuLeft = document.querySelector('.menu-left');
const mainContent = document.querySelector('.main-content');

function toggleMenu() {
    menuLeft.classList.toggle('collapsed');
    mainContent.classList.toggle('collapsed');
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
}
let itemNav = document.querySelectorAll('[data-page-name]');

itemNav.forEach( item => {
    if (pageName.includes(item.getAttribute('data-page-name'))) {
        item.classList.add('active');
    }
})

