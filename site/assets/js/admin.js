console.log('файл admin.js подключен');

//функция добавления
function addNew(section_name) {

    //достаем из инпута введенное название новой категории или бренда
    let name = document.getElementById('add-new').value;
    console.log(name);
    console.log(section_name);

    if(name.trim()) {

        // собираем ссылку для запроса
        let link = mainUrl + '/api/'+ section_name + '.php';

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

        document.location.href = mainUrl + '/pages/admin.php?section=' + section_name + '&limit=' + limit.value +  '&page=' + lastPage;
    }
    
}

function edit(id, section_name) {

    //достаем массив всех элементов, содержащих редактируемое название
    let nameElements = document.querySelectorAll('.list-orders_status');
    //console.log(nameElements);

    //переменная для обновленного значения
    //let changedValue = '';

    //переменная для исходного значения
    let uneditedValue = '';

    //меняем поле с названием на редактируемый инпут
    for (let i = 0; i < nameElements.length; i++) {
        //ищем среди них то, у котого атрибут совпадает с нужным id
        if (nameElements[i].getAttribute('data-id') == id) {

            //копируем из нередактируемого поля текстовое значение, которое будет редактироваться в инпуте
            uneditedValue = nameElements[i].innerHTML;
            console.log(uneditedValue);
            //скрываем нередактируемое поле
            nameElements[i].classList.add('d-none');
            //достаем инпут, который надо сделать видимым
            let inputBlock = document.querySelectorAll('.input')[i];
            //console.log(inputBlock);
            //открываем инпут
            inputBlock.classList.remove('d-none');
            //кладем внутрь инпута значение, которое будем редактировать
            inputBlock.querySelector('input').value = uneditedValue;
            input = inputBlock.querySelector('input');

            //скрываем блок с карандашом
            document.getElementsByClassName('edit')[i].classList.add('d-none');
            //открываем блок с отменой
            document.getElementsByClassName('cancel')[i].classList.remove('d-none');
            //открываем блок с сохранением
            document.getElementsByClassName('save')[i].classList.remove('d-none');

        }


    }
}

//функция отмены редактирования (по нажатию на крестик)
function cancel(id, name) {
    
    //достаем массив всех элементов, содержащих редактируемое название
    let nameElements = document.querySelectorAll('.list-orders_status');
    //console.log(nameElements);

    //меняем редактируемый инпут на поле с названием
    for (let i = 0; i < nameElements.length; i++) {
        if (nameElements[i].getAttribute('data-id') == id) {

            //если назначен дата-атрибут с измененным названием, достаем его
            if(nameElements[i].getAttribute('data-new-name')) {
                name = nameElements[i].getAttribute('data-new-name');
            }

            //достаем инпут, который надо скрыть
            let inputBlock = document.querySelectorAll('.input')[i];

            //скрываем инпут и возвращаем нередактируемое поле с названием
            inputBlock.classList.add('d-none');
            nameElements[i].classList.remove('d-none');
            //возвращаем в него изначальное название до редактирования
            nameElements[i].innerHTML = name;
            //скрываем блок с отменой
            document.getElementsByClassName('cancel')[i].classList.add('d-none');
            //скрываем блок с сохранением
            document.getElementsByClassName('save')[i].classList.add('d-none');
            //открываем блок с карандашом
            document.getElementsByClassName('edit')[i].classList.remove('d-none');
            
        }
    }
}

