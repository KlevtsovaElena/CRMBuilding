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
function sendRequestPOST(url, params){

    let requestObj = new XMLHttpRequest();
    requestObj.open('POST', url, false);
    requestObj.setRequestHeader('Content-Type', 'application/json');
    requestObj.send(params);
    return requestObj.responseText;

}

/* ---------- TOGGLE MENU LEFT ---------- */
const menuLeft = document.querySelector('.menu-left');
const mainContent = document.querySelector('.main-content');

function toggleMenu() {
    menuLeft.classList.toggle('collapsed');
    mainContent.classList.toggle('collapsed');
}