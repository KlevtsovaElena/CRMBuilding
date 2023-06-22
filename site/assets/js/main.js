/* ****** Функции для отправки запросов и получения апишки ****** */
console.log("ghbdtn");
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


/* ---------- ВАЛИДАЦИЯ ФОРМЫ ДОБАВЛЕНИЯ ТОВАРОВ ---------- */

const formAddProduct = document.getElementById('form-add-product');

// запишем значения полей формы в переменные
let  vendorId = formAddProduct.querySelector('#vendorId');
let  nameProduct = formAddProduct.querySelector('#name');
let  photoName = formAddProduct.querySelector('#photo');
let  brandId = formAddProduct.querySelector('#brandId');
let  categoryId = formAddProduct.querySelector('#categoryId');
let  description = formAddProduct.querySelector('#description');
let  article = formAddProduct.querySelector('#article');
let  quantityAvailable = formAddProduct.querySelector('#quantityAvailable');
let  price = formAddProduct.querySelector('#price');
let  maxPrice = formAddProduct.querySelector('#maxPrice');

let priceValue;
if (price.value) {
    priceValue = Number(price.value);
} else {priceValue = 0;}

let file; 
let photoFileData;
let photoFileName;
// brandId.addEventListener("change", () => {
//     brandId.style.opacity = "1";
// })
// categoryId.addEventListener("change", () => {
//     categoryId.style.color = "1";
// })

function addProduct() {
    //предотвратить дефолтные действия, отмена отправки формы (чтобы страница не перезагружалась)
    event.preventDefault(); 

    let hasError = false; 

    // валидация полей (кроме vendorId)
    [nameProduct, photoName, brandId, categoryId, description, article, 
    quantityAvailable, price, maxPrice].forEach(item => {

        console.log(item.getAttribute('name') + "    " + item.value);

        const errorInfoContainer = item.closest('.form-add-product__elements__item').querySelector('.error-info');
        
        if (!(item.value.trim())) {
            // пустое поле
            item.classList.add('error');   
            errorInfoContainer.innerText = "Заполните данные!";
            errorInfoContainer.classList.remove('d-none');
            hasError = true;
            
        } else if ((item.id === "price") || (item.id === "maxPrice") || (item.id === "quantityAvailable")) {

            // не пустое поле и числовое значение д.б. >= 0
            if (!(Number(item.value) >= 0)) {

                item.classList.add('error');   
                errorInfoContainer.innerText = "Значение Не может быть отрицательным!";
                errorInfoContainer.classList.remove('d-none');
                hasError = true;

            } else if ((item.id === "price")){

                item.classList.remove('error');
                errorInfoContainer.innerText = "";
                errorInfoContainer.classList.add('d-none');

            } else if ((item.id === "maxPrice")) {

                // если >= 0 , запишем цену рыночную в переменную
                let maxPriceValue = Number(item.value);

                // и сравним с ценой продавца
                // цена продавца д.б. меньше рыночной

                if (priceValue > maxPriceValue) {
                    
                    item.classList.add('error');   
                    errorInfoContainer.innerText = "Рыночная цена меньше вашей!";
                    errorInfoContainer.classList.remove('d-none');
                    price.classList.add('error');
                    hasError = true; 

                } else {
                    item.classList.remove('error');
                    errorInfoContainer.innerText = "";
                    errorInfoContainer.classList.add('d-none');
                }
            } else {
                item.classList.remove('error');
                errorInfoContainer.innerText = "";
                errorInfoContainer.classList.add('d-none');
            }
        } else if ((item.id === "photo")) {
            file = photoName.files[0];
            // если картинка, то проверим расширение 
            let photoSplit = item.value.split('.');
            let fileExtension =  photoSplit[photoSplit.length-1];

            if (!(fileExtension === 'jpg' || fileExtension === 'jpeg' || fileExtension === 'png')) {
                item.classList.add('error');   
                errorInfoContainer.innerText = "Недопустимый формат файла!";
                errorInfoContainer.classList.remove('d-none');
                hasError = true;

            // здесь надо определить максимальный разрешённый размер файла
            } else if (file.size > (1 * 1024 * 1024)) {
                item.classList.add('error');   
                errorInfoContainer.innerText = "Размер файла не должен превышать 1Мб";
                errorInfoContainer.classList.remove('d-none');
                hasError = true;

            } else {
                item.classList.remove('error');
                errorInfoContainer.innerText = "";
                errorInfoContainer.classList.add('d-none');
            }

        } else {

            item.classList.remove('error');
            errorInfoContainer.innerText = "";
            errorInfoContainer.classList.add('d-none');
        }

    })

    // если были ошибки, то выходим
    console.log(hasError);
    if (hasError) {
        return;
    }
        

    // кодируем файл с изображением
    


    



     let obj = JSON.stringify({
        photoFileData,
        priceValue
     });


    sendRequestPOST('http://localhost/pages/test.php', obj);

    // соберём json для передачи на сервер


    // передаём данные на сервер


    // получаем ответ с сервера


        // const url = "http://localhost/pages/test.php";
        // let json = sendRequestPOST(url, params);
        // let data = JSON.parse(json);


        



}

/* ---------- КОДИРОВАНИЕ ФАЙЛА В base64 ---------- */

const loadFile = () => {
    let fileReader = new FileReader();
    let progressBar = document.getElementById('progress');
    file = photoName.files[0];

    progressBar.value = 0;
    fileReader.onprogress = (event) => {
        progressBar.value = event.loaded;
        progressBar.max = event.total;
    }

    fileReader.onload = (event) => {
        photoFileData = fileReader.result;
        photoFileName = file.name;
    }

    photoFileData = null;
    photoFileName = null;
    fileReader.readAsDataURL(file);
}


