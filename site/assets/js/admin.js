console.log('works');

//функция добавления новой категории
function addNew(section_name) {

    //достаем из инпута введенное название новой категории или бренда
    let name = document.getElementById('add-new').value;
    console.log(name);
    console.log(section_name);

    // собираем ссылку для запроса
    link = 'http://localhost/api/'+ section_name + '.php';

    // соберём json для передачи на сервер
    //для категорий
    if(section_name == 'categories') {
        obj = JSON.stringify({
            'category_name': name
        });
        console.log(obj);
    }
    //для брендов
    if (section_name == 'brands') {
        obj = JSON.stringify({
            'brand_name': name
        });
    }
    
    //передаем на сервер в пост-запросе
    sendRequestPOST(link, obj);

    //очищаем инпут
    document.getElementById('add-new').value = '';
}

//функция удаления категории или бренда
function deleteOne(section_name, id) {

    //отправка запроса на удаление
    sendRequestDELETE('http://localhost/api/'+ section_name +'.php?id=' + id);
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

        let key = document.querySelector('.page-title').getAttribute('data-name');

        //вносим изменение в адресную строку страницы
        history.replaceState(history.length, null, 'admin.php?section=' + section_name + '&limit=' + limit.value + '&search=' + key + ':' + searchQuery.trim());

        document.location.href = 'http://localhost/pages/admin.php?section=' + section_name + '&limit=' + limit.value + '&search=' + key + ':' + searchQuery.trim();
        
    } else {
        // соберём json для передачи на сервер
        // let obj = JSON.stringify({
        //     'limit': limit.value
        // });

        //let link = 'http://localhost/limit.php?limit=';

        //let result = sendRequestGET(link+limit.value);
        //console.log(result);

    history.replaceState(history.length, null, 'admin.php?section=' + section_name + '&limit=' + limit.value);

    document.location.href = 'http://localhost/pages/admin.php?section=' + section_name + '&limit=' + limit.value;

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

