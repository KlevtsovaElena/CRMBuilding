<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRMBuilding</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Open+Sans:wght@400;500;600;700&family=Raleway:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel='stylesheet' href='./../assets/css/base.css'>
</head>
<body>


<button class="btn btn-ok d-iblock" onclick="addVendorToggle()">+ Добавить поставщика</button>

<!-- Добавление поставщика -->
<section class="add-vendor d-none">

    <!-- здесь храним id поставщика -->
    <input type="hidden" id="vendor_id" name="vendor_id" value="<?= $vendor_id; ?>">

    <form class="form-add-vendor form-elements-container">

        <!-- название -->
        <div class="form-add-vendor__item">
            <p>Название</p><input type="text" id="name" name="name" value="" required>
            <div class="error-info d-none"></div>
        </div>

        <!-- город -->
        <div class="form-add-vendor__item">
            <p>Город</p>
            <select id="city_id" name="city_id" value="" required>
                <option value="" selected hidden></option>

                <?php 
                $citiesJson = file_get_contents("http://nginx/api/cities.php");
                $cities = json_decode($citiesJson, true);

                foreach($cities as $city) { ?>
                    <option value="<?= $city['id']; ?>"><?= $city['name']; ?></option>
                <?php }; ?>

            </select>
            <div class="error-info d-none"></div> 
        </div>

        <!-- комментарий -->
        <div class="form-add-vendor__item">
            <p>Комментарий</p><textarea id="comment" name="comment"></textarea>
            <div class="error-info d-none"></div> 
        </div>

        <!-- телефон -->
        <div class="form-add-vendor__item">
            <p>Телефон</p><input type="tel" id="phone" name="phone" value="">
            <div class="error-info d-none"></div>
        </div>

        <!-- email -->
        <div class="form-add-vendor__item">
            <p>Email</p><input type="email" id="email" name="email" value="" required>
            <div class="error-info d-none"></div>
        </div>

        <!-- статус -->
        <div class="form-add-vendor__item">
            <p>Статус</p>
            <select id="is_active" name="is_active" value="" required>
                <option value="1">Активен</option>
                <option value="0">Не активен</option>
            </select>
            <div class="error-info d-none"></div>
        </div> 

        <!-- hash -->
        <div class="form-add-vendor__item d-none">
            <p>Уникальный идентификатор поставщика</p>
            <input type="text" id="unique_id" name="unique_id" value="" required>
            <div class="gen-hash btn btn-ok d-iblock">генератор идентификатора</div>
            <div class="error-info d-none"></div>
        </div>

        <div>
            <button class="btn btn-ok" onclick="addVendor()">Сохранить</button>
        </div>

    </form>
    <div class="link-bot"></div>
    <div class="vendor-data-temp"></div>
</section>


<script src='./../assets/js/main.js'></script>
    
</body>
</html>

<style>
.gen-hash {
    margin: 5px 0px;
}
</style>

<script>
console.log('подключили add-vendor.js');
const formAddVendor = document.querySelector('.form-add-vendor');
const generateHashButton = document.querySelector('.gen-hash');
const vendorDataTemp = document.querySelector('.vendor-data-temp');
const linkBot = document.querySelector('.link-bot');

// запишем значения полей формы в переменные
const nameVendor = formAddVendor.querySelector('#name');
const cityId = formAddVendor.querySelector('#city_id');
const comment = formAddVendor.querySelector('#comment');
const phone = formAddVendor.querySelector('#phone');
const email = formAddVendor.querySelector('#email');
const is_active = formAddVendor.querySelector('#is_active');
const uniqueIdInput = formAddVendor.querySelector('#unique_id');



let tempPass = ''; 

generateHashButton.addEventListener('click', (e) => {
    let uniqueId = generateHashString(12);
    uniqueIdInput.value = uniqueId;
})

function addVendor() {
    //предотвратить дефолтные действия, отмена отправки формы (чтобы страница не перезагружалась)
    event.preventDefault(); 

    hasError = validationAddVendor();

    // если были ошибки, то выходим
    console.log(hasError);
    if (hasError) {
        return;
    }

    tempPass = generateHashString(10);

    uniqueIdInput.value = generateHashString(12);

    // соберём json для передачи на сервер
    let obj = JSON.stringify({
        'name': nameVendor.value.trim(),
        'city_id': cityId.value,
        'comment': comment.value.trim(),
        'phone': phone.value,
        'email': email.value.trim(),
        'is_active': is_active.value,
        'unique_id': uniqueIdInput.value,
        'temp_password': tempPass,
        'role': 2
    });

    // передаём данные на сервер
    let responseJson = sendRequestPOST('http://localhost/api/vendors.php', obj);
    let response;
    // получаем ответ с сервера
    if (responseJson) {
       response = JSON.parse(responseJson);
    } else {
        return;
    }
console.log(response);

    // formAddVendor.reset();

    // показать ссылку на бота
    linkBot.innerText = response['linkBot'];
    // показать логин и временный пароль
    vendorDataTemp.innerText = "Логин: " + response['login'] + 
                                "    Временный пароль: " + response['tempPass'];

}

function validationAddVendor() {
    return false;
}

function addVendorToggle() {
    document.querySelector('.add-vendor').classList.toggle('d-none');
}

function generateHashString(n) {
  const numberChars = '0123456789';
  const upperChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  const lowerChars = 'abcdefghijklmnopqrstuvwxyz';
 
  const stringAll = numberChars + upperChars + lowerChars;

  let result = '';

  for (let i = 0; i < n; i++) {
    let randomChar = Math.floor(Math.random() * stringAll.length);
    result += stringAll[randomChar];
  }
  return result;
}


</script>
        