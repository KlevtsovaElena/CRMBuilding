//получаем все элементы заголовка для отслеживания сортировки
const headTableOrders = document.getElementById('list-orders').querySelectorAll('[data-sort]');

/* ---------- НАЖАТИЕ НА ИМЯ ЗАГОЛОВКА ТАБЛИЦЫ (СОРТИРОВКА по одному ключу) ---------- */
function sortChange() {

    //получим название раздела (как в адресной строке) из дата-section 
    let section_name = document.getElementById('list-orders').getAttribute('data-section');
    console.log(section_name);
    //let keySearch;

    // if (section_name === 'admin-orders') {
    //     keySearch = 'order_id:';
    // } else if (section_name === 'admin-vendors') {
    //     keySearch = 'name:';
    // }

    // получим значение атрибута data-sort
    let dataSort = event.target.getAttribute('data-sort');

    // получим значение атрибута data-limit, содержащего уже заданный лимит кол-ва отображаемых на стр. записей
    let dataLimit = document.getElementById('list-orders').getAttribute('data-limit');
    console.log(dataLimit);

    // получим значение атрибута data-search, содержащего уже введенный поисковый запрос
    let dataSearch = document.getElementById('list-orders').getAttribute('data-search');

    // получим значение атрибута data-page, содержащего номер текущей страницы
    let dataPage = document.getElementById('list-orders').getAttribute('data-page');

    // получим значение атрибута data-vendor-select, содержащего название выбранного поставщика
    let dataVendor = document.getElementById('list-orders').getAttribute('data-vendor-select');

    //получим селект "город"
    let citySel = document.getElementById('city').querySelectorAll('option:checked')[0].value;
    console.log(citySel);

    // получим значение атрибута data-status-select, содержащего выбранный статус
    let dataStatus = document.getElementById('list-orders').getAttribute('data-status-select');

    // получим значение атрибута data-archive, содержащего статус архива
    let dataArchive = document.getElementById('list-orders').getAttribute('data-archive-select');

    // получим значение чекбокса по архивных заказам
    //let dataArchiveCheck = document.getElementById('archive').value;

    //проверка, есть ли на странице сортировка по дате
    let from; let till;
    if (document.getElementById('from') || document.getElementById('till')) {
        // получим значение даты С
        from = sortByDateFrom();   
        
        // получим значение даты ПО
        till = sortByDateTill();  
    } 

    //собираем фильтры (дата + поиск)
    let filters = '';

    //если задан поставщик
    if (dataVendor) {
        filters += '&vendor_name=' + dataVendor;
    }

    //если задан город
    if (citySel) {
        if (section_name == 'admin-orders') {
            filters += '&vendor_city=' + citySel;
        }
        if (section_name == 'admin-vendors') {
            filters += '&city_name=' + citySel;
        }
    }

    //если задан статус
    if (dataStatus) {
        if (dataStatus == 5) {
            filters += '&archive=1';
        } else {
            filters += '&status=' + dataStatus;
        } 
    }

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
        dataLimit = 'all';
        filters += '&search=' + dataSearch;
    } 

    //если задан архив
    if (dataArchive) {
        filters += '&archive=' + dataArchive;
    }

    //если отмечен чекбокс с архивными записями, выводим заказы с архивными (для страницы заказов)
    if (archiveCheck) {
        if (archiveChecked()) {
            filters.replace('&archive=.', '');
            filters += '&archived';
        //если НЕ отмечен чекбокс с архивными записями, выводим заказы без архивных
        } else {
            filters += '&archive=0';
        }
    }

    //если задана страница
    if (dataPage) {
        filters += '&page=' + dataPage;
    } 

    let key;

    if (!dataSort) {

        // если атрибут пуст,
        // то всем заголовкам устанавливаем пустое значение этого атрибута
        headTableOrders.forEach(item => {
            item.setAttribute('data-sort', '');
        })

        // а заголовку, по которому кликнули, устанавливаем asc
        event.target.setAttribute('data-sort', 'asc');

        //вынимаем ключ
        key = event.target.getAttribute('data-id');

        filters += '&orderby=' + key + ':asc';


    } else if (dataSort === "asc") {
        // если значение атрибута asc, то меняем его на desc
        event.target.setAttribute('data-sort', 'desc');

        //вынимаем ключ
        key = event.target.getAttribute('data-id');

        filters += '&orderby=' + key + ':desc';

    } else if (dataSort === "desc") {
        // если значение атрибута desc, то меняем его на asc
        event.target.setAttribute('data-sort', 'asc');

        //вынимаем ключ
        key = event.target.getAttribute('data-id');

        filters += '&orderby=' + key + ':asc';
    }

    console.log(filters);

        //и передаем в адресную строку гет-параметр
        //вносим изменение в адресную строку страницы

        history.replaceState(history.length, null, section_name + '.php?limit=' + dataLimit + filters);
        window.location.href = mainUrl + '/pages/' + section_name + '.php?limit=' + dataLimit + filters;

}

//функция сбора параметра сортировки по дате ДО
function sortByDateFrom() {

    //достаем выбранную дату из календаря
    let fromString = document.getElementById('from').value;
    console.log(fromString);

    if(!fromString) {
        return;
    }

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

    if(!tillString) {
        return;
    }

    //добавляем к ней время конца суток
    let dateString = tillString + " 23:59:59";
    //конвертируем в юникс формат без миллисекунд
    let unixTime = Date.parse(dateString) / 1000;

    console.log(unixTime);
    return unixTime;
}

let archiveCheck = document.getElementById('archive');

//функция чекбокса архивных записей (для страницы заказов)
function archiveChecked() {
    //если не отмечен чекбокс с архивными записями, выводим заказы БЕЗ архивных
    if (archiveCheck.checked) {
        archiveCheck.value = '';
        return 1;
    //если не отмечен чекбокс с архивными записями, выводим заказы БЕЗ архивных
    } else {
        archiveCheck.value = 'archive=0';
        return 0;
    }
}

// отслеживаем клик по заголовку
headTableOrders.forEach(item => {
    item.addEventListener("click", sortChange);
})