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
    menuLeft.classList.toggle('collapsed');
    mainContent.classList.toggle('collapsed');
}


/* ---------- ВАЛИДАЦИЯ ФОРМЫ ДОБАВЛЕНИЯ ТОВАРОВ ---------- */

const formAddProduct = document.getElementById('form-add-product');

async function addProduct() {
    //предотвратить дефолтные действия, отмена отправки формы (чтобы страница не перезагружалась)
    event.preventDefault(); 

    // запишем значения полей формы в переменные
    let  vendorId = formAddProduct.querySelector('#vendorId');
    let  nameProduct = formAddProduct.querySelector('#name');
    let  photo = formAddProduct.querySelector('#photo');
    let  brandId = formAddProduct.querySelector('#brandId');
    let  categoryId = formAddProduct.querySelector('#categoryId');
    let  description = formAddProduct.querySelector('#description');
    let  article = formAddProduct.querySelector('#article');
    let  quantityAvailable = formAddProduct.querySelector('#quantityAvailable');
    let  price = formAddProduct.querySelector('#price');
    let  maxPprice = formAddProduct.querySelector('#maxPprice');

    
    
    
    

[vendorId, nameProduct, photo, brandId, categoryId, description, article, 
quantityAvailable, price, maxPprice].forEach(item => {
    console.log(item.getAttribute('name') + "    " + item.value);
    
    if (!(item.value.trim())) {
        
        item.classList.add('error');
        item.closest('.form-add-product__elements__item').querySelector('.error-info').innerText = "Заполните данные!";
        item.closest('.form-add-product__elements__item').querySelector('.error-info').classList.remove('d-none');


    } else {
        
        item.classList.remove('error');
        // не работает
        // item.closest('.form-add-product__elements__item').querySelector('.error-info').innerText="";

    }
})

    
    //валидация


    // const url = "http://localhost/pages/test.php";
    // let json = sendRequestPOST(url, params);
    // let data = JSON.parse(json);


    
    console.log("отправлено");


}


