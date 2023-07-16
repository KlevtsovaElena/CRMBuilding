console.log("Подключили auth");

const login = document.getElementById('email');
const pass = document.getElementById('password');
const info_auth = document.querySelector('.info-auth');


function logIn() {

    // очистим информационное поле
    info_auth.innerText = "";

    let hasError = false;

    // валидация
    [login, pass].forEach(item => {
        const errorInfoContainer = item.closest('.form-login__item').querySelector('.error-info');
        if (!(item.value.trim())) {
            item.classList.add('error');   
            errorInfoContainer.innerText = "Заполните данные!";
            errorInfoContainer.classList.remove('d-none');
            hasError = true;
        } else {
            item.classList.remove('error');
            errorInfoContainer.innerText = "";
            errorInfoContainer.classList.add('d-none');
        }
    })

    // если валидация с ошибками - то выходим
    if(hasError) {
        return;
    }

    // иначе, соберём данные для отправки
    let params = "email=" + login.value.trim() + "&password=" + pass.value.trim();
    
    //получаем ответ
    let jsonResponse = sendRequestFormUrlPOST("http://localhost/api/authorization/login.php", params);
    let response = JSON.parse(jsonResponse);

    //проверяем ответ
    //если пользователь не найден или логин/пароль не совпадают, то вернётся  {'success': false, 'error': 'Неверный логин или пароль!'}
    //выведем пользователю
    if(!response['success']) {
        info_auth.innerText = response['error'];
        return;
    }

    //если пользователь найден и логин/пароль верны
    //получим токен, запишем в куки временем жизни 24часа
    if(response['success']) {
        document.cookie = "profile=" + response['token'] + "; path=/";
    }

    // и перейдём на страницу CRM
    window.location.href = 'http://localhost/index.php';
}