//функция сохранения отредактированного значения
function save(id, uneditedValue, section_name) {
    
    //достаем массив всех элементов, содержащих редактируемое название
    let nameElements = document.querySelectorAll('.list-orders_status');
    //console.log(nameElements);

    let changedValue = '';

    //меняем редактируемый инпут на поле с названием
    for (let i = 0; i < nameElements.length; i++) {
        if (nameElements[i].getAttribute('data-id') == id) {

            //если назначен дата-атрибут с измененным названием, достаем его
            if(nameElements[i].getAttribute('data-new-name')) {
                changedValue = nameElements[i].getAttribute('data-new-name');
            }

            //достаем инпут, который надо скрыть
            let inputBlock = document.querySelectorAll('.input')[i];
            //console.log(inputBlock);
            //console.log(nameElements[i]);

            //копируем измененное значение
            changedValue = inputBlock.querySelector('input').value;

            //если итоговое значение совпадает с исходным, то оно не было изменено, запрос не отправляем
            if (changedValue.trim() === uneditedValue) {
                console.log('значение не было обновлено');
                //скрываем инпут
                inputBlock.classList.add('d-none');
                //возвращаем нередактируемое поле
                nameElements[i].classList.remove('d-none');
                //возвращаем блок с карандашом
                document.getElementsByClassName('edit')[i].classList.remove('d-none');
                //скрываем блок с отменой
                document.getElementsByClassName('cancel')[i].classList.add('d-none');
                //скрываем блок с сохранением
                document.getElementsByClassName('save')[i].classList.add('d-none');
                return;
            //если в поле вместо значения пустота
            } else if (changedValue.trim() === '') {
                alert('Поле не может быть пустым');
                return;
            //если значение действительно было изменено
            } else {
                //запрос подтверждения
                let yes = window.confirm('Вы действительно хотите изменить этот элемент?');

                //при отрицательном ответе ничего не меняем
                if(!yes) {
                    console.log("не изменять");
                    return;
                }
            }
            console.log(changedValue);

            // собираем ссылку для запроса
            link = mainUrl + '/api/'+ section_name + '.php';

            // соберём json для передачи на сервер
            //для категорий
            if(section_name == 'categories') {
                obj = JSON.stringify({
                    'id': id,
                    'category_name': changedValue
                });
            }

            //для брендов
            if (section_name == 'brands') {
                obj = JSON.stringify({
                    'id': id,
                    'brand_name': changedValue
                });
            }

            //для городов
            if (section_name == 'cities') {
                obj = JSON.stringify({
                    'id': id,
                    'name': changedValue
                });
            }

            //для admin-vendors
            if (section_name == 'vendors') {
                obj = JSON.stringify({
                    'id': id,
                    'debt': changedValue
                });
            }

            console.log(obj);

            //передаем на сервер в пост-запросе
            sendRequestPOST(link, obj);

            //записываем только что измененное значение в дата-атрибут
            nameElements[i].setAttribute('data-new-name', changedValue);

            //скрываем инпут и возвращаем нередактируемое поле с названием
            inputBlock.classList.add('d-none');
            nameElements[i].classList.remove('d-none');
            //кладем в него измененное значение
            nameElements[i].innerHTML = changedValue;
            //скрываем блок с отменой
            document.getElementsByClassName('cancel')[i].classList.add('d-none');
            //скрываем блок с сохранением
            document.getElementsByClassName('save')[i].classList.add('d-none');
            //открываем блок с карандашом
            document.getElementsByClassName('edit')[i].classList.remove('d-none');
            
        }
    }
}

//функция, позволяющая вводить в инпут только цифры
function restrictInput(input) {
    input.value = input.value.replace(/\D/g, '');
}

