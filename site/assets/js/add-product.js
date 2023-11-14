console.log("подключили add-product.js", mainUrl);

const formAddProduct = document.getElementById('form-add-product');

// запишем значения полей формы в переменные
let  vendor_id = formAddProduct.querySelector('#vendor_id');
let  nameProduct = formAddProduct.querySelector('#name');  //название продукта русский
let  nameProduct2 = formAddProduct.querySelector('#name2'); //название продукта Оʻzbek
let  nameProduct3 = formAddProduct.querySelector('#name3'); //название продукта Ўзбек
let  new_photo = formAddProduct.querySelector('#new_photo');
let  brand_id = formAddProduct.querySelector('#brand_id');
let  category_id = formAddProduct.querySelector('#category_id');
let  description = formAddProduct.querySelector('#description'); //описание русский
let  description2 = formAddProduct.querySelector('#description2'); //описание Оʻzbek
let  description3 = formAddProduct.querySelector('#description3'); //описание Ўзбек
let  article = formAddProduct.querySelector('#article');
let  quantity_available = formAddProduct.querySelector('#quantity_available');
let  price = formAddProduct.querySelector('#price');
let  price_dollar = formAddProduct.querySelector('#price_dollar');
let  max_price = formAddProduct.querySelector('#max_price');
let  max_price_dollar = formAddProduct.querySelector('#max_price_dollar');
let  unit_id = formAddProduct.querySelector('#unit_id');
let  is_active = formAddProduct.querySelector('#is_active');

let priceValue;
let hasError;
let file; 
let photoFileData;
let photoFileName;


/* ---------- ДОБАВЛЕНИЕ ТОВАРОВ ---------- */

function addProduct(role) {
    
    // проверяем корректность токена
    check();

    //предотвратить дефолтные действия, отмена отправки формы (чтобы страница не перезагружалась)
    event.preventDefault(); 

    // если поставщик не выбран, то не пропускаем дальше (это для страницы админа
    // тк там блок изменения цен зависит от валюты поставщика, если он не выбран, то блока нет)
    if (!vendor_id.value) {
        alert('Выберите поставщика!');
        return;
    }

    // валидация полей
    hasError = validationAdd();

    // если были ошибки, то выходим
    console.log(hasError);
    if (hasError) {
        return;
    }
    
    // соберём json для передачи на сервер

    // утверждён ли продукт?
    // если товар добавляет поставщик, а не админ, то этот товар переводим в 0 до утверждения админом (поле is_confirm)
    let confirmProduct;
    if (role !== 1) {
        confirmProduct = 0;
    }

    let obj = JSON.stringify({
        'vendor_id': vendor_id.value,
        'name':  nameProduct.value,
        'name2':  nameProduct2.value,
        'name3':  nameProduct3.value,
        'brand_id': brand_id.value,
        'category_id': category_id.value,
        'description': description.value,
        'description2': description2.value,
        'description3': description3.value,
        'article': article.value,
        'quantity_available': quantity_available.value,
        'price': price.value,
        'price_dollar': price_dollar.value,
        'max_price': max_price.value,
        'max_price_dollar': max_price_dollar.value,
        'unit_id': unit_id.value,
        'deleted': 0,
        'is_active': is_active.value,
        'is_confirm': confirmProduct,
        photoFileData,
        photoFileName
    });


    // передаём данные на сервер
    sendRequestPOST(mainUrl + '/api/products.php', obj);

    // если товар добавляет поставщик, а не админ, то поставщика переводим в 0 до подтверждения цен
    // if (role == 2) {
    //     // отправим запрос на изменение статуса подтверждения цен поставщика
    //     let objVendor = JSON.stringify({
    //         'id': vendor_id.value,
    //         'price_confirmed':  0
    //     });
    //     sendRequestPOST(mainUrl + '/api/vendors.php', objVendor);
    // }

    alert("Данные отправлены");
    // перезагрузим страницу
    window.location.href = window.location.href;
}

/* ---------- ВАЛИДАЦИЯ ФОРМЫ ДОБАВЛЕНИЯ ТОВАРОВ ---------- */

