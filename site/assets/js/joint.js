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

    // получим значение даты С
    let from = sortByDateFrom();   
    
    // получим значение даты ПО
    let till = sortByDateTill();   

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
    if (dataSearch) {
        dataLimit = 'all';
        filters += '&search=' + dataSearch;
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

// отслеживаем клик по заголовку
headTableOrders.forEach(item => {
    item.addEventListener("click", sortChange);
})