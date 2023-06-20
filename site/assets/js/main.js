/* ****** Функции для отправки запросов и получения апишки ****** */

//функция для отправки запросов GET
function sendRequestGET(url){

    let requestObj = new XMLHttpRequest();
    requestObj.open('GET', url, false);
    requestObj.send();
    return requestObj.responseText;
}

//функция для отправки запросов POST
function sendRequestPOST(url, params){

    let requestObj = new XMLHttpRequest();
    requestObj.open('POST', url, false);
    requestObj.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    requestObj.send(params);
    return requestObj.responseText;

}

/* ---------- TOGGLE MENU LEFT ---------- */
// ДОДЕЛАТЬ
const menuLeft = document.querySelector('.menu-left');
const mainContent = document.querySelector('.main-content');

function toggleMenu() {
    menuLeft.classList.toggle('collapse');
    mainContent.classList.toggle('collapse');
}


/* ---------- ВАЛИДАЦИЯ ФОРМЫ ДОБАВЛЕНИЯ ТОВАРОВ ---------- */

const formAddProduct = document.getElementById('form-add-product');

async function addProduct() {
    //предотвратить дефолтные действия, отмена отправки формы (чтобы страница не перезагружалась)
    event.preventDefault(); 


    
    //валидация



// вариант 1
    let formData = new FormData(formAddProduct);
    let response = await fetch('test.php', {
        method: 'POST',
        body: formData
    });

    if (response.ok){
        let result = await response.json();
    } else {
        return "Ошибка!";
    }


// вариант 2
    const url = "http://localhost/pages/test.php";
    let json = sendRequestPOST(url, params);
    let data = JSON.parse(json);


    
    console.log(response);
    console.log(data);

}