//функция софт-удаления
function deleteOne(section_name, id) {

    //отправка запроса на удаление (жесткое)
    // sendRequestDELETE('http://localhost/api/'+ section_name +'.php?id=' + id);

    let isDelete = window.confirm('Вы действительно хотите удалить этот элемент?');

    if(!isDelete) {
        console.log("не удалять");
        return;
    }

    // собираем ссылку для запроса
    //для удаления поставщиков персональная апишка, чтобы вместе с поставщиком скрывались его товары
    if (section_name == 'vendors') {
        link = mainUrl + '/api/vendors/delete-vendor-with-products.php';
    } else {
        link = mainUrl + '/api/'+ section_name + '.php';
    }
    

    if (section_name == 'vendors') {
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

    if (section_name == 'vendors') {
        //передаем в адресную строку изменения, чтобы сразу их видеть на последней странице
        history.replaceState(history.length, null, 'admin-vendors.php?limit=' + limit.value + '&page=' + currentPage);

        document.location.href = mainUrl + '/pages/admin-vendors.php?limit=' + limit.value + '&page=' + currentPage;
    } else {
        //передаем в адресную строку изменения, чтобы сразу их видеть на последней странице
        history.replaceState(history.length, null, 'admin.php?section=' + section_name + '&limit=' + limit.value + '&page=' + currentPage);

        document.location.href = mainUrl + '/pages/admin.php?section=' + section_name + '&limit=' + limit.value +  '&page=' + currentPage;
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
            history.replaceState(history.length, null, 'admin-vendors.php?limit=all&search=name:' + searchQuery.trim());

            document.location.href = mainUrl + '/pages/admin-vendors.php?limit=all&search=name:' +  searchQuery.trim();

        } else {

            let key = document.querySelector('.page-title').getAttribute('data-name');

            //вносим изменение в адресную строку страницы
            history.replaceState(history.length, null, 'admin.php?section=all&limit=' + limit.value + '&search=' + key + ':' + searchQuery.trim());

            document.location.href = mainUrl + '/pages/admin.php?section=all&limit=' + limit.value + '&search=' + key + ':' + searchQuery.trim();
        }

        
        
    } else {

        //если мы на странице admin-vendors
        if (section_name == 'admin-vendors') {

            //вносим изменение в адресную строку страницы
            history.replaceState(history.length, null, 'admin-vendors.php?limit=' + limit.value);

            document.location.href = mainUrl + '/pages/admin-vendors.php?limit=' + limit.value;

        } else {

            //вносим изменение в адресную строку страницы
            history.replaceState(history.length, null, 'admin.php?section=' + section_name + '&limit=' + limit.value);

            document.location.href = mainUrl + '/pages/admin.php?section=' + section_name + '&limit=' + limit.value;
        }
    }

}

//функция при нажатии на кнопку "Применить" для admin-orders!!!
function applyInOrders() {

    console.log(limit.value);

    //лимит задан всегда, поэтому проверяем наличие поискового запроса и другие селекты
    //получим селект "поставщик"
    let vendorSel = document.getElementById('vendor').querySelectorAll('option:checked')[0].value;
    console.log(vendorSel);

    //получим селект "статус"
    let statusSel = document.getElementById('status').querySelectorAll('option:checked')[0].value;
    console.log(statusSel);

    //получим введенное в поиск значение
    let searchQuery = document.getElementById('search').value;
    let dataSearch = searchQuery.trim();

    //и даты "с"
    let from = sortByDateFrom();
    console.log(from);

    //и даты "по"
    let till = sortByDateTill();
    console.log(till);

    //собираем фильтры (дата + поиск + селекты)
    let filters = '';

    //если задана дата
    if (from || till) {
        //если  С
        if (from) {
            filters += '&date_from=' + from;
        } 
        if (till) {
            //если ДО
            filters += '&date_till=' + till;
        } 
    }

    //если задан поиск
    if (dataSearch) {
        limit.value = 'all';
        filters += '&search=order_id:' + dataSearch;
    } 

    //если задан поставщик
    if (vendorSel) {
        filters += '&vendor_name=' + vendorSel;
    }

    //если задан статус
    if (statusSel) {
        //если выбраны только заказы "В архиве"
        if(statusSel == 5) {
            filters += '&archive=1';
            //сразу на фронте активируем чекбокс
            archiveCheck.checked;
        //если выбраны все заказы
        } else if(statusSel == 'all') {
            //если отмечен чекбокс с архивными записями, выводим заказы с архивными
            if (archiveChecked()) {
                filters.replace('&archive=.', '');
                filters += '&archived';
            //если НЕ отмечен чекбокс с архивными записями, выводим заказы без архивных
            } else {
                filters += '&archive=0';
            }
        //если выбраны заказы 0-4
        } else {
            //если отмечен чекбокс с архивными записями, выводим заказы с архивными
            if (archiveChecked()) {
                filters.replace('&archive=.', '');
                filters += '&archived';
            //если НЕ отмечен чекбокс с архивными записями, выводим заказы без архивных
            } else {
                filters += '&archive=0';
            }

            filters += '&status=' + statusSel;
        } 
    }
    

    //alert(filters);

    //собираем сортировку
    // получим значение атрибута data-sort
    let allTitlesElems = document.getElementById('list-orders').querySelectorAll('.cell-title');

    //переменная для значения ключа (asc или desc), которое активировано нажатим на стрелку вверх или вниз в названии колонки
    let dataSort = '';
    //переменная для ключа, соответствующего названию сортируемого поля в БД
    let key = '';

    //в цикле вынимаем эти два элемента
    for (let i = 0; i < allTitlesElems.length; i++) {
        if (allTitlesElems[i].getAttribute('data-sort')) {
            //вынимаем заданное значение ключа
            dataSort = allTitlesElems[i].getAttribute('data-sort');
            console.log(dataSort);
            //вынимаем ключ
            key = document.getElementById('list-orders').querySelectorAll('.cell-title')[i].getAttribute('data-id');
            console.log(key);
        }
    }
    
    // получим значение атрибута data-page, содержащего номер текущей страницы
    let dataPage = document.getElementById('list-orders').getAttribute('data-page');
    console.log(dataPage);

    let sorting = '';

    //если активировано значение asc
    if (dataSort && dataSort === "asc") {
        sorting += '&orderby=' + key + ':asc';
    //если активировано значение desc
    } else if (dataSort === "desc") {
        sorting += '&orderby=' + key + ':desc';
    }

    //вносим изменение в адресную строку страницы
    history.replaceState(history.length, null, 'admin-orders.php?limit=' + limit.value + filters + sorting);

    document.location.href = mainUrl + '/pages/admin-orders.php?limit=' + limit.value + filters + sorting;

}

