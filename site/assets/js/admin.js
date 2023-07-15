console.log('works');

//функция добавления
function addNew(section_name) {

    //достаем из инпута введенное название новой категории или бренда
    let name = document.getElementById('add-new').value;
    console.log(name);
    console.log(section_name);

    if(name.trim()) {

        // собираем ссылку для запроса
        link = 'http://localhost/api/'+ section_name + '.php';

        // соберём json для передачи на сервер
        //для категорий
        if(section_name == 'categories') {
            obj = JSON.stringify({
                'category_name': name,
                'deleted': 0
            });
            console.log(obj);
        }
        //для брендов
        if (section_name == 'brands') {
            obj = JSON.stringify({
                'brand_name': name,
                'deleted': 0
            });
        }

        //для городов или поставщиков
        if (section_name == 'cities' || section_name == 'vendors') {
            obj = JSON.stringify({
                'name': name,
                'deleted': 0
            });
        }

        //передаем на сервер в пост-запросе
        sendRequestPOST(link, obj);

        //очищаем инпут
        document.getElementById('add-new').value = '';

        //вынимаем информацию о кол-ве страниц
        let lastPage = document.getElementById('pages-info').getAttribute('data-pages');

        //передаем в адресную строку изменения, чтобы сразу их видеть
        history.replaceState(history.length, null, 'admin.php?section=' + section_name + '&limit=' + limit.value + '&page=' + lastPage);

        document.location.href = 'http://localhost/pages/admin.php?section=' + section_name + '&limit=' + limit.value +  '&page=' + lastPage;
    }
    
}

//функция активации редактируемого инпута для названия
function edit(id, section_name) {

    //достаем массив всех элементов, содержащих редактируемое название
    let nameElements = document.querySelectorAll('.list-orders_status');
    console.log(nameElements);

    //меняем поле с названием на редактируемый инпут
    for (let i = 0; i < nameElements.length; i++) {
        if (nameElements[i].getAttribute('data-id') == id) {
            //вынимаем текстовое значение, которое будет редактироваться
            let editedValue = nameElements[i].innerHTML;
            //очищаем поле, откуда взяли название
            nameElements[i].innerText = '';
            console.log(editedValue);
            //создаем инпут
            let input = document.createElement('input');
            input.type = 'text';
            //вешаем его внутрь обычной нередактируемой строки
            nameElements[i].appendChild(input);
            //переносим имеющееся  название внутрь инпута для редактирования
            input.value = editedValue;

            input.onclick = function() {

                input.onblur = function() {
                    //возвращаем обратно нередактируемую строку
                    //убираем добавленный инпут
                    nameElements[i].removeChild(input);
                    //и возвращаем измененное значение в исходный нередактируемый элемент
                    nameElements[i].innerText = editedValue;
                }

                //при изменении в инпуте
                input.onchange = function() {

                let changedValue = input.value;
                let name = changedValue;

                // собираем ссылку для запроса
                link = 'http://localhost/api/'+ section_name + '.php';

                // соберём json для передачи на сервер
                //для категорий
                if(section_name == 'categories') {
                    obj = JSON.stringify({
                        'id': id,
                        'category_name': name
                    });
                    console.log(obj);
                }

                //для брендов
                if (section_name == 'brands') {
                    obj = JSON.stringify({
                        'id': id,
                        'brand_name': name
                    });
                }

                //для городов или поставщиков
                if (section_name == 'cities' || section_name == 'vendors') {
                    obj = JSON.stringify({
                        'id': id,
                        'name': name
                    });
                }

                console.log(obj);

                //передаем на сервер в пост-запросе
                sendRequestPOST(link, obj);

                //возвращаем обратно нередактируемую строку
                //убираем добавленный инпут
                nameElements[i].removeChild(input);
                //и возвращаем измененное значение в исходный нередактируемый элемент
                nameElements[i].innerText = changedValue;
        };
            }

            
           
        }
    }

}

//функция софт-удаления
function deleteOne(section_name, id) {

    //отправка запроса на удаление (жесткое)
    // sendRequestDELETE('http://localhost/api/'+ section_name +'.php?id=' + id);

    // собираем ссылку для запроса
    link = 'http://localhost/api/'+ section_name + '.php';

    if (section_name == 'admin-vendors') {
        // соберём json для передачи на сервер
        obj = JSON.stringify({
            'id': id,
            'deleted': 1,
            'is_active': 0
        });
    } else {
        // соберём json для передачи на сервер
        obj = JSON.stringify({
            'id': id,
            'deleted': 1
        });
    }

    console.log(obj);

    //передаем на сервер в пост-запросе
    sendRequestPOST(link, obj);

    //вынимаем информацию о номере текущей страницы
    let currentPage = document.getElementById('pages-info').getAttribute('data-current-page');

    if (section_name == 'admin-vendors') {
        //передаем в адресную строку изменения, чтобы сразу их видеть на последней странице
        history.replaceState(history.length, null, 'admin-vendors.php?limit=' + limit.value + '&page=' + currentPage);

        document.location.href = 'http://localhost/pages/admin-vendors.php?limit=' + limit.value + '&page=' + currentPage;
    } else {
        //передаем в адресную строку изменения, чтобы сразу их видеть на последней странице
        history.replaceState(history.length, null, 'admin.php?section=' + section_name + '&limit=' + limit.value + '&page=' + currentPage);

        document.location.href = 'http://localhost/pages/admin.php?section=' + section_name + '&limit=' + limit.value +  '&page=' + currentPage;
    }


}

