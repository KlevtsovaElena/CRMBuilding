//функция отображения формы отправки предложения оптовика админу
function toggleOfferToAdmin() {
    //открываем или скрываем форму
    document.getElementById('form-offer').classList.toggle('d-none');
    //открываем или скрываем строку с подзаголовком
    document.querySelector('.title-send').classList.toggle('d-none');
}

//функция отмены отправки предложения оптовика админу
function cancelOfferToAdmin() {

    //очищаем текстовое поле
    let textarea = document.getElementById('text-offer');
    textarea.value = '';

    //открываем или скрываем форму
    toggleOfferToAdmin();
}

//функция отправки предложения оптовика админу
function sendOfferToAdmin(id) {

    //достаем текст сообщения
    let text = document.getElementById('text-offer').value;

    //собираем ссылку на нужный эндпойнт
    let link = mainUrl + '/api/notification/telegram-send-wh-a-notification.php';

    if (text == '') {
        alert("Текстовое поле не может быть пустым");
        return;
    } else {
        //формируем параметры для передачи в апишку
        let obj = JSON.stringify({
            "vendor_id" : id, //id оптовика
            "text" : text
        });

        //передаем параметры на сервер в пост-запросе
        sendRequestPOST(link, obj);

        console.log('оповещение отправлено');

        //очищаем текстовое поле
        document.getElementById('text-offer').value = '';
    }

    // //вытащить из БД телеграм айди админа по номеру роли

    // if (text == '') {
    //     alert("Текстовое поле не может быть пустым");
    // } else {
    //     //формирование ссылки
    //     let link = 'https://api.telegram.org/bot6251938024:AAG84w6ZyxcVqUxmRRUW0Ro8d4ej7FpU83o/sendMessage?chat_id=' + customer_tg_id + '&text=' + text;

    //     //отправляем гет-запрос
    //     sendRequestGET(link);
    // }
}