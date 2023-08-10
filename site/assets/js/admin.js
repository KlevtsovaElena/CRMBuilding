console.log('works');

//функция добавления
function addNew(section_name) {

    //достаем из инпута введенное название новой категории или бренда
    let name = document.getElementById('add-new').value;
    console.log(name);
    console.log(section_name);

    if(name.trim()) {

        // собираем ссылку для запроса
        let link = 'http://localhost/api/'+ section_name + '.php';

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

function edit(id, section_name) {

    //достаем массив всех элементов, содержащих редактируемое название
    let nameElements = document.querySelectorAll('.list-orders_status');
    //console.log(nameElements);

    //переменная для обновленного значения
    let changedValue = '';

    //переменная для исходного значения
    let editedValue = '';

    //меняем поле с названием на редактируемый инпут
    for (let i = 0; i < nameElements.length; i++) {
        //ищем среди них то, у котого атрибут совпадает с нужным id
        if (nameElements[i].getAttribute('data-id') == id) {

            //копируем из нередактируемого поля текстовое значение, которое будет редактироваться в инпуте
            editedValue = nameElements[i].innerHTML;
            console.log(editedValue);
            //скрываем нередактируемое поле
            nameElements[i].classList.add('d-none');
            //достаем инпут, который надо сделать видимым
            let inputBlock = document.querySelectorAll('.input')[i];
            console.log(inputBlock);
            //открываем инпут
            inputBlock.classList.remove('d-none');
            //кладем внутрь инпута значение, которое будем редактировать
            inputBlock.querySelector('input').value = editedValue;
            input = inputBlock.querySelector('input');

            //скрываем блок с карандашом
            document.getElementsByClassName('edit')[i].classList.add('d-none');
            //открываем блок с отменой
            document.getElementsByClassName('cancel')[i].classList.remove('d-none');

            
            //при хотя бы одном клике по открывшемуся инпуту
            input.onclick = function() {

                //1) вариант, когда только кликнули, но ничего не изменили в инпуте и ушли с него
                //если после этого кликнули по любому другому месту, кроме инпута
                input.onblur = function() {
                    //исключение - если кликнули по крестику отмены редактирования
                    document.onmousedown = function (e) {
                        if (e.target.className === "cancel") {
                            console.log('отловлено нажатие');
                            document.getElementsByClassName('edit')[i].onmousedown = cancel(id, editedValue);
                            return;
                        } 
                    };

                    console.log('onblur срабатывает');
                    //скрываем инпут
                    inputBlock.classList.add('d-none');
                    //возвращаем нередактируемое поле
                    nameElements[i].classList.remove('d-none');
                    //возвращаем блок с карандашом
                    document.getElementsByClassName('edit')[i].classList.remove('d-none');
                    //скрываем блок с отменой
                    document.getElementsByClassName('cancel')[i].classList.add('d-none');

                }

                //2) вариант, когда кликнули и внесли изменения в инпут
                //при изменении в инпуте
                input.onchange = function() {

                    //console.log('onchange срабатывает');

                    //запрос подтверждения
                    let yes = window.confirm('Вы действительно хотите изменить этот элемент?');

                    if(!yes) {
                        console.log("не изменять");
                        return;
                    }

                    //копируем измененное значение
                    changedValue = inputBlock.querySelector('input').value;
                    if (changedValue.trim() === editedValue) {
                        console.log('значение не было обновлено');
                        return;
                    } 
                    if (changedValue.trim() === '') {
                        alert('Поле не может быть пустым');
                        return;
                    }
                    console.log(changedValue);

                    // собираем ссылку для запроса
                    link = 'http://localhost/api/'+ section_name + '.php';

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

                    console.log(obj);

                    //передаем на сервер в пост-запросе
                    sendRequestPOST(link, obj);

                    //убираем инпут
                    inputBlock.classList.add('d-none');
                    //возвращаем нередактируемое поле
                    nameElements[i].classList.remove('d-none');
                    //и возвращаем измененное значение в исходный нередактируемый элемент
                    nameElements[i].innerHTML = changedValue;
                    //возвращаем блок с карандашом
                    document.getElementsByClassName('edit')[i].classList.remove('d-none');
                    //скрываем блок с отменой
                    document.getElementsByClassName('cancel')[i].classList.add('d-none');

                    //если значение поменялось, заносим его в дата-атрибут, чтобы достать в функции cancel
                    if (changedValue.trim() != '' && changedValue.trim() !== editedValue) {
                        //console.log(nameElements[i]);
                        nameElements[i].setAttribute('data-new-name', changedValue);
                        //console.log(nameElements[i].getAttribute('data-new-name'));
                    }

                }
                
            
            }
        }


    }
}

//функция отмены редактирования (по нажатию на крестик)
function cancel(id, name) {
    
    //достаем массив всех элементов, содержащих редактируемое название
    let nameElements = document.querySelectorAll('.list-orders_status');
    console.log(nameElements);

    //меняем редактируемый инпут на поле с названием
    for (let i = 0; i < nameElements.length; i++) {
        if (nameElements[i].getAttribute('data-id') == id) {

            //если назначен дата-атрибут с измененным названием, достаем его
            if(nameElements[i].getAttribute('data-new-name')) {
                name = nameElements[i].getAttribute('data-new-name');
            }

            //достаем инпут, который надо скрыть
            let inputBlock = document.querySelectorAll('.input')[i];
            console.log(inputBlock);
            console.log(nameElements[i]);

            //скрываем инпут и возвращаем нередактируемое поле с названием
            inputBlock.classList.add('d-none');
            nameElements[i].classList.remove('d-none');
            //возвращаем в него изначальное название до редактирования
            nameElements[i].innerHTML = name;
            //скрываем блок с отменой
            document.getElementsByClassName('cancel')[i].classList.add('d-none');
            //открываем блок с карандашом
            document.getElementsByClassName('edit')[i].classList.remove('d-none');
            
        }
    }
}

//функция активации редактируемого инпута для названия
function xxx(id, section_name) {

    //достаем массив всех элементов, содержащих редактируемое название
    let nameElements = document.querySelectorAll('.list-orders_status');
    console.log(nameElements);

    //меняем поле с названием на редактируемый инпут
    for (let i = 0; i < nameElements.length; i++) {
        if (nameElements[i].getAttribute('data-id') == id) {

            //убираем карандаш у нужной строки
            document.getElementsByClassName('edit')[i].innerHTML = '';
            //убираем онклик с того места, где был карандаш
            //document.getElementsByClassName('edit')[i].onclick = null;
            //делаем обычный курсор в том месте, где был карандаш
            document.getElementsByClassName('edit')[i].style.cursor = 'default';
            //вынимаем текстовое значение, которое будет редактироваться
            let editedValue = nameElements[i].innerHTML;
            //очищаем поле, откуда взяли название
            nameElements[i].innerText = '';
            //создаем инпут
            let input = document.createElement('input');
            input.type = 'text';
            //вешаем его внутрь обычной нередактируемой строки
            nameElements[i].appendChild(input);
            //переносим имеющееся  название внутрь инпута для редактирования
            input.value = editedValue;
            console.log(editedValue);
            

            nameElements[i].childNodes[0].setAttribute('created', 1);
            console.log(nameElements[i].childNodes[0].getAttribute('created'));
            //если совершено скликивание даже без клика по инпуту

            // if (nameElements[i].childNodes[0].getAttribute('created')) {
            //     document.addEventListener( 'click', (e) => {

            //         if(e.target !== nameElements[i].childNodes[0]) {
            //             console.log(nameElements[i].childNodes[0].getAttribute('created'));
            //             console.log('клик мимо инпута');
            //         }
            //         const withinBoundaries = e.composedPath().includes(input);
                 
                        // console.log(nameElements[i].childNodes[0].getAttribute('created'));
                        // console.log('клик мимо инпута');
                        // //возвращаем обратно нередактируемую строку
                        // //убираем добавленный инпут
                        // nameElements[i].removeChild(input);
                        // //и возвращаем измененное значение в исходный нередактируемый элемент
                        // nameElements[i].innerText = editedValue;
                        // //возвращаем карандаш у нужной строки
                        // document.getElementsByClassName('edit')[i].innerHTML = '&#9998;';
                        // //возвращаем указующий курсор в том месте, где был карандаш
                        // document.getElementsByClassName('edit')[i].style.cursor = 'pointer';

            //     })
            // }

            //document.addEventListener( 'click', (e) => {
                //const withinBoundaries = e.composedPath().includes(input);
             
                // if (nameElements[i].childNodes[0].getAttribute('created') && event.target !== input) {
                //     console.log(nameElements[i].childNodes[0].getAttribute('created'));
                //     console.log('клик мимо инпута');
                //     //возвращаем обратно нередактируемую строку
                //     //убираем добавленный инпут
                //     nameElements[i].removeChild(input);
                //     //и возвращаем измененное значение в исходный нередактируемый элемент
                //     nameElements[i].innerText = editedValue;
                //     //возвращаем карандаш у нужной строки
                //     document.getElementsByClassName('edit')[i].innerHTML = '&#9998;';
                //     //возвращаем указующий курсор в том месте, где был карандаш
                //     document.getElementsByClassName('edit')[i].style.cursor = 'pointer';
                // }
            //})

            input.onclick = function() {

                input.onblur = function() {
                    //возвращаем обратно нередактируемую строку
                    //убираем добавленный инпут
                    nameElements[i].removeChild(input);
                    //и возвращаем измененное значение в исходный нередактируемый элемент
                    nameElements[i].innerText = editedValue;
                    //возвращаем карандаш у нужной строки
                    document.getElementsByClassName('edit')[i].innerHTML = '&#9998;';
                    //возвращаем указующий курсор в том месте, где был карандаш
                    document.getElementsByClassName('edit')[i].style.cursor = 'pointer';
                    //возвращаем онклик
                    //document.getElementsByClassName('edit')[i].onclick = edit(id, section_name);
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
                    //возвращаем карандаш у нужной строки
                    document.getElementsByClassName('edit')[i].innerHTML = '&#9998;';
                    //возвращаем указующий курсор в том месте, где был карандаш
                    document.getElementsByClassName('edit')[i].style.cursor = 'pointer';
                };
            }

            
           
        }
    }

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
        link = 'http://localhost/api/vendors/delete-vendor-with-products.php';
    } else {
        link = 'http://localhost/api/'+ section_name + '.php';
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
            history.replaceState(history.length, null, 'admin-vendors.php?limit=all&search=name:' + searchQuery.trim());

            document.location.href = 'http://localhost/pages/admin-vendors.php?limit=all&search=name:' +  searchQuery.trim();

        } else {

            let key = document.querySelector('.page-title').getAttribute('data-name');

            //вносим изменение в адресную строку страницы
            history.replaceState(history.length, null, 'admin.php?section=all&limit=' + limit.value + '&search=' + key + ':' + searchQuery.trim());

            document.location.href = 'http://localhost/pages/admin.php?section=all&limit=' + limit.value + '&search=' + key + ':' + searchQuery.trim();
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

}

//функция при нажатии на кнопку "Применить" для admin-orders!!!
function applyInOrders() {

    console.log(limit.value);

    //лимит задан всегда, поэтому проверяем наличие поискового запроса
    //получим введенное в поиск значение
    let searchQuery = document.getElementById('search').value;
    let dataSearch = searchQuery.trim();

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
            filters += '&order_date_from=' + from;
        } 
        if (till) {
            //если ДО
            filters += '&order_date_till=' + till;
        } 
    }

    //если задан поиск
    if (dataSearch) {
        limit.value = 'all';
        filters += '&search=order_id:' + dataSearch;
    } 

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
    history.replaceState(history.length, null, 'admin-orders.php?limit=' + limit.value + filters + sorting);

    document.location.href = 'http://localhost/pages/admin-orders.php?limit=' + limit.value + filters + sorting;

    // //если активировано значение asc
    // if (dataSort && dataSort === "asc") {
    //     //передаем его в адресную строку как гет-параметр
    //     //вносим изменение в адресную строку страницы
    //     //но сначала проверяем, какие ДРУГИЕ гет-параметры уже переданы
    //     //если в гет-параметрах нет ни поиска, ни страницы
    //     if (!dataSearch && !dataPage) {
    //         history.replaceState(history.length, null, 'admin-orders.php?limit=' + limit.value + '&orderby=' + key + ':asc');
    //         window.location.href = 'http://localhost/pages/admin-orders.php?limit=' + limit.value + '&orderby=' + key + ':asc';
    //     //если в гет-параметрах уже есть поиск, но не страница
    //     } else if (dataSearch && !dataPage) {
    //         history.replaceState(history.length, null, 'admin-orders.php?limit=all&search=order_id:' + dataSearch + '&orderby=' + key + ':asc');
    //         window.location.href = 'http://localhost/pages/admin-orders.php?limit=all&search=order_id:' + dataSearch + '&orderby=' + key + ':asc';
    //     //если в гет-параметрах уже есть страница, но не поиск
    //     } else if (!dataSearch && dataPage) {
    //         history.replaceState(history.length, null, 'admin-orders.php?limit=' + limit.value + '&page=' + dataPage + '&orderby=' + key + ':asc');
    //         window.location.href = 'http://localhost/pages/admin-orders.php?limit=' + limit.value + '&page=' + dataPage + '&orderby=' + key + ':asc';
    //     //если в гет-параметрах уже есть и страница, и поиск
    //     } else if (dataSearch && dataPage) {
    //         history.replaceState(history.length, null, 'admin-orders.php?limit=all&search=order_id:' + dataSearch + '&page=' + dataPage + '&orderby=' + key + ':asc');
    //         window.location.href = 'http://localhost/pages/admin-orders.php?limit=all&search=order_id:' + dataSearch + '&page=' + dataPage + '&orderby=' + key + ':asc';
    //     }
    // //если активировано значение desc
    // } else if (dataSort === "desc") {

    //     //передаем его в адресную строку как гет-параметр
    //     //вносим изменение в адресную строку страницы
    //     //но сначала проверяем, какие ДРУГИЕ гет-параметры уже переданы
    //     //если в гет-параметрах нет ни поиска, ни страницы
    //     if (!dataSearch && !dataPage) {
    //         history.replaceState(history.length, null, 'admin-orders.php?limit=' + limit.value + '&orderby=' + key + ':desc');
    //         window.location.href = 'http://localhost/pages/admin-orders.php?limit=' + limit.value + '&orderby=' + key + ':desc';
    //     //если в гет-параметрах уже есть поиск, но не страница
    //     } else if (dataSearch && !dataPage) {
    //         history.replaceState(history.length, null, 'admin-orders.php?limit=all&search=order_id:' + dataSearch + '&orderby=' + key + ':desc');
    //         window.location.href = 'http://localhost/pages/admin-orders.php?limit=all&search=order_id:' + dataSearch + '&orderby=' + key + ':desc';
    //     //если в гет-параметрах уже есть страница, но не поиск
    //     } else if (!dataSearch && dataPage) {
    //         history.replaceState(history.length, null, 'admin-orders.php?limit=' + limit.value + '&page=' + dataPage + '&orderby=' + key + ':desc');
    //         window.location.href = 'http://localhost/pages/admin-orders.php?limit=' + limit.value + '&page=' + dataPage + '&orderby=' + key + ':desc';
    //     //если в гет-параметрах уже есть и страница, и поиск
    //     } else if (dataSearch && dataPage) {
    //         history.replaceState(history.length, null, 'admin-orders.php?limit=all&search=order_id:' + dataSearch + '&page=' + dataPage + '&orderby=' + key + ':desc');
    //         window.location.href = 'http://localhost/pages/admin-orders.php?limit=all&search=order_id:' + dataSearch + '&page=' + dataPage + '&orderby=' + key + ':desc';
    //     }
    // } else if (!dataSort) {
    //     //проверим значение дат и поиска
    //     if((from || till) && searchQuery.trim()) {
    //         //если есть только С и поиск
    //         if(from && searchQuery.trim()) {
    //             //вносим изменение в адресную строку страницы
    //             history.replaceState(history.length, null, 'admin-orders.php?limit=all&order_date_from=' + from + '&search=order_id:' + searchQuery.trim());

    //             document.location.href = 'http://localhost/pages/admin-orders.php?limit=all&order_date_from=' + from + 'search=order_id:' + searchQuery.trim();
    //         //если есть только ПО и поиск
    //         } else if(till && searchQuery.trim()) {
    //             //вносим изменение в адресную строку страницы
    //             history.replaceState(history.length, null, 'admin-orders.php?limit=all&order_date_till=' + till + '&search=order_id:' + searchQuery.trim());

    //             document.location.href = 'http://localhost/pages/admin-orders.php?limit=all&order_date_till=' + till + 'search=order_id:' + searchQuery.trim();
    //         //если есть С, ПО и поиск
    //         } else if((from && till) && searchQuery.trim()) {
    //             //вносим изменение в адресную строку страницы
    //             history.replaceState(history.length, null, 'admin-orders.php?limit=all&order_date_from=' + from + '&order_date_till=' + till + '&search=order_id:' + searchQuery.trim());

    //             document.location.href = 'http://localhost/pages/admin-orders.php?limit=all&order_date_from=' + from + '&order_date_till=' + till + 'search=order_id:' + searchQuery.trim();

    //         }
    //     }
    //     //если есть только одна или обе даты без поиска
    //     else if((from || till) && !searchQuery.trim()) {
    //         //если есть только С
    //         if(from && !till) {
    //             //вносим изменение в адресную строку страницы
    //             history.replaceState(history.length, null, 'admin-orders.php?limit=all&order_date_from=' + from);

    //             document.location.href = 'http://localhost/pages/admin-orders.php?limit=all&order_date_from=' + from;
    //         //если есть только ПО
    //         } else if(till && !from) {
    //             //вносим изменение в адресную строку страницы
    //             history.replaceState(history.length, null, 'admin-orders.php?limit=all&order_date_till=' + till);

    //             document.location.href = 'http://localhost/pages/admin-orders.php?limit=all&order_date_till=' + till;
    //         //если есть С и ПО
    //         } else if(from && till) {
    //             //вносим изменение в адресную строку страницы
    //             history.replaceState(history.length, null, 'admin-orders.php?limit=all&order_date_from=' + from + '&order_date_till=' + till);

    //             document.location.href = 'http://localhost/pages/admin-orders.php?limit=all&order_date_from=' + from + '&order_date_till=' + till;

    //         }
    //     }
    //     //есть только поиск без дат
    //     else if (searchQuery.trim() && !(from || till)) {
    //         console.log(searchQuery);

    //         //вносим изменение в адресную строку страницы
    //         history.replaceState(history.length, null, 'admin-orders.php?limit=all&search=order_id:' + searchQuery.trim());

    //         document.location.href = 'http://localhost/pages/admin-orders.php?limit=all&search=order_id:' + searchQuery.trim();
    //     //если нет ни поиска, ни дат  
    //     } else {

    //     //вносим изменение в адресную строку страницы
    //     history.replaceState(history.length, null, 'admin-orders.php?limit=' + limit.value);

    //     document.location.href = 'http://localhost/pages/admin-orders.php?limit=' + limit.value;

    //     }

    // }
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
            window.location.href = 'http://localhost/pages/admin-vendors.php?limit=' + limit.value + '&orderby=' + key + ':asc';
        //если в гет-параметрах уже есть поиск, но не страница
        } else if (dataSearch && !dataPage) {
            history.replaceState(history.length, null, 'admin-vendors.php?limit=all&search=name:' + dataSearch + '&orderby=' + key + ':asc');
            window.location.href = 'http://localhost/pages/admin-vendors.php?limit=all&search=name:' + dataSearch + '&orderby=' + key + ':asc';
        //если в гет-параметрах уже есть страница, но не поиск
        } else if (!dataSearch && dataPage) {
            history.replaceState(history.length, null, 'admin-vendors.php?limit=' + limit.value + '&page=' + dataPage + '&orderby=' + key + ':asc');
            window.location.href = 'http://localhost/pages/admin-vendors.php?limit=' + limit.value + '&page=' + dataPage + '&orderby=' + key + ':asc';
        //если в гет-параметрах уже есть и страница, и поиск
        } else if (dataSearch && dataPage) {
            history.replaceState(history.length, null, 'admin-vendors.php?limit=all&search=name:' + dataSearch + '&page=' + dataPage + '&orderby=' + key + ':asc');
            window.location.href = 'http://localhost/pages/admin-vendors.php?limit=all&search=name:' + dataSearch + '&page=' + dataPage + '&orderby=' + key + ':asc';
        }
    //если активировано значение desc
    } else if (dataSort === "desc") {

        //передаем его в адресную строку как гет-параметр
        //вносим изменение в адресную строку страницы
        //но сначала проверяем, какие ДРУГИЕ гет-параметры уже переданы
        //если в гет-параметрах нет ни поиска, ни страницы
        if (!dataSearch && !dataPage) {
            history.replaceState(history.length, null, 'admin-vendors.php?limit=' + limit.value + '&orderby=' + key + ':desc');
            window.location.href = 'http://localhost/pages/admin-vendors.php?limit=' + limit.value + '&orderby=' + key + ':desc';
        //если в гет-параметрах уже есть поиск, но не страница
        } else if (dataSearch && !dataPage) {
            history.replaceState(history.length, null, 'admin-vendors.php?limit=all&search=name:' + dataSearch + '&orderby=' + key + ':desc');
            window.location.href = 'http://localhost/pages/admin-vendors.php?limit=all&search=name:' + dataSearch + '&orderby=' + key + ':desc';
        //если в гет-параметрах уже есть страница, но не поиск
        } else if (!dataSearch && dataPage) {
            history.replaceState(history.length, null, 'admin-vendors.php?limit=' + limit.value + '&page=' + dataPage + '&orderby=' + key + ':desc');
            window.location.href = 'http://localhost/pages/admin-vendors.php?limit=' + limit.value + '&page=' + dataPage + '&orderby=' + key + ':desc';
        //если в гет-параметрах уже есть и страница, и поиск
        } else if (dataSearch && dataPage) {
            history.replaceState(history.length, null, 'admin-vendors.php?limit=all&search=name:' + dataSearch + '&page=' + dataPage + '&orderby=' + key + ':desc');
            window.location.href = 'http://localhost/pages/admin-vendors.php?limit=all&search=name:' + dataSearch + '&page=' + dataPage + '&orderby=' + key + ':desc';
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

    let link = 'http://localhost/api/cities.php';

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

    let link = 'http://localhost/api/vendors.php';

    //передаем на сервер в пост-запросе
    sendRequestPOST(link, obj);

}

/* ---------- ПЕРЕХОД И ПЕРЕДАЧА ПАРАМЕТРОВ ФИЛЬТРАЦИИ НА СТРАНИЦУ редактирования---------- */
function editVendor(id) {

let getParam = window.location.search;

// при переходе на страницу редактирования товара передаём ещё и параметры фильтрации в get
window.location.href = "http://localhost/pages/admin-edit-vendor.php?id=" + id + "&deleted=0" + getParam.replace('?', '&'); 
    
}

//функция сбора параметра сортировки по дате ДО
function sortByDateFrom() {

    //достаем выбранную дату из календаря
    let fromString = document.getElementById('from').value;
    console.log(fromString);

    //добавляем к ней время начала суток
    let dateString = fromString + " 00:00:00";
    //конвертируем в юникс формат без миллисекунд
    let unixTime = Date.parse(dateString) / 1000;

    console.log(unixTime);
    return unixTime;
}

//функция сбора параметра сортировки по дате ПОСЛЕ
function sortByDateTill() {
    //достаем выбранную дату из календаря
    let tillString = document.getElementById('till').value;

    console.log(tillString);
    //console.log(till.value);

    //добавляем к ней время начала суток
    let dateString = tillString + " 23:59:59";
    //конвертируем в юникс формат без миллисекунд
    let unixTime = Date.parse(dateString) / 1000;

    console.log(unixTime);
    return unixTime;
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

    //let newPhone;

    //по нажатию на энтер
    // document.addEventListener('keyup', event => {
    //     if( event.code === 'Enter' ) {

    //         //меняем телефон и отправляем изменения
    //         changePhoneAndSend(phone);
    //     }
    // });
      

      //меняем кнопку "Изменить на "Сохранить"
      let btn = document.getElementById('btn-phone');
      btn.innerHTML = 'Сохранить';
      btn.onclick = function() {
          changePhoneAndSend(phone);
      }

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

        //меняем кнопку обратно на "Изменить"
        let btn = document.getElementById('btn-phone');
        btn.innerHTML = 'Изменить';
        btn.onclick = function() {
            changePhone();
        }
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

        //меняем кнопку обратно на "Изменить"
        let btn = document.getElementById('btn-phone');
        btn.innerHTML = 'Изменить';
        btn.onclick = function() {
            changePhone();
        }

        return;
    }

    //меняем редактируемый инпут на простую строку
    changeTagName(document.getElementById('phone-number'), 'p');

    //вставляем внутрь измененное значение
    document.getElementById('phone-number').innerHTML = newPhone;

    //ссылка
    let link = 'http://localhost/api/settings.php';

    //соберем json для передачи на сервер
    obj = JSON.stringify({
        'name' : 'phone',
        'value' : newPhone
    });
    console.log(obj);

    //отправляем новый телефон на сервер
    sendRequestPOST(link, obj);

    //меняем кнопку обратно на "Изменить"
    let btn = document.getElementById('btn-phone');
    btn.innerHTML = 'Изменить';
    btn.onclick = function() {
        changePhone();
    }

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
