console.log("подключили edit-product.js", mainUrl);

let photo = document.getElementById("photo");
let productId = formAddProduct.getAttribute('product-id');

// первоначальные цены
let priceOld = price.getAttribute('price-old');
let maxPriceOld = max_price.getAttribute('max-price-old');


/* ---------- РЕДАКТИРОВАНИЕ ТОВАРОВ ---------- */

function editProduct(role) {
        
    // проверяем корректность токена
    check();

    //предотвратить дефолтные действия, отмена отправки формы (чтобы страница не перезагружалась)
    event.preventDefault(); 

    hasError = validationEdit();

    // если были ошибки, то выходим
    if (hasError) {
        console.log('oшибки');
        return;
    }
    
    let obj;
    
    // соберём json для передачи на сервер

    if (!new_photo.value) {
        obj = {
            'id': productId,
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
            'is_active': is_active.value,
            'photo': photo.value
        };
    } else {
        obj = {
            'id': productId,
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
            'is_active': is_active.value,
            photoFileData,
            photoFileName
        };
    }

    // утверждён ли продукт?
    // если товар изменяет поставщик (именно цену), а не админ, то этот товар переводим в 0 до утверждения админом (поле is_confirm)
    if (role !== 1) {
        if (!(price.value == priceOld && max_price.value == maxPriceOld)) {  
            obj['is_confirm'] = 0;
        }
    } else {
        obj['is_confirm'] = formAddProduct.querySelector('#is_confirm').value;;
    }
    
    obj = JSON.stringify(obj);

    console.log(obj);
    // передаём данные на сервер
    sendRequestPOST(mainUrl + '/api/products.php', obj);

    // если товар изменяет поставщик, а не админ, то проверяем менял ли он цену
    // и если да, то поставщика переводим в 0 до подтверждения цен
    // if (role == 2) {
    //     if (!(price.value == priceOld && max_price.value == maxPriceOld)) {        
    //         // отправим запрос на изменение статуса подтверждения цен поставщика
    //         let objVendor = JSON.stringify({
    //             'id': vendor_id.value,
    //             'price_confirmed':  0
    //         });
    //         sendRequestPOST(mainUrl + '/api/vendors.php', objVendor);
    //     }
    // }

    // получаем ответ с сервера
    alert("Данные изменены");

    // перезагрузим страницу
    window.location.href = window.location.href;
}

/* ---------- ВАЛИДАЦИЯ ФОРМЫ РЕДАКТИРОВАНИЯ ТОВАРОВ ---------- */

function validationEdit() {
    hasError = false; 

    if (price.value) {
        priceValue = Number(price.value);
    } else {priceValue = 0;}

    // валидация наименования и описания
    // должно быть заполнено хотя бы одно поле наименования и соответвующее поле описания, 
    // могут быть заполнены все
    if(!nameProduct.value.trim() && !nameProduct2.value.trim() && !nameProduct3.value.trim()) {
        alert("Нет наименования");
        hasError = true;
        return hasError;
    } else if(!description.value.trim() && !description2.value.trim() && !description3.value.trim()) {
        alert("Нет описания");
        hasError = true;
        return hasError;
    } else if((nameProduct.value.trim() && !description.value.trim()) || (nameProduct2.value.trim() && !description2.value.trim()) || (nameProduct3.value.trim() && !description3.value.trim())) {
        alert("Нет подходящего описания");
        hasError = true;
        return hasError;
    } else if((!nameProduct.value.trim() && description.value.trim()) || (!nameProduct2.value.trim() && description2.value.trim()) || (!nameProduct3.value.trim() && description3.value.trim())) {
        alert("Нет подходящего наименования");
        hasError = true;
        return hasError;
    } 

   // валидация полей (кроме vendorId)
   [brand_id, category_id, unit_id, 
    quantity_available, price, max_price, is_active].forEach(item => {

        // console.log(item.getAttribute('name') + "    " + item.value);

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

    // валидация полей цен в долларах 
    if (price.classList.contains('error') || (!price_dollar.value)) {
        price_dollar.classList.add('error');
        hasError = true;
    } else {
        price_dollar.classList.remove('error');
    }

    if (max_price.classList.contains('error') || (!max_price_dollar.value)) {
        max_price_dollar.classList.add('error');
        hasError = true;
    } else {
        max_price_dollar.classList.remove('error');
    }

    return hasError;

}

/* ---------- УДАЛЕНИЕ ТОВАРА СО СТРАНИЦЫ РЕДАКТИРОВАНИЯ ТОВАРА ---------- */

function deleteProductFromEditForm(id) {
        
    // проверяем корректность токена
    check();

    // запрашиваем подтверждение удаления
    let isDelete = false;

    isDelete = window.confirm('Вы действительно хотите удалить этот товар?');

    if(!isDelete) {
        return;
    }

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
