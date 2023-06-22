<?php include('./../components/header.php'); ?>
                
                <p class="page-title">СПИСОК ТОВАРОВ</p>

                <a href="./../pages/vendor-add-product.php" class="btn btn-ok d-iblock">+ Добавить товар</a>

                <!-- соберём данные для отображения в форме -->

                <?php
                    $brandsJson = file_get_contents("http://nginx/api/brands.php");
                    $brands = json_decode($brandsJson, true);
                    $categoriesJson = file_get_contents("http://nginx/api/categories.php");
                    $categories = json_decode($categoriesJson, true);
                ?>

                <form class="form-filters" action="#" method="post" enctype="multipart/form-data">

                    <input type="hidden" name="vendorId" value="111">
                    
                    <div class="form-elements-container">
                        <!-- список -->
                        <label>Сортировать по
                            <select id="brandId" name="brandId" value="" required>
                                <option value="1">артикул</option>
                                <option value="2">бренд</option>
                                <option value="3">категория</option>
                                <option value="4">наименование</option>
                                <option value="3">цена</option>
                                <option value="4">остаток</option>
                            </select>
                            </label>
                        <!-- список -->
                        <label>Бренд
                        <select id="brandId" name="brandId" value="" required>
                            <option value="0">Все</option>
                            <?php foreach($brands as $brand) { ?>
                                <option value="<?= $brand['id']; ?>"><?= $brand['brand_name']; ?></option>
                            <?php }; ?>
                        </select>
                        </label>
                        <!-- список -->
                        <label>Категория
                        <select id="categoryId" name="categoryId" value="" required>
                            <!-- <option value="" class="select-default" selected>Выберите категорию...</option> -->
                            <option value="0">Все</option>
                            <?php foreach($categories as $category) { ?>
                                <option value="<?= $category['id']; ?>"><?= $category['category_name']; ?></option>
                            <?php }; ?>
                        </select>
                        </label>
                        <!-- список -->
                        <label>Показывать по
                            <select id="categoryId" name="categoryId" value="" required>
                                <!-- <option value="" class="select-default" selected>Выберите категорию...</option> -->
                                <option value="20">20</option>
                                <option value="40">40</option>
                                <option value="100">100</option>
                                <option value="0">все</option>
                            </select>
                            </label>
                        <label>
                            <br>
                            <input type="search" id="search" name="search" value="" placeholder="Поиск">
                            
                        </label>
                        <div>
                            <button class="btn btn-ok" type="submit">Применить</button>
                        </div>
                    </div>
                </form>

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

                        <tbody>

                            <?php
                                $productsJson = file_get_contents("http://nginx/api/products.php?vendor_id=111");
                                $products = json_decode($productsJson, true);

                                foreach($products as $product) {
                            ?>

                            <tr role="row">
                                <!-- <td style="width: 17px;"><input type="checkbox" value="" style="width: 17px;"></td> -->
                                <td><a href="#"><strong><?= $product['article']; ?></strong></a></td>
                                <td><a href="#"><strong><?= $product['name']; ?></strong></a></td>
                                <td><?= $product['brand_id']; ?></td>
                                <td><?= $product['category_id']; ?></td>
                                <td><?= $product['photo']; ?></td>
                                <td><?= $product['quantity_available']; ?></td>
                                <td><?= $product['price']; ?></td>
                                <td><?= $product['max_price']; ?></td>
                            </tr>

                        <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="pagination-wrapper"><div class="dataTables_paginate paging_simple_numbers" id="datatable-ecommerce-list_paginate"><ul class="pagination pagination-modern pagination-modern-spacing justify-content-center"><li class="paginate_button page-item previous disabled" id="datatable-ecommerce-list_previous"><a href="#" aria-controls="datatable-ecommerce-list" data-dt-idx="0" tabindex="0" class="page-link"><i class="fas fa-chevron-left"></i></a></li><li class="paginate_button page-item active"><a href="#" aria-controls="datatable-ecommerce-list" data-dt-idx="1" tabindex="0" class="page-link">1</a></li><li class="paginate_button page-item "><a href="#" aria-controls="datatable-ecommerce-list" data-dt-idx="2" tabindex="0" class="page-link">2</a></li><li class="paginate_button page-item next" id="datatable-ecommerce-list_next"><a href="#" aria-controls="datatable-ecommerce-list" data-dt-idx="3" tabindex="0" class="page-link"><i class="fas fa-chevron-right"></i></a></li></ul></div></div>

        </div>
    </section>       

<style>
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
</style>




        
<?php include('./../components/footer.php'); ?>


<!-- ?filters&orderBy="article"&brand_id=1&category_id=0&vendor_id=111&search=""&limit=20&quantity=61

где     filters - показывает, что запрос на фильтрацию по полям
        orderBy - по какому полю сортировка
        brand_id - только этот бренд
        category_id - только эта категория
        vendor_id - товары только этого поставщика
        search - содержащие в названии или описании эту строку
        limit - сколько записей выбирать
        quantity - с какой записи этой выборки

        brand_id и category_id равны 0, если по ним выборка не нужна (те выгружать все)
        search - пустая строка, если не нужен поиск -->