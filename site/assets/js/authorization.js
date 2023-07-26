console.log("Подключили auth");

/* ---------- ВХОД ПО ENTER ---------- */
let inputBox = document.querySelectorAll('.input-login');
inputBox.forEach (item => {
    item.addEventListener('keypress', (e) => {
        if(e.key == 'Enter') {
            logIn();
        }
       
    }) 
})

/* ---------- ВХОД В АККАУНТ ---------- */
function logIn() {

    const login = document.getElementById('email');
    const pass = document.getElementById('password');
    const info_auth = document.querySelector('.info-auth');

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
    let params = "email=" + login.value.trim() + "&password=" + pass.value.trim() + "&deleted=0&is_active=1";
    
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
    let urlSearch = window.location.search;
    let urlGetParams = new URLSearchParams(urlSearch);
    if(urlGetParams.get('return_url')) {
        window.location.href = urlSearch.replace('?return_url=', '');
    } else {
        window.location.href = 'http://localhost/index.php';
    }

}

// /* ---------- ОТОБРАЗИТЬ В КРУЖКЕ ТОЛЬКО ПЕРВУЮ БУКВУ ИМЕНИ ---------- */

// let profileAvatar = document.querySelector('.menu-top__profile-avatar');
// let profileName = document.querySelector('.menu-top__profile-name').innerText;
// profileAvatar.innerText = profileName.trim()[0];

/* ---------- ВЫХОД ИЗ АККАУНТА ---------- */
function logOut() {
    
    //берём токен из куки
    const cookie = document.cookie.match(/profile=(.+?)(;|$)/);

    //если токена нет, то рисуем форму Авторизации и выходим из функции
    if (cookie == null || cookie == undefined || cookie == ""){
        window.location.href = 'http://localhost/pages/login.php';
        return;
    }

    //если токен есть , то передаём его на сервер
    let params = "token=" + cookie[1];

    //отправляем запрос на сервер
    sendRequestFormUrlPOST("http://localhost/api/authorization/logout.php", params);

    //удаляем токен из куки
    document.cookie = "profile=''; path=/; max-age=-1";

    //рисуем форму авторизации
    window.location.href = 'http://localhost/pages/login.php';

}