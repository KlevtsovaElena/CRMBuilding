/* ****** Функции для отправки запросов и получения апишки ****** */
console.log("Файл main.js подключен");
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