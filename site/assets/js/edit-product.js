console.log("подключили edit-product.js");

let photo = document.getElementById("photo");
let productId = formAddProduct.getAttribute('product-id');

let odj;

function editProduct() {
    //предотвратить дефолтные действия, отмена отправки формы (чтобы страница не перезагружалась)
    event.preventDefault(); 

    hasError = validationEdit();

    // если были ошибки, то выходим
    console.log(hasError);
    if (hasError) {
        return;
    }
    
    
    // соберём json для передачи на сервер
    if (!new_photo.value) {
        obj = JSON.stringify({
            'id': productId,
            'vendor_id': vendor_id.value,
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
            'vendor_id': vendor_id.value,
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
    sendRequestPOST('http://localhost/api/products.php', obj);

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

    // делаем запрос на удаление товара по id
    sendRequestDELETE('http://localhost/api/products.php?id=' + id);

    alert("Товар удалён");

    // получим гет параметры страницы без id
    let paramsArr = window.location.href.split('?')[1].split('&');
    paramsArr.splice(0, 1);
    let params = paramsArr.join('&');

    // переход обратно на странницу списка товаров с прежними параметрами
    window.location.href = 'http://localhost/pages/vendor-list-products.php?' + params;
}