//функция при нажатии на кнопку "Применить" для admin-main!!!
function applyInMain() {

    console.log(limit.value);

    //лимит задан всегда, поэтому проверяем наличие поискового запроса
    //получим введенное в поиск значение
    // let searchQuery = document.getElementById('search').value;
    // let dataSearch = searchQuery.trim();

    //и даты "с"
    let from = sortByDateFrom();
    console.log(from);

    //и даты "по"
    let till = sortByDateTill();
    console.log(till);

    //собираем фильтры (дата + поиск)
    let filters = '';

    //если задана дата
    if (from || till) {
        //если  С
        if (from) {
            filters += '&date_from=' + from;
        } 
        if (till) {
            //если ДО
            filters += '&date_till=' + till;
        } 
    }

    //если задан поиск
    // if (dataSearch) {
    //     limit.value = 'all';
    //     filters += '&search=name' + dataSearch;
    // }

    //собираем сортировку
    // получим значение атрибута data-sort
    let allTitlesElems = document.getElementById('list-orders').querySelectorAll('.cell-title');
    console.log(allTitlesElems);

    //переменная для значения ключа (asc или desc), которое активировано нажатим на стрелку вверх или вниз в названии колонки
    let dataSort = '';
    //переменная для ключа, соответствующего названию сортируемого поля в БД
    let key = '';

    //в цикле вынимаем эти два элемента
    for (let i = 0; i < allTitlesElems.length; i++) {
        if (allTitlesElems[i].getAttribute('data-sort')) {
            //вынимаем заданное значение ключа
            dataSort = allTitlesElems[i].getAttribute('data-sort');
            console.log(dataSort);
            //вынимаем ключ
            key = document.getElementById('list-orders').querySelectorAll('.cell-title')[i].getAttribute('data-id');
            console.log(key);
        }
    }
    
    // получим значение атрибута data-page, содержащего номер текущей страницы
    let dataPage = document.getElementById('list-orders').getAttribute('data-page');
    console.log(dataPage);

    let sorting = '';

    //если активировано значение asc
    if (dataSort && dataSort === "asc") {
        sorting += '&orderby=' + key + ':asc';
    //если активировано значение desc
    } else if (dataSort === "desc") {
        sorting += '&orderby=' + key + ':desc';
    }

    //вносим изменение в адресную строку страницы
    history.replaceState(history.length, null, 'admin-main.php?limit=' + limit.value + filters + sorting);

    document.location.href = mainUrl + '/pages/admin-main.php?limit=' + limit.value + filters + sorting;

}

