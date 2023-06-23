<?php include('./../components/header.php'); ?>
                
                <p class="page-title">СПИСОК ТОВАРОВ</p>

                <a href="./../pages/vendor-add-product.php" class="btn btn-ok d-iblock">+ Добавить товар</a>

                <!-- соберём данные для отображения в форме -->

                <?php
                    $brandsJson = file_get_contents("http://nginx/api/brands.php");
                    $brands = json_decode($brandsJson, true);
                    $brands_table = [];
                    foreach($brands as $brand) {
                        $brands_table += [$brand['id'] => $brand['brand_name']];
                    }

                    $categoriesJson = file_get_contents("http://nginx/api/categories.php");
                    $categories = json_decode($categoriesJson, true);
                    $categories_table = [];
                    foreach($categories as $category) {
                        $categories_table += [$category['id'] => $category['category_name']];
                    }
                ?>

                <div class="form-filters">

                    <input type="hidden" id="vendor_id" name="vendor_id" value="111">
                    
                    <div class="form-elements-container">
                        <!-- список -->
                        <label>Сортировать по
                            <select id="orderby" name="orderby" value="">
                                <option value="article">Артикул</option>
                                <option value="brand_id">Бренд</option>
                                <option value="category_id">Категория</option>
                                <option value="name">Наименование</option>
                                <option value="price:asc">Цена min</option>
                                <option value="price:asc">Цена max</option>
                                <option value="quantity_available">Остаток</option>
                            </select>
                            </label>
                        <!-- список -->
                        <label>Бренд
                        <select id="brand_id" name="brand_id" value="">
                            <option value="">Все</option>
                            <?php foreach($brands as $brand) { ?>
                                <option value="<?= $brand['id']; ?>"><?= $brand['brand_name']; ?></option>
                            <?php }; ?>
                        </select>
                        </label>
                        <!-- список -->
                        <label>Категория
                        <select id="category_id" name="category_id" value="">
                            <!-- <option value="" class="select-default" selected>Выберите категорию...</option> -->
                            <option value="">Все</option>
                            <?php foreach($categories as $category) { ?>
                                <option value="<?= $category['id']; ?>"><?= $category['category_name']; ?></option>
                            <?php }; ?>
                        </select>
                        </label>
                        <!-- список -->
                        <label>Показывать по
                            <select id="limit" name="limit" value="" required>
                                <!-- <option value="" class="select-default" selected>Выберите категорию...</option> -->
                                <option value="20">20</option>
                                <option value="40">40</option>
                                <option value="100">100</option>
                                <option value="">все</option>
                            </select>
                            </label>
                        <label>
                            <br>
                            <input type="search" id="search" name="search" value="" placeholder="Поиск">
                            
                        </label>
                        <div>
                            <button class="btn btn-ok" >Применить</button>
                        </div>
                    </div>
                </div>

                <div class="products">
                    <table id="list-products">

                        <thead>
                            <tr role="row">
                                <!-- <th style="width: 17px;">
                                    <input type="checkbox" name="select-all"  value="" style="width: 17px;">
                                </th> -->
                                <th>Артикул</th>
                                <th>Наименование</th>
                              
                                <th>Бренд</th>
                                <th>Категория</th>
                                <th>Картинка</th>
                                <th>Остаток</th>
                                <th>Цена</th>
                                <th>Цена рыночная</th>
                               
                            </tr>
                        </thead>

                        <tbody class="list-products__body">

                            <?php
                                $productsJson = file_get_contents("http://nginx/api/products.php?vendor_id=111");
                                $products = json_decode($productsJson, true);

                                foreach($products as $product) {
                            ?>

                            <tr role="row" class="list-products__row">
                                <!-- <td style="width: 17px;"><input type="checkbox" value="" style="width: 17px;"></td> -->
                                <td><a href="#"><strong><?= $product['article']; ?></strong></a></td>
                                <td><a href="#"><strong><?= $product['name']; ?></strong></a></td>
                                <td><?= $brands_table[$product['brand_id']]; ?></td>
                                <td><?= $categories_table[$product['category_id']]; ?></td>
                                <td><img src="<?= $product['photo']; ?>" /></td>
                                <td><?= $product['quantity_available']; ?></td>
                                <td><?= $product['price']; ?></td>
                                <td><?= $product['max_price']; ?></td>
                            </tr>

                        <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="pagination-wrapper"></div>

                <!-- ШАБЛОНЫ -->
                <template id="template-body-table">
                    
                    <tr role="row" class="list-products__row">
                        <td><a href="#"><strong>${article}</strong></a></td>
                        <td><a href="#"><strong>${name}</strong></a></td>
                        <td>${brand_id}</td>
                        <td>${category_id}</td>
                        <td>${photo}</td>
                        <td>${quantity_available}</td>
                        <td>${price}</td>
                        <td>${max_price}</td>
                    </tr>
         
                </template>

                <template id="template-pagination">
                    <div class="page-switch">                
                        <div class="page-switch__prev"  onclick="switchPage(-1)">
                            <svg  class="fill" viewBox="0 8 23 16" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>down</title> <path d="M11.125 16.313l7.688-7.688 3.594 3.719-11.094 11.063-11.313-11.313 3.5-3.531z"></path> </g></svg>
                        </div>
                        <span class="current-page">${currentPage}</span>
                        <div class="page-switch__next" onclick="switchPage(1)">
                            <svg  class="fill" viewBox="0 8 23 16" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>down</title> <path d="M11.125 16.313l7.688-7.688 3.594 3.719-11.094 11.063-11.313-11.313 3.5-3.531z"></path> </g></svg>
                        </div>
                    </div>
                    <div class="page-status">стр <span class="current-page">${currentPage}</span> из <span class="total-page">${totalPages}</span></div>
                </template>



        </div>
    </section>       

<style>
.page-switch {
    display: flex;
}
.page-switch svg {
    width: 15px;
    cursor: pointer;
}

.page-switch__prev svg {
    transform: rotate(90deg);
}

.page-switch__next svg {
    transform: rotate(270deg);
}

.page-switch .current-page {
    margin: 0 10px;
    font-weight: 700;
}

.pagination {
    display: flex;
    padding-left: 0;
    list-style: none;
}
.products {
    margin-top: 10px;
}
.table {
    background-color: white;
}
td, th {
    padding: 5px;
    border: 1px solid black;
}
.form-filters input, 
.form-filters select {
    max-width: unset;
    min-width: unset;
    width: 100px;
}
.form-filters input[type="search"] {
    width: 200px;
}

img {
    width: 30px;
    height: 30px;
}
</style>




</div>
    </section>    
      
        
<script src="./../assets/js/main.js"></script>    
<script src="./../assets/js/list-products.js"></script>    
<?php include('./../components/footer.php'); ?>

        