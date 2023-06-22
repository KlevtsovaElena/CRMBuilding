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
let  vendor_id = formAddProduct.querySelector('#vendor_id');
let  nameProduct = formAddProduct.querySelector('#name');
let  photo = formAddProduct.querySelector('#photo');
let  brand_id = formAddProduct.querySelector('#brand_id');
let  category_id = formAddProduct.querySelector('#category_id');
let  description = formAddProduct.querySelector('#description');
let  article = formAddProduct.querySelector('#article');
let  quantity_available = formAddProduct.querySelector('#quantity_available');
let  price = formAddProduct.querySelector('#price');
let  max_price = formAddProduct.querySelector('#max_price');

let priceValue;




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
    if (price.value) {
        priceValue = Number(price.value);
    } else {priceValue = 0;}

    // валидация полей (кроме vendorId)
    [nameProduct, photo, brand_id, category_id, description, article, 
    quantity_available, price, max_price].forEach(item => {

        console.log(item.getAttribute('name') + "    " + item.value);

        const errorInfoContainer = item.closest('.form-add-product__elements__item').querySelector('.error-info');
        
        if (!(item.value.trim())) {
            // пустое поле
            item.classList.add('error');   
            errorInfoContainer.innerText = "Заполните данные!";
            errorInfoContainer.classList.remove('d-none');
            hasError = true;
            
        } else if ((item.id === "price") || (item.id === "max_price") || (item.id === "quantity_available") || (item.id === "article")) {

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

            } else if ((item.id === "max_price")) {

                // если >= 0 , запишем цену рыночную в переменную
                let maxPriceValue = Number(item.value);

                // и сравним с ценой продавца
                // цена продавца д.б. меньше рыночной
                console.log (priceValue, maxPriceValue);
                console.log (typeof(priceValue), typeof(maxPriceValue));
                console.log(priceValue > maxPriceValue);

                if (priceValue > maxPriceValue) {
                    console.log('больше');
                    item.classList.add('error');   
                    errorInfoContainer.innerText = "Рыночная цена меньше вашей!";
                    errorInfoContainer.classList.remove('d-none');
                    price.classList.add('error');
                    hasError = true; 

                } else {
                    console.log("не больше");
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
            file = photo.files[0];
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
    
    // соберём json для передачи на сервер

    
     let obj = JSON.stringify({
        vendor_id: vendor_id.value,
        'name':  nameProduct.value,
        brand_id: brand_id.value,
        category_id: category_id.value,
        description: description.value,
        article: article.value,
        quantity_available: quantity_available.value,
        price: price.value,
        max_price: max_price.value,
        photoFileData,
        photoFileName
     });

     console.log(obj);


    // передаём данные на сервер
    //sendRequestPOST('http://localhost/api/products.php', obj);

    // получаем ответ с сервера


}

/* ---------- КОДИРОВАНИЕ ФАЙЛА В base64 ---------- */

const loadFile = () => {
    let fileReader = new FileReader();
    let progressBar = document.getElementById('progress');
    file = photo.files[0];

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