//функция при нажатии на кнопку "Применить" для admin-vendors!!!
function applyInVendors() {

    console.log(limit.value);

    //лимит задан всегда, поэтому проверяем только наличие поискового запроса
    //получим введенное в поиск значение
    let searchQuery = document.getElementById('search').value;
    let dataSearch = searchQuery.trim();

    // получим значение атрибута data-sort
    let allTitlesElems = document.getElementById('list-orders').querySelectorAll('.cell-title');
    console.log(allTitlesElems);

    //переменная для значения ключа (asc или desc), которое активировано нажатим на стрелку вверх или вниз в названии колонки
    let dataSort = '';
    //переменная для ключа, соответствующего названию сортируемого поля в БД
    let key = '';

    //в цикле вынимаем эти два элемента
    for (let i = 0; i < allTitlesElems.length; i++) {
        if (allTitlesElems[i].getAttribute('data-sort')) {
            //вынимаем заданное значение ключа
            dataSort = allTitlesElems[i].getAttribute('data-sort');
            console.log(dataSort);
            //вынимаем ключ
            key = document.getElementById('list-orders').querySelectorAll('.cell-title')[i].getAttribute('data-id');
            console.log(key);
        }
    }

    // получим значение атрибута data-page, содержащего номер текущей страницы
    let dataPage = document.getElementById('list-orders').getAttribute('data-page');
    console.log(dataPage);

    //если активировано значение asc
    if (dataSort && dataSort === "asc") {
        
        //передаем его в адресную строку как гет-параметр
        //вносим изменение в адресную строку страницы
        //но сначала проверяем, какие ДРУГИЕ гет-параметры уже переданы
        //если в гет-параметрах нет ни поиска, ни страницы
        if (!dataSearch && !dataPage) {
            history.replaceState(history.length, null, 'admin-vendors.php?limit=' + limit.value + '&orderby=' + key + ':asc');
            window.location.href = mainUrl + '/pages/admin-vendors.php?limit=' + limit.value + '&orderby=' + key + ':asc';
        //если в гет-параметрах уже есть поиск, но не страница
        } else if (dataSearch && !dataPage) {
            history.replaceState(history.length, null, 'admin-vendors.php?limit=all&search=name:' + dataSearch + '&orderby=' + key + ':asc');
            window.location.href = mainUrl + '/pages/admin-vendors.php?limit=all&search=name:' + dataSearch + '&orderby=' + key + ':asc';
        //если в гет-параметрах уже есть страница, но не поиск
        } else if (!dataSearch && dataPage) {
            history.replaceState(history.length, null, 'admin-vendors.php?limit=' + limit.value + '&page=' + dataPage + '&orderby=' + key + ':asc');
            window.location.href = mainUrl + '/pages/admin-vendors.php?limit=' + limit.value + '&page=' + dataPage + '&orderby=' + key + ':asc';
        //если в гет-параметрах уже есть и страница, и поиск
        } else if (dataSearch && dataPage) {
            history.replaceState(history.length, null, 'admin-vendors.php?limit=all&search=name:' + dataSearch + '&page=' + dataPage + '&orderby=' + key + ':asc');
            window.location.href = mainUrl + '/pages/admin-vendors.php?limit=all&search=name:' + dataSearch + '&page=' + dataPage + '&orderby=' + key + ':asc';
        }
    //если активировано значение desc
    } else if (dataSort === "desc") {

        //передаем его в адресную строку как гет-параметр
        //вносим изменение в адресную строку страницы
        //но сначала проверяем, какие ДРУГИЕ гет-параметры уже переданы
        //если в гет-параметрах нет ни поиска, ни страницы
        if (!dataSearch && !dataPage) {
            history.replaceState(history.length, null, 'admin-vendors.php?limit=' + limit.value + '&orderby=' + key + ':desc');
            window.location.href = mainUrl + '/pages/admin-vendors.php?limit=' + limit.value + '&orderby=' + key + ':desc';
        //если в гет-параметрах уже есть поиск, но не страница
        } else if (dataSearch && !dataPage) {
            history.replaceState(history.length, null, 'admin-vendors.php?limit=all&search=name:' + dataSearch + '&orderby=' + key + ':desc');
            window.location.href = mainUrl + '/pages/admin-vendors.php?limit=all&search=name:' + dataSearch + '&orderby=' + key + ':desc';
        //если в гет-параметрах уже есть страница, но не поиск
        } else if (!dataSearch && dataPage) {
            history.replaceState(history.length, null, 'admin-vendors.php?limit=' + limit.value + '&page=' + dataPage + '&orderby=' + key + ':desc');
            window.location.href = mainUrl + '/pages/admin-vendors.php?limit=' + limit.value + '&page=' + dataPage + '&orderby=' + key + ':desc';
        //если в гет-параметрах уже есть и страница, и поиск
        } else if (dataSearch && dataPage) {
            history.replaceState(history.length, null, 'admin-vendors.php?limit=all&search=name:' + dataSearch + '&page=' + dataPage + '&orderby=' + key + ':desc');
            window.location.href = mainUrl + '/pages/admin-vendors.php?limit=all&search=name:' + dataSearch + '&page=' + dataPage + '&orderby=' + key + ':desc';
        }
    } else if (!dataSort) {
        apply('admin-vendors');
    }

}

