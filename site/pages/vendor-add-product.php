<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRMBuilding</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Open+Sans:wght@400;500;600;700&family=Raleway:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="./../assets/css/base.css">
</head>

<body>
    
</body>
</html>
<?php include('./../components/header.php'); ?>
    
        <div><h1>Добавить товар</h1></div>

                
        <form action="#" method="post" enctype="multipart/form-data">

            <input type="hidden" name="vendorId" value="111">
            <div class="form-elements-container">
                <p>Наименование</p><input type="text" id="name" name="name" value="" required>
                <!-- фото -->
                <p>
                    <div class="img-title-form">Изображениe для карточки 
                        <div>(Рекомендованные параметры такие НА такие)</div>
                        <div class="img-title-prew img-guide"><img></div>
                        <input type="file"  id="photo" name="photo" accept="image/png, image/jpg, image/jpeg" required>                               
                    </div> 
                </p>

                <!-- список -->
                <p>Бренд</p>
                <select id="brandId" name="brandId" value="" required>
                    <option value="Бренд1">Бренд1</option>
                    <option value="Бренд1">Бренд2</option>
                    <option value="Бренд1">Бренд3</option>
                    <option value="Бренд1">Бренд4</option>
                </select>

                <!-- список -->
                <p>Категория</p>
                <select id="categoryId" name="categoryId" value="" required>
                    <option value="Категория1">Категория1</option>
                    <option value="Категори1">Категория2</option>
                    <option value="Категория1">Категория3</option>
                    <option value="Категория1">Категория4</option>
                </select>


                <p>Описание</p><textarea id="description" name="description" required></textarea>
                <p>Артикул</p><input type="text" id="noname" name="" value="" required>
                <p>Количество</p><input type="number" id="quantityAvailable" name="quantityAvailable" value="" required>
                <p>Цена (руб.)</p><input type="number" id="price" name="price" value="" required>
                <p>Цена рыночная (руб.)</p><input type="number" id="maxPprice" name="maxPrice" value="" required>

            </div>
            <div>
                <button class="btn btn-ok" type="submit">Сохранить</button>
                <a href="#" class="btn btn-neutral">Сбросить изменения</a> 
            </div>
        </form>

               

<?php include('./../components/footer.php'); ?>



</body>
</html>