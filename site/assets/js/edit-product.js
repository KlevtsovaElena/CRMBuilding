console.log("подключили edit-product.js", mainUrl);

let photo = document.getElementById("photo");
let productId = formAddProduct.getAttribute('product-id');

// первоначальные цены
let priceOld = price.getAttribute('price-old');
let maxPriceOld = max_price.getAttribute('max-price-old');

function editProduct(role) {
        
    // проверяем корректность токена
    check();

    //предотвратить дефолтные действия, отмена отправки формы (чтобы страница не перезагружалась)
    event.preventDefault(); 

    hasError = validationEdit();

    // если были ошибки, то выходим
    console.log(hasError);
    if (hasError) {
        return;
    }
    
    let obj;
    
    // соберём json для передачи на сервер
    if (!new_photo.value) {
        obj = JSON.stringify({
            'id': productId,
            'name':  nameProduct.value,
            'brand_id': brand_id.value,
            'category_id': category_id.value,
            'description': description.value,
            'article': article.value,
            'quantity_available': quantity_available.value,
            'price': price.value,
            'max_price': max_price.value,
            'unit_id': unit_id.value,
            'photo': photo.value
        });
    } else {
        obj = JSON.stringify({
            'id': productId,
            'name':  nameProduct.value,
            'brand_id': brand_id.value,
            'category_id': category_id.value,
            'description': description.value,
            'article': article.value,
            'quantity_available': quantity_available.value,
            'price': price.value,
            'max_price': max_price.value,
            'unit_id': unit_id.value,
            photoFileData,
            photoFileName
        });
    }

    console.log(obj);
    // передаём данные на сервер
    sendRequestPOST(mainUrl + '/api/products.php', obj);

    // если товар изменяет поставщик, а не админ, то проверяем менял ли он цену
    // и если да, то поставщика переводим в 0 до подтверждения цен
    if (role == 2) {
        if (!(price.value == priceOld && max_price.value == maxPriceOld)) {        
            // отправим запрос на изменение статуса подтверждения цен поставщика
            let objVendor = JSON.stringify({
                'id': vendor_id.value,
                'price_confirmed':  0
            });
            sendRequestPOST(mainUrl + '/api/vendors.php', objVendor);
        }
    }

    // получаем ответ с сервера
    alert("Данные изменены");

}

function validationEdit() {
    hasError = false; 

    if (price.value) {
        priceValue = Number(price.value);
    } else {priceValue = 0;}

   // валидация полей (кроме vendorId)
   [nameProduct, brand_id, category_id, unit_id, 
    quantity_available, price, max_price].forEach(item => {

        console.log(item.getAttribute('name') + "    " + item.value);

        const errorInfoContainer = item.closest('.form-add-product__elements-item').querySelector('.error-info');
        
        if (!(item.value.trim())) {

            // пустое поле
            item.classList.add('error');   
            errorInfoContainer.innerText = "Заполните данные!";
            errorInfoContainer.classList.remove('d-none');
            hasError = true;
            
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
                if (priceValue >= maxPriceValue) {
                    item.classList.add('error');   
                    errorInfoContainer.innerText = "Эта цена должна быть больше вашей!";
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
        } else {

            item.classList.remove('error');
            errorInfoContainer.innerText = "";
            errorInfoContainer.classList.add('d-none');
        }

})

// проверка фото, либо передавать картинку с загрузкой, либо передавать ссылку поля photo
if (!new_photo.value) {
    if (!photo.value) {
        // пустое поле
        const errorInfoContainer = new_photo.closest('.form-add-product__elements-item').querySelector('.error-info');
        choiceImage.classList.add('error');   
        errorInfoContainer.innerText = "Заполните данные!";
        errorInfoContainer.classList.remove('d-none');
        hasError = true;
    } 
} else {
    file = new_photo.files[0];
    // если картинка, то проверим расширение 
    let photoSplit = new_photo.value.split('.');
    let fileExtension =  photoSplit[photoSplit.length-1];
    const errorInfoContainer = new_photo.closest('.form-add-product__elements-item').querySelector('.error-info');
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
}

    return hasError;

}

function deleteProductFromEditForm(id) {
        
    // проверяем корректность токена
    check();
    
    console.log(id);

    // запрашиваем подтверждение удаления
    let isDelete = false;

    isDelete = window.confirm('Вы действительно хотите удалить этот товар?');

    if(!isDelete) {
        console.log(" ни в коем случае");
        return;
    }

    // если подтвердили удаление
    console.log("удаляем");

    // соберём json
    let obj = JSON.stringify({
        'id': id,
        'deleted':  1
    });

    // делаем запрос на удаление товара по id
    sendRequestPOST(mainUrl + '/api/products.php', obj);


    // делаем запрос на удаление товара по id
    // sendRequestDELETE(mainUrl + '/api/products.php?id=' + id);

    alert("Товар удалён");

    // получим гет параметры страницы без id
    let paramsArr = window.location.href.split('?')[1].split('&');
    paramsArr.splice(0, 1);
    let params = paramsArr.join('&');

    // переход обратно на странницу списка товаров с прежними параметрами
    if (window.location.href.includes('vendor-edit-product')) {
        window.location.href = mainUrl + '/pages/vendor-list-products.php?' + params;
    } else if (window.location.href.includes('admin-edit-product')) {
        window.location.href = mainUrl + '/pages/admin-list-products.php?' + params;
    }
}