//функция по чекбоксам со статусом города
function checkboxChangedCity(id) {

    //выдаем поп-ап с подтверждением действия
    let isChecked = window.confirm('Вы действительно хотите изменить статус города?');

    if(!isChecked) {
        console.log("не менять");
        //чтобы визуально не менялась галочка
        if(event.target.checked) {
            event.target.checked = false;
        } else {
            event.target.checked = true;
        }
        return;
    }

    //если при нажатии чекбокс активировн
    if (event.target.checked) {

        //собираем параметры для передачи в бд
        obj = JSON.stringify({
            'id': id,
            'is_active': 1
        });

    //если при нажатии чекбокс деактивирован
    } else {

        obj = JSON.stringify({
            'id': id,
            'is_active': 0
        });
    }

    console.log(obj);

    let link = mainUrl + '/api/cities.php';

    //передаем на сервер в пост-запросе
    sendRequestPOST(link, obj);

}

//функция по чекбоксам "поставщик подтвердил цены" у админа
function checkboxChangedVendorPrice(id) {

    //выдаем поп-ап с подтверждением действия
    let isChecked = window.confirm('Вы действительно хотите изменить статус?');

    if(!isChecked) {
        console.log("не менять");
        //чтобы визуально не менялась галочка
        if(event.target.checked) {
            event.target.checked = false;
        } else {
            event.target.checked = true;
        }
        return;
    }

    //если при нажатии чекбокс активировн
    if (event.target.checked) {

        //собираем параметры для передачи в бд
        obj = JSON.stringify({
            'id': id,
            'price_confirmed': 1
        });

    //если при нажатии чекбокс деактивирован
    } else {

        obj = JSON.stringify({
            'id': id,
            'price_confirmed': 0
        });
    }

    console.log(obj);

    let link = mainUrl + '/api/vendors.php';

    //передаем на сервер в пост-запросе
    sendRequestPOST(link, obj);

}

/* ---------- ПЕРЕХОД И ПЕРЕДАЧА ПАРАМЕТРОВ ФИЛЬТРАЦИИ НА СТРАНИЦУ редактирования---------- */
function editVendor(id) {

let getParam = window.location.search;

// при переходе на страницу редактирования товара передаём ещё и параметры фильтрации в get
window.location.href = mainUrl + "/pages/admin-edit-vendor.php?id=" + id + "&deleted=0" + getParam.replace('?', '&'); 
    
}

