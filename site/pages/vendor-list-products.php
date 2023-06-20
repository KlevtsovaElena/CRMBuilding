<?php include('./../components/header.php'); ?>
                
                <div><h3>СПИСОК ТОВАРОВ</h3></div>

                <a href="./../pages/vendor-add-product.php" class="btn btn-ok d-iblock">+ Добавить товар</a>
                <form action="#" method="post" enctype="multipart/form-data">

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
                            <option value="1">Все</option>
                            <option value="1">Бренд1</option>
                            <option value="2">Бренд2</option>
                            <option value="3">Бренд3</option>
                            <option value="4">Бренд4</option>
                        </select>
                        </label>
                        <!-- список -->
                        <label>Категория
                        <select id="categoryId" name="categoryId" value="" required>
                            <!-- <option value="" class="select-default" selected>Выберите категорию...</option> -->
                            <option value="1">Все</option>
                            <option value="1">Категория1</option>
                            <option value="2">Категория2</option>
                            <option value="3">Категория3</option>
                            <option value="4">Категория4</option>
                        </select>
                        </label>
                        <!-- список -->
                        <label>Показывать по
                            <select id="categoryId" name="categoryId" value="" required>
                                <!-- <option value="" class="select-default" selected>Выберите категорию...</option> -->
                                <option value="1">20</option>
                                <option value="1">40</option>
                                <option value="2">100</option>
                                <option value="3">все</option>
                            </select>
                            </label>
                        <label>
                            <input type="search" id="search" name="search" value="" required placeholder="Поиск">
                            </input>
                        </label>
                        <div>
                            <button class="btn btn-ok" type="submit">Применить</button>
                        </div>
                    </div>
                </form>

                <div class="products">
                    <table class="table table-ecommerce-simple table-striped mb-0 dataTable no-footer" id="datatable-ecommerce-list" style="min-width: 550px;" role="grid" aria-describedby="datatable-ecommerce-list_info">

                        <thead>
                            <tr role="row"><th width="3%" class="sorting_disabled" rowspan="1" colspan="1" aria-label="" style="width: 17px;"><input type="checkbox" name="select-all" class="select-all checkbox-style-1 p-relative top-2" value=""></th><th width="8%" class="sorting" tabindex="0" aria-controls="datatable-ecommerce-list" rowspan="1" colspan="1" aria-label="ID: activate to sort column ascending" style="width: 57.3375px;">ID</th><th width="28%" class="sorting_desc" tabindex="0" aria-controls="datatable-ecommerce-list" rowspan="1" colspan="1" aria-label="Name: activate to sort column ascending" style="width: 272.025px;" aria-sort="descending">Name</th><th width="23%" class="sorting" tabindex="0" aria-controls="datatable-ecommerce-list" rowspan="1" colspan="1" aria-label="Slug: activate to sort column ascending" style="width: 218.562px;">Slug</th><th width="38%" class="sorting" tabindex="0" aria-controls="datatable-ecommerce-list" rowspan="1" colspan="1" aria-label="Parent Category: activate to sort column ascending" style="width: 379.275px;">Parent Category</th></tr>
                        </thead>
                        <tbody>
                                                      
                        <tr role="row" class="odd">
                                <td width="30"><input type="checkbox" name="checkboxRow1" class="checkbox-style-1 p-relative top-2" value=""></td>
                                <td><a href="ecommerce-category-form.html"><strong>199</strong></a></td>
                                <td class="sorting_1"><a href="ecommerce-category-form.html"><strong>Category Name Example 9</strong></a></td>
                                <td class="">category-name-example-9</td>
                                <td class="">Parent Category Name 9</td>
                            </tr><tr role="row" class="even">
                                <td width="30"><input type="checkbox" name="checkboxRow1" class="checkbox-style-1 p-relative top-2" value=""></td>
                                <td><a href="ecommerce-category-form.html"><strong>198</strong></a></td>
                                <td class="sorting_1"><a href="ecommerce-category-form.html"><strong>Category Name Example 8</strong></a></td>
                                <td class="">category-name-example-8</td>
                                <td class="">Parent Category Name 8</td>
                            </tr><tr role="row" class="odd">
                                <td width="30"><input type="checkbox" name="checkboxRow1" class="checkbox-style-1 p-relative top-2" value=""></td>
                                <td><a href="ecommerce-category-form.html"><strong>197</strong></a></td>
                                <td class="sorting_1"><a href="ecommerce-category-form.html"><strong>Category Name Example 7</strong></a></td>
                                <td class="">category-name-example-7</td>
                                <td class="">Parent Category Name 7</td>
                            </tr><tr role="row" class="even">
                                <td width="30"><input type="checkbox" name="checkboxRow1" class="checkbox-style-1 p-relative top-2" value=""></td>
                                <td><a href="ecommerce-category-form.html"><strong>196</strong></a></td>
                                <td class="sorting_1"><a href="ecommerce-category-form.html"><strong>Category Name Example 6</strong></a></td>
                                <td class="">category-name-example-6</td>
                                <td class="">Parent Category Name 6</td>
                            </tr><tr role="row" class="odd">
                                <td width="30"><input type="checkbox" name="checkboxRow1" class="checkbox-style-1 p-relative top-2" value=""></td>
                                <td><a href="ecommerce-category-form.html"><strong>195</strong></a></td>
                                <td class="sorting_1"><a href="ecommerce-category-form.html"><strong>Category Name Example 5</strong></a></td>
                                <td class="">category-name-example-5</td>
                                <td class="">Parent Category Name 5</td>
                            </tr><tr role="row" class="even">
                                <td width="30"><input type="checkbox" name="checkboxRow1" class="checkbox-style-1 p-relative top-2" value=""></td>
                                <td><a href="ecommerce-category-form.html"><strong>194</strong></a></td>
                                <td class="sorting_1"><a href="ecommerce-category-form.html"><strong>Category Name Example 4</strong></a></td>
                                <td class="">category-name-example-4</td>
                                <td class="">Parent Category Name 4</td>
                            </tr><tr role="row" class="odd">
                                <td width="30"><input type="checkbox" name="checkboxRow1" class="checkbox-style-1 p-relative top-2" value=""></td>
                                <td><a href="ecommerce-category-form.html"><strong>193</strong></a></td>
                                <td class="sorting_1"><a href="ecommerce-category-form.html"><strong>Category Name Example 3</strong></a></td>
                                <td class="">category-name-example-3</td>
                                <td class="">Parent Category Name 3</td>
                            </tr><tr role="row" class="even">
                                <td width="30"><input type="checkbox" name="checkboxRow1" class="checkbox-style-1 p-relative top-2" value=""></td>
                                <td><a href="ecommerce-category-form.html"><strong>192</strong></a></td>
                                <td class="sorting_1"><a href="ecommerce-category-form.html"><strong>Category Name Example 2</strong></a></td>
                                <td class="">category-name-example-2</td>
                                <td class="">Parent Category Name 2</td>
                            </tr><tr role="row" class="odd">
                                <td width="30"><input type="checkbox" name="checkboxRow1" class="checkbox-style-1 p-relative top-2" value=""></td>
                                <td><a href="ecommerce-category-form.html"><strong>206</strong></a></td>
                                <td class="sorting_1"><a href="ecommerce-category-form.html"><strong>Category Name Example 16</strong></a></td>
                                <td class="">category-name-example-16</td>
                                <td class="">Parent Category Name 16</td>
                            </tr><tr role="row" class="even">
                                <td width="30"><input type="checkbox" name="checkboxRow1" class="checkbox-style-1 p-relative top-2" value=""></td>
                                <td><a href="ecommerce-category-form.html"><strong>205</strong></a></td>
                                <td class="sorting_1"><a href="ecommerce-category-form.html"><strong>Category Name Example 15</strong></a></td>
                                <td class="">category-name-example-15</td>
                                <td class="">Parent Category Name 15</td>
                            </tr><tr role="row" class="odd">
                                <td width="30"><input type="checkbox" name="checkboxRow1" class="checkbox-style-1 p-relative top-2" value=""></td>
                                <td><a href="ecommerce-category-form.html"><strong>204</strong></a></td>
                                <td class="sorting_1"><a href="ecommerce-category-form.html"><strong>Category Name Example 14</strong></a></td>
                                <td class="">category-name-example-14</td>
                                <td class="">Parent Category Name 14</td>
                            </tr><tr role="row" class="even">
                                <td width="30"><input type="checkbox" name="checkboxRow1" class="checkbox-style-1 p-relative top-2" value=""></td>
                                <td><a href="ecommerce-category-form.html"><strong>203</strong></a></td>
                                <td class="sorting_1"><a href="ecommerce-category-form.html"><strong>Category Name Example 13</strong></a></td>
                                <td class="">category-name-example-13</td>
                                <td class="">Parent Category Name 13</td>
                            </tr></tbody>
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
</style>




        
<?php include('./../components/footer.php'); ?>