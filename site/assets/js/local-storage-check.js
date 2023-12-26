console.log("подключили ls-check");

// переменные для сохранения позиции скролла, по умолчанию по нулям
let coordinateXInLS = 0;
let coordinateYInLS = 0;

checkPageKeyInLS() 

/* ---------- ПРОВЕРКА НАЛИЧИЯ ГЕТ-ПАРАМЕТРОВ В lOCALSTORAGE ДЛЯ ТЕКУЩЕЙ СТРАНИЦЫ---------- */
// проверка и при необходимостти перезагрузка
function checkPageKeyInLS() {

    // если в ЛС есть элемент с ключом=адрес страницы без гет-параметров (/pages/admin-list-products.php)
    if(localStorage.getItem(window.location.pathname)) {
        // достанем значение-строку по ключу, преобразуя в объект
        let valueAsObject = JSON.parse(localStorage.getItem(window.location.pathname));

        // значение гет-параметров в объекте
        let getParamsInLS = valueAsObject.getParams;

        // значение координат в объекте
        coordinateXInLS = valueAsObject.coordinateX;
        coordinateYInLS = valueAsObject.coordinateY;

        // если текущее значение get-параметра такое же, как и в локасторадж, то ничего не делаем
    
        // если текущее значение гет-параметра не такое, как в локалсторадж, то перезагрузим страницу с параметрами из ЛС
        if(window.location.search !== getParamsInLS) {
            window.location.replace(window.location.origin + window.location.pathname + getParamsInLS)
        }

    } 

}

/* ---------- ИЗМЕНЕНИЕ ПОЗИЦИИ СКРОЛЛА ---------- */
// передвигаем скролл в положение, сохранённое в LS
function changePositionScroll() {
    console.log('window.scroll(' + coordinateXInLS +', ' +coordinateYInLS +')');
    window.scroll(coordinateXInLS, coordinateYInLS);
    console.log('перекрутили на ', coordinateXInLS, coordinateYInLS);
}

/* ---------- СОХРАНЕНИЕ ГЕТ-ПАРАМЕТРОВ И КООРДИНАТ ПРОКРУТКИ В LOCALSTORAGE ---------- */
// сохранить гетпараметры вместе с координатами
function saveGetParamsInLS(getParams) {
    // ключ для элемента локал сторадж
    let keyEl = window.location.pathname;

    // соберём данные для сохранения
    let localStorageObj = {
        "getParams": getParams,
        "coordinateX": window['scrollX'],
        "coordinateY": window['scrollY']
    }

    // значение для элемента локал сторад
    let valueEl = JSON.stringify(localStorageObj);

    // сохраним данные в локал сторадж
    localStorage.setItem(keyEl, valueEl);
}


/* ---------- УДАЛЕНИЕ ЭЛЕМЕНТА С ГЕТ-ПАРАМЕТРАМИ И КООРДИНАТАМИ ПРОКРУТКИ СТРАНИЦЫ В LOCALSTORAGE ---------- */
// Очищаем фильтр
function removeGetParamsInLS(keyEl) {
    // удалим из ЛС элемент с ключом адреса страницы (например, /pages/vendor-list-products.php)
    localStorage.removeItem(keyEl);
    // перезагрузим страницу с пустыми параметрами
    window.location.replace(window.location.origin + window.location.pathname);
}




