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
        let isSuccessJson = sendRequestPOST(mainUrl + '/api/price/change-price-rate.php', obj);
        let isSuccess;
        // провeрим, что вернулось с сервера success:true || success:false
        try {
            // попробуем распарсить json, если там какой-то текст=ошибка, то распарсить не получится
            isSuccess  = JSON.parse(isSuccessJson);
        } catch(e) {
            alert ('Ошибка! Попробуйте позже!');
            return;
        }
        // если запрос не выполнен , то показываем alert с ошибкой и не перезагружаем страницу
        // иначе - Товар изменён, и если изменял не админ, и менялась цена, то оповещаем админа в телеграмм
        if (!isSuccess.success) {
            // если распарсили и получили success : false, то Ошибка
            alert('Ошибка!');
            return;
        } else {
            // если распарсили и получили success : true, то всё записалось в базу
            // оповещение админа с ссылкой неутверждённых товаров
            if (isSuccess.count > 0) {
                notifyAdminInactiveGoods();
            }
            alert("Курс изменён");
        }
   
        window.location.href = window.location.href;
    })
}