function validationAdd() {
    
    hasError = false; 

    if (price.value) {
        priceValue = Number(price.value);
    } else {priceValue = 0;}

    // валидация полей (кроме vendorId)
    [nameProduct, description, new_photo, brand_id, category_id, unit_id,
        quantity_available, price, max_price, vendor_id, is_active].forEach(item => {
    
            const errorInfoContainer = item.closest('.form-add-product__elements-item').querySelector('.error-info');
            
            if (!(item.value.trim())) {
                // пустое поле
                if (item.id === "new_photo") {
                    choiceImage.classList.add('error');
                    hasError = true;
                } else {
                    item.classList.add('error');   
                    errorInfoContainer.innerText = "Заполните данные!";
                    errorInfoContainer.classList.remove('d-none');
                    hasError = true;
                }
                
            } else if ((item.id === "price") || (item.id === "max_price") || (item.id === "quantity_available")) {
    
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
    
                    if (priceValue >= maxPriceValue) {
                        console.log('больше');
                        item.classList.add('error');   
                        errorInfoContainer.innerText = "Эта цена должна быть больше вашей!";
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
            } else if ((item.id === "new_photo")) {
                file = new_photo.files[0];
                // если картинка, то проверим расширение 
                let photoSplit = item.value.split('.');
                let fileExtension =  photoSplit[photoSplit.length-1];
    
                if (!(fileExtension === 'jpg' || fileExtension === 'jpeg' || fileExtension === 'png')) {
                    choiceImage.classList.add('error');
                    errorInfoContainer.innerText = "Недопустимый формат файла!";
                    errorInfoContainer.classList.remove('d-none');
                    hasError = true;
    
                // здесь надо определить максимальный разрешённый размер файла
                } else if (file.size > (20 * 1024 * 1024)) {
                    choiceImage.classList.add('error');
                    errorInfoContainer.innerText = "Размер файла не должен превышать 20Мб";
                    errorInfoContainer.classList.remove('d-none');
                    hasError = true;
    
                } else {
                    choiceImage.classList.remove('error');
                    errorInfoContainer.innerText = "";
                    errorInfoContainer.classList.add('d-none');
                }
    
            } else {
    
                item.classList.remove('error');
                errorInfoContainer.innerText = "";
                errorInfoContainer.classList.add('d-none');
            }
    
        })

        if (price.classList.contains('error')) {
            price_dollar.classList.add('error');
        } else {
            price_dollar.classList.remove('error');
        }

        if (max_price.classList.contains('error')) {
            max_price_dollar.classList.add('error');
        } else {
            max_price_dollar.classList.remove('error');
        }

        return hasError;
}
/* ---------- КОДИРОВАНИЕ ФАЙЛА В base64 ---------- */

const loadFile = () => {
    let fileReader = new FileReader();
    let progressBar = document.getElementById('progress');
    file = new_photo.files[0];

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

/* ---------- ПРЕДПРОСМОТР ИЗОБРАЖЕНИЯ ---------- */

let imagePreview = document.querySelector('.form-add-product__elements-item__img-prew');
let choiceImage = document.querySelector('.form-add-product__elements-item__img');
const handleFilePreview = (e) => {
    console.log("prew");
  let files = e.target.files;

  imagePreview.querySelector('img').remove();

  let image = document.createElement('img');
  image.src = window.URL.createObjectURL(files[0]);
  imagePreview.appendChild(image);

}

new_photo.addEventListener('change', handleFilePreview);

/* ---------- ПЕРЕСЧЁТ ЦЕН В СУМЫ ---------- */

function calcPriceUzs() {

    // запишем курс в переменную
    let rate = event.target.getAttribute('rate');

    // куда записывать пересчитанную сумму в сумах
    let priceUzsEl = event.target.closest('.form-add-product__elements-item').querySelector('.price-uzs');

    let priceUzs = rate * event.target.value;
    priceUzsEl.innerHTML = "<b>" + priceUzs + "</b>";

    if (event.target.value === '') {
        event.target.closest('.form-add-product__elements-item').querySelector('.price-value').value = '';
    } else {
        event.target.closest('.form-add-product__elements-item').querySelector('.price-value').value = priceUzs;
    }
    

}

/* ---------- ПЕРЕРИСОВКА БЛОКА С ИЗМЕНЕНИЕМ ЦЕН В ЗАВИСИМОСТИ ОТ ВАЛЮТЫ ПОСТАВЩИКА (У АДМИНА) ---------- */

function renderPriceBlock() {

    // контейнер для блока изменения цен
    let blockPrice = document.getElementById('block-price');

    // шаблоны изменения цен
    let tmplPriceUzs = document.getElementById('tmpl-price-uzs');
    let tmplPriceUsd = document.getElementById('tmpl-price-usd');

    let currencyTmp;
    let rateTemp;

    // после выбора поставщика в списке, определим какая у него валюта цен
    for (let i = 0; i < vendor_id.options.length; i++) {
        if(vendor_id.options[i].selected){
            rateTemp = vendor_id.options[i].getAttribute('rate');
            currencyTmp = vendor_id.options[i].getAttribute('currency');
            break;
        };
    }

    // если валюта доллар, то отрисуем шаблон изменения цен в долларах
    if (currencyTmp == "1") {
        blockPrice.innerHTML = tmplPriceUsd.innerHTML   .replace('${rate}', rateTemp)
                                                        .replace('${rate}', rateTemp);
    } else {
        blockPrice.innerHTML = tmplPriceUzs.innerHTML;
    }

    price = formAddProduct.querySelector('#price');
    price_dollar = formAddProduct.querySelector('#price_dollar');
    max_price = formAddProduct.querySelector('#max_price');
    max_price_dollar = formAddProduct.querySelector('#max_price_dollar');

}