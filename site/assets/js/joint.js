//получаем все элементы заголовка для отслеживания сортировки
const headTableOrders = document.getElementById('list-orders').querySelectorAll('[data-sort]');

/* ---------- НАЖАТИЕ НА ИМЯ ЗАГОЛОВКА ТАБЛИЦЫ (СОРТИРОВКА по одному ключу) ---------- */
function sortChange() {

    // получим значение атрибута data-sort
    let dataSort = event.target.getAttribute('data-sort');

    // получим значение атрибута data-limit, содержащего уже заданный лимит кол-ва отображаемых на стр. записей
    let dataLimit = document.getElementById('list-orders').getAttribute('data-limit');

    // получим значение атрибута data-search, содержащего уже введенный поисковый запрос
    let dataSearch = document.getElementById('list-orders').getAttribute('data-search');

    // получим значение атрибута data-page, содержащего номер текущей страницы
    let dataPage = document.getElementById('list-orders').getAttribute('data-page');

    if (!dataSort) {

        // если атрибут пуст,
        // то всем заголовкам устанавливаем пустое значение этого атрибута
        headTableOrders.forEach(item => {
            item.setAttribute('data-sort', '');
        })

        // а заголовку, по которому кликнули, устанавливаем asc
        event.target.setAttribute('data-sort', 'asc');

        //вынимаем ключ
        let key = event.target.getAttribute('data-id');

        //и передаем в адресную строку гет-параметр
        //вносим изменение в адресную строку страницы
        //если в гет-параметрах нет ни поиска, ни страницы
        if (!dataSearch && !dataPage) {
            history.replaceState(history.length, null, 'admin-orders.php?limit=' + dataLimit + '&orderby=' + key + ':asc');
            window.location.href = 'http://localhost/pages/admin-orders.php?limit=' + dataLimit + '&orderby=' + key + ':asc';
        //если в гет-параметрах уже есть поиск, но не страница
        } else if (dataSearch && !dataPage) {
            history.replaceState(history.length, null, 'admin-orders.php?limit=all&search=' + dataSearch + '&orderby=' + key + ':asc');
            window.location.href = 'http://localhost/pages/admin-orders.php?limit=all&search=' + dataSearch + '&orderby=' + key + ':asc';
        //если в гет-параметрах уже есть страница, но не поиск
        } else if (!dataSearch && dataPage) {
            history.replaceState(history.length, null, 'admin-orders.php?limit=' + dataLimit + '&page=' + dataPage + '&orderby=' + key + ':asc');
            window.location.href = 'http://localhost/pages/admin-orders.php?limit=' + dataLimit + '&page=' + dataPage + '&orderby=' + key + ':asc';
        //если в гет-параметрах уже есть и страница, и поиск
        } else if (dataSearch && dataPage) {
            history.replaceState(history.length, null, 'admin-orders.php?limit=all&search=' + dataSearch + '&page=' + dataPage + '&orderby=' + key + ':asc');
            window.location.href = 'http://localhost/pages/admin-orders.php?limit=all&search=' + dataSearch + '&page=' + dataPage + '&orderby=' + key + ':asc';
        }

    } else if (dataSort === "asc") {
        // если значение атрибута asc, то меняем его на desc
        event.target.setAttribute('data-sort', 'desc');

        //вынимаем ключ
        let key = event.target.getAttribute('data-id');

        //и передаем в адресную строку гет-параметр
        //вносим изменение в адресную строку страницы
        //если в гет-параметрах нет ни поиска, ни страницы
        if (!dataSearch && !dataPage) {
            history.replaceState(history.length, null, 'admin-orders.php?limit=' + dataLimit + '&orderby=' + key + ':desc');
            window.location.href = 'http://localhost/pages/admin-orders.php?limit=' + dataLimit + '&orderby=' + key + ':desc';
        //если в гет-параметрах уже есть поиск, но не страница
        } else if (dataSearch && !dataPage) {
            history.replaceState(history.length, null, 'admin-orders.php?limit=all&search=' + dataSearch + '&orderby=' + key + ':desc');
            window.location.href = 'http://localhost/pages/admin-orders.php?limit=all&search=' + dataSearch + '&orderby=' + key + ':desc';
        //если в гет-параметрах уже есть страница, но не поиск
        } else if (!dataSearch && dataPage) {
            history.replaceState(history.length, null, 'admin-orders.php?limit=' + dataLimit + '&page=' + dataPage + '&orderby=' + key + ':desc');
            window.location.href = 'http://localhost/pages/admin-orders.php?limit=' + dataLimit + '&page=' + dataPage + '&orderby=' + key + ':desc';
        //если в гет-параметрах уже есть и страница, и поиск
        } else if (dataSearch && dataPage) {
            history.replaceState(history.length, null, 'admin-orders.php?limit=all&search=' + dataSearch + '&page=' + dataPage + '&orderby=' + key + ':desc');
            window.location.href = 'http://localhost/pages/admin-orders.php?limit=all&search=' + dataSearch + '&page=' + dataPage + '&orderby=' + key + ':desc';
        }

    } else if (dataSort === "desc") {
        // если значение атрибута desc, то меняем его на asc
        event.target.setAttribute('data-sort', 'asc');

        //вынимаем ключ
        let key = event.target.getAttribute('data-id');

        //и передаем в адресную строку гет-параметр
        //вносим изменение в адресную строку страницы
        //если в гет-параметрах нет ни поиска, ни страницы
        if (!dataSearch && !dataPage) {
            history.replaceState(history.length, null, 'admin-orders.php?limit=' + dataLimit + '&orderby=' + key + ':asc');
            window.location.href = 'http://localhost/pages/admin-orders.php?limit=' + dataLimit + '&orderby=' + key + ':asc';
        //если в гет-параметрах уже есть поиск, но не страница
        } else if (dataSearch && !dataPage) {
            history.replaceState(history.length, null, 'admin-orders.php?limit=all&search=' + dataSearch + '&orderby=' + key + ':asc');
            window.location.href = 'http://localhost/pages/admin-orders.php?limit=all&search=' + dataSearch + '&orderby=' + key + ':asc';
        //если в гет-параметрах уже есть страница, но не поиск
        } else if (!dataSearch && dataPage) {
            history.replaceState(history.length, null, 'admin-orders.php?limit=' + dataLimit + '&page=' + dataPage + '&orderby=' + key + ':asc');
            window.location.href = 'http://localhost/pages/admin-orders.php?limit=' + dataLimit + '&page=' + dataPage + '&orderby=' + key + ':asc';
        //если в гет-параметрах уже есть и страница, и поиск
        } else if (dataSearch && dataPage) {
            history.replaceState(history.length, null, 'admin-orders.php?limit=all&search=' + dataSearch + '&page=' + dataPage + '&orderby=' + key + ':asc');
            window.location.href = 'http://localhost/pages/admin-orders.php?limit=all&search=' + dataSearch + '&page=' + dataPage + '&orderby=' + key + ':asc';
        }
    }

    
}

// отслеживаем клик по заголовку
headTableOrders.forEach(item => {
    item.addEventListener("click", sortChange);
})