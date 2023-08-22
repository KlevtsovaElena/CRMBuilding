console.log("подключили vendor-main.js", mainUrl)

// инпут с курсом доллара
let rateEl = document.getElementById('rate');

// изменение курса доллара
if(rateEl) {
    rateEl.addEventListener('change', () => {

        // валидация поля
        let rateValue = rateEl.value;
        if (!(rateValue && rateValue>0)) {
           alert('Значение должно быть больше 0');
           window.location.href = window.location.href;
           return;
        } 

        let rateChange = window.confirm('Вы действительно хотите изменить курс доллара?');
    
        // если отмена, то перезагружаем страницу
        if (!rateChange) {
            window.location.href = window.location.href;
            return;
        }
    
        // иначе
        // запрос на перезапись курса доллара
        let vendorId = document.querySelector('.menu-top__profile').getAttribute('vendor-id');
        
        let obj  = JSON.stringify({
            'id': vendorId,
            'rate': rateValue,
            'price_confirmed': 0
        })
    
        // передаём данные на сервер
        sendRequestPOST(mainUrl + '/api/price/change-price-rate.php', obj);
        
        window.location.href = window.location.href;
    })
}
