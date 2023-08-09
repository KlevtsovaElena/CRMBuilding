console.log("подключили vendor-main.js")

// инпут с курсом доллара
let rateEl = document.getElementById('rate');

// изменение курса доллара
rateEl.addEventListener('change', () => {

    let rateChange = window.confirm('Вы действительно хотите изменить курс доллара?');

    // если отмена, то перезагружаем страницу
    if (!rateChange) {
        window.location.href = window.location.href;
    }

    // иначе
    // 1. запрос на перезапись курса доллара
    let vendorId = document.querySelector('.menu-top__profile').getAttribute('vendor-id');
    console.log(vendorId);
    let obj  = JSON.stringify({
        'id': vendorId,
        'rate': rateEl.value,
        'price_confirmed': 0
    })

    // передаём данные на сервер
    sendRequestPOST('http://localhost/api/price/change-price-rate.php', obj);
    
    window.location.href = window.location.href;
})