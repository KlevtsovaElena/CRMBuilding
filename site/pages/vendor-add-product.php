<?php include('./../components/header.php'); ?>
    <div>ПРИМЕР ЭЛЕМЕНТОВ
        <input type="text" placeholder="test">
        <textarea name="" id="" cols="30" rows="10" placeholder="test"></textarea>
        <button class="btn btn-ok">Сохранить товар</button>
        <button class="btn btn-neutral">Сбросить</button>
        <button class="btn btn-delete">Удалить товар</button>
    </div>

                
    <div><h1>Добавить товар</h1></div>

            
    <form id="form-add-product" method="post" enctype="multipart/form-data">

        <input type="hidden" name="vendorId" value="111">
        <div class="form-elements-container">
            <p>Наименование</p><input type="text" id="name" name="name" value="" required placeholder="jdhvjshdjh">
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
                <option value="1">Бренд1</option>
                <option value="2">Бренд2</option>
                <option value="3">Бренд3</option>
                <option value="4">Бренд4</option>
            </select>

            <!-- список -->
            <p>Категория</p>
            <select id="categoryId" name="categoryId" value="" required>
                <!-- <option value="" class="select-default" selected>Выберите категорию...</option> -->
                <option value="1">Категория1</option>
                <option value="2">Категория2</option>
                <option value="3">Категория3</option>
                <option value="4">Категория4</option>
            </select>


            <p>Описание</p><textarea id="description" name="description" required></textarea>
            <p>Артикул</p><input type="text" id="noname" name="" value="" required>
            <p>Количество</p><input type="number" id="quantityAvailable" name="quantityAvailable" min="0" value="" required>
            <p>Цена (руб.)</p><input type="number" id="price" name="price" min="0" value="" required>
            <p>Цена рыночная (руб.)</p><input type="number" id="maxPprice" name="maxPrice" min="0" value="" required>

        </div>
        <div>
            <button class="btn btn-ok" onclick="addProduct()">Сохранить</button>
            <a href="#" class="btn btn-neutral">Сбросить изменения</a> 
        </div>
    </form>

<?php include('./../components/footer.php'); ?>