//функция при нажатии на кнопку "Применить"
function apply(section_name) {

    console.log(limit.value);

    //лимит задан всегда, поэтому проверяем только наличие поискового запроса
    //получим введенное в поиск значение
    let searchQuery = document.getElementById('search').value;

    // проверим значение поиска
    if(searchQuery.trim()) {
        console.log(searchQuery);

        //если мы на странице admin-vendors
        if (section_name == 'admin-vendors') {

            //вносим изменение в адресную строку страницы
            history.replaceState(history.length, null, 'admin-vendors.php?limit=' + limit.value + '&search=name:' + searchQuery.trim());

            document.location.href = 'http://localhost/pages/admin-vendors.php?limit=' + limit.value + '&search=name:' +  searchQuery.trim();

        } else {

            let key = document.querySelector('.page-title').getAttribute('data-name');

            //вносим изменение в адресную строку страницы
            history.replaceState(history.length, null, 'admin.php?section=' + section_name + '&limit=' + limit.value + '&search=' + key + ':' + searchQuery.trim());

            document.location.href = 'http://localhost/pages/admin.php?section=' + section_name + '&limit=' + limit.value + '&search=' + key + ':' + searchQuery.trim();
        }

        
        
    } else {

    //если мы на странице admin-vendors
    if (section_name == 'admin-vendors') {

        //вносим изменение в адресную строку страницы
        history.replaceState(history.length, null, 'admin-vendors.php?limit=' + limit.value);

        document.location.href = 'http://localhost/pages/admin-vendors.php?limit=' + limit.value;

    } else {

        //вносим изменение в адресную строку страницы
        history.replaceState(history.length, null, 'admin.php?section=' + section_name + '&limit=' + limit.value);

        document.location.href = 'http://localhost/pages/admin.php?section=' + section_name + '&limit=' + limit.value;
    }
}

        // let select = document.getElementById('limit');

        // let selected = [];

        // for (let i = 0; i < select.options.length; i++) {
        //     selected[i] = select.options[i].selected;
        //     if(selected[i]) {
        //         let selectedValue = select.options[i].value;
        //     }
        //     console.log(selected[i]);
        // }
        // select.value = selectedValue;


        // let result = limit.value; // The value you want to transfer
        // let xhr = new XMLHttpRequest();
        // xhr.open("POST", "http://localhost/pages/admin.php?categories", true);
        // xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        // xhr.onreadystatechange = function() {
        // if (xhr.readyState === 4 && xhr.status === 200) {
        //     console.log(xhr.responseText); // Response from PHP script
        // }
        // };
        // xhr.send("limit=" + result);


}

//функция при нажатии на кнопку "Применить" для admin-orders!!!
function applyInOrders() {

    console.log(limit.value);

    //лимит задан всегда, поэтому проверяем только наличие поискового запроса
    //получим введенное в поиск значение
    let searchQuery = document.getElementById('search').value;

    // проверим значение поиска
    if(searchQuery.trim()) {
        console.log(searchQuery);

        //вносим изменение в адресную строку страницы
        history.replaceState(history.length, null, 'admin-orders.php?limit=' + limit.value + '&search=order_id:' + searchQuery.trim());

        document.location.href = 'http://localhost/pages/admin-orders.php?limit=' + limit.value + '&search=order_id:' + searchQuery.trim();
        
    } else {
        // соберём json для передачи на сервер
        // let obj = JSON.stringify({
        //     'limit': limit.value
        // });

        //let link = 'http://localhost/limit.php?limit=';

        //let result = sendRequestGET(link+limit.value);
        //console.log(result);

    //вносим изменение в адресную строку страницы
    history.replaceState(history.length, null, 'admin-orders.php?limit=' + limit.value);

    document.location.href = 'http://localhost/pages/admin-orders.php?limit=' + limit.value;

    }

}

/* ---------- ПЕРЕХОД И ПЕРЕДАЧА ПАРАМЕТРОВ ФИЛЬТРАЦИИ НА СТРАНИЦУ редактирования---------- */
function editVendor(id) {

    let getParam = window.location.search;

    // при переходе на страницу редактирования товара передаём ещё и параметры фильтрации в get
    window.location.href = "http://localhost/pages/admin-edit-vendor.php?id=" + id + "&deleted=0" + getParam.replace('?', '&'); 
    
}