//функция для изменения номера телефона для связи в боте на Главной
function changePhone() {

    //достаем нередактируемую строку с телефоном внутри
    let phoneEl = document.getElementById('phone-number');

    //достаем текущий телефон
    let phone = phoneEl.innerHTML;
    console.log(phone);

     //меняем простую строку на редактируемый инпут
    changeTagName(phoneEl, 'input');

    //вставляем в инпут телефон
    document.getElementById('phone-number').value = phone;
    console.log(document.getElementById('phone-number').value);

    //скрываем карандаш
    document.querySelector('.edit').classList.add('d-none');
    //открываем иконку сохранения
    document.querySelector('.save').classList.remove('d-none');
    //открываем иконку отмены
    document.querySelector('.cancel').classList.remove('d-none');

    //при нажатии на иконку сохранения
    document.querySelector('.save').onclick = function() { changePhoneAndSend(phone) };

    //при нажатии на иконку отмены
    document.querySelector('.cancel').onclick = function() { cancelChangePhone(phone) };
}

//функция изменения и отправки телефона для связи на Главной у админа
function changePhoneAndSend(oldPhone) {

    //достаем измененное значение
    newPhone = document.getElementById('phone-number').value;

    //если новое значение пустое
    if (newPhone.trim() == '') {
        alert('Телефон не может быть пустым');
        return;
    }

    //если новый телефон совпадает со старым (изменения не внесены), делаем то же самое, но без пост-запроса
    if (newPhone === oldPhone) {
        //меняем редактируемый инпут на простую строку
        changeTagName(document.getElementById('phone-number'), 'p');

        //вставляем внутрь прежнее значение
        document.getElementById('phone-number').innerHTML = oldPhone;

        //возвращаем карандаш
        document.querySelector('.edit').classList.remove('d-none');
        //скрываем иконку сохранения
        document.querySelector('.save').classList.add('d-none');
        //скрываем иконку отмены
        document.querySelector('.cancel').classList.add('d-none');
        return;
    }

    //запрос подтверждения
    let yes = window.confirm('Вы действительно хотите изменить телефон?');

    if(!yes) {
        console.log("не изменять");
        //меняем редактируемый инпут на простую строку
        changeTagName(document.getElementById('phone-number'), 'p');

        //вставляем внутрь старое значение
        document.getElementById('phone-number').innerHTML = oldPhone;

        //возвращаем карандаш
        document.querySelector('.edit').classList.remove('d-none');
        //скрываем иконку сохранения
        document.querySelector('.save').classList.add('d-none');
        //скрываем иконку отмены
        document.querySelector('.cancel').classList.add('d-none');

        return;
    }

    //меняем редактируемый инпут на простую строку
    changeTagName(document.getElementById('phone-number'), 'p');

    //вставляем внутрь измененное значение
    document.getElementById('phone-number').innerHTML = newPhone;

    //ссылка
    let link = mainUrl + '/api/settings.php';

    //соберем json для передачи на сервер
    obj = JSON.stringify({
        'name' : 'phone',
        'value' : newPhone
    });
    console.log(obj);

    //отправляем новый телефон на сервер
    sendRequestPOST(link, obj);

    //возвращаем карандаш
    document.querySelector('.edit').classList.remove('d-none');
    //скрываем иконку сохранения
    document.querySelector('.save').classList.add('d-none');
    //скрываем иконку отмены
    document.querySelector('.cancel').classList.add('d-none');

}

//функция отмены изменения телефона для связи на Главной у админа
function cancelChangePhone(oldPhone) {

    //меняем редактируемый инпут на простую строку
    changeTagName(document.getElementById('phone-number'), 'p');

    //вставляем внутрь прежнее значение
    document.getElementById('phone-number').innerHTML = oldPhone;

    //возвращаем карандаш
    document.querySelector('.edit').classList.remove('d-none');
    //скрываем иконку сохранения
    document.querySelector('.save').classList.add('d-none');
    //скрываем иконку отмены
    document.querySelector('.cancel').classList.add('d-none');
}

//функция изменения типа элемента
function changeTagName(el, newTagName) {
    let n = document.createElement(newTagName);
    let attr = el.attributes;
    for (let i = 0, len = attr.length; i < len; ++i) {
      n.setAttribute(attr[i].name, attr[i].value);
    }
    n.innerHTML = el.innerHTML;
    el.parentNode.replaceChild(n, el);
  }

//записываем в куки локальный часовой пояс
let timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
document.cookie = 'time_zone=' + timeZone;
console.log(document.cookie);
