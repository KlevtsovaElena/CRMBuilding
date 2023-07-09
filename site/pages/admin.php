<?php 
    // собираем массив из подключаемых файлов css и js
    $styleSrc = [
        "<link rel='stylesheet' href='./../assets/css/base.css'>",
        "<link rel='stylesheet' href='./../assets/css/list-products.css'>",
        "<link rel='stylesheet' href='./../assets/css/new-order.css'>",
        "<link rel='stylesheet' href='./../assets/css/admin.css'>"
    ];
    $scriptsSrc = [
        "<script src='./../assets/js/main.js'></script>",
        "<script src='./../assets/js/admin.js'></script>"
    ];
?>

<!-- подключим хэдер -->
<?php include('./../components/header.php'); ?>   

    <?php
        //записываем в переменную текущую страницу
        $currentPage;
        if(isset($_GET['page']) && $_GET['page'] > 0) {
            $currentPage = $_GET['page'];
        } else {
            $currentPage = 1;
        }
    ?>

    <!-- проверяем, какой передан гет-параметр -->
    <?php
    if(isset($_GET['section'])) { 
        //если совершен переход на Категории
        $dataJson; $keyName; $title;
        if($_GET['section'] == 'categories') { 
            $dataJson = file_get_contents("http://nginx/api/categories.php");
            $keyName = 'category_name';
            $title = 'Категории';
    ?>     

    <?php } 
        //если совершен переход на Бренды
        if($_GET['section'] == 'brands') { 
            $dataJson = file_get_contents("http://nginx/api/brands.php");
            $keyName = 'brand_name'; 
            $title = 'Бренды';      
    ?> 

    <?php }
        //соберём данные для отображения в форме 
        $data = json_decode($dataJson, true); 
    ?>

        <!-- далее отрисовываем всю страницу Брендов или Категорий -->
        <p class="page-title" data-name="<?= $keyName; ?>"><?= $title ?></p>

        <section class="form-filters">

            <div class="d-iblock add-block">
                <!-- поле добавления новой категории -->
                <input class="input-add" type="text" id="add-new" name="add-new" value="" placeholder="Введите название">
                <button class="btn btn-ok d-iblock" onclick="addNew('<?= $_GET['section'] ?>')">+ Добавить</button>
            </div>
            <br>
            <div class="form-elements-container">
                <!-- выбор кол-ва отображаемых записей на странице -->
                <div class="d-iblock">Показывать по
                    <select id="limit" name="limit" value="" required>
                    <?php
                        //если лимит не был задан
                        if (!isset($_GET['limit'])) {
                        ?>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="all" <?php if (isset($_GET['search'])) {echo 'selected';} ?> >все</option>
                        <?php
                        //если лимит уже задан
                        } else {
                            $limit = $_GET['limit'];
                            ?>
                            <option value="10" <?php if ($_GET['limit'] == 10) {echo 'selected';} ?> >10</option>
                            <option value="20" <?php if ($_GET['limit'] == 20) {echo 'selected';} ?> >20</option>
                            <option value="50" <?php if ($_GET['limit'] == 50) {echo 'selected';} ?> >50</option>
                            <option value="all" <?php if ($_GET['limit'] == 'all' || isset($_GET['search'])) {echo 'selected';} ?> >все</option>
                        <?php }
                        ?> 
                    </select>
                </div>

                <!-- поле поиска -->
                <input type="search" id="search" name="search" value="" placeholder="Поиск">
                <!-- кнопка, активирующая лимит записей на странице и поиск -->
                <button onclick="apply('<?= $_GET['section'] ?>')" class="btn btn-ok d-iblock">Применить</button>
            </div>

        </section>

        <!-- список выдаваемых элементов -->
        <section class="orders">
            <table>
                
                <thead>
                    <tr role="row">
                        <th class="table-header">№</th>
                        <th class="table-header">Название</th>
                        <th></th>
                        <th></th>
                    </tr>             
                </thead>

                <tbody class="list-products__body" id="new-order-products">

                    <?php 
                    //проверяем, выбран ли лимит на кол-во отображаемых элементов
                    if (isset($_GET['limit'])) {
                        //если выбрано отображать все
                        if($_GET['limit'] == 'all') {
                            $limit = count($data);
                        } 
                    //если лимит не выбран, задаем дефолтное значение
                    } else {
                        $limit = 10;
                    }

                    //считаем и записываем в переменные общее кол-во страниц и оффсет
                    $totalPages = ceil(count($data) / $limit);
                    $offset = ($currentPage - 1) * $limit;
                    $totalNumElements = count($data);

                    //если мы НЕ на первой странице
                    if(isset($_GET['page']) && $_GET['page'] > 1) {
                        //print_r($currentPage);
                        for ($i = $offset; $i < $limit + $offset; $i++) {
                            //проверка на то, чтобы выводилось не больше строк, чем есть в БД
                            if(isset($data[$i]['id'])) {
                            ?>
                                <tr role="row" class="list-orders__row">
                                    <td><?= $i + 1 ?></td>
                                    <td class="list-orders_status"><?= $data[$i]["$keyName"] ?></td>
                                    <td></td>
                                    <td><svg onclick="deleteOne(<?= $data[$i]["$keyName"], $data[$i]['id'] ?>)" class="garbage" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20px" height="20px" viewBox="0 0 32 32" id="icons" version="1.0" xml:space="preserve" fill="#000000"><g id="SVGRepo_iconCarrier"><rect class="garbage-svg" height="22" id="XMLID_14_" width="16" x="8" y="7"/><line class="garbage-svg" id="XMLID_4_" x1="16" x2="16" y1="10" y2="26"/><line class="garbage-svg" id="XMLID_118_" x1="20" x2="20" y1="10" y2="26"/><line class="garbage-svg" id="XMLID_3_" x1="12" x2="12" y1="10" y2="26"/><line class="garbage-svg" id="XMLID_5_" x1="5" x2="27" y1="7" y2="7"/><rect class="garbage-svg" height="4" id="XMLID_6_" width="6" x="13" y="3"/><g id="XMLID_386_"/></g></svg></td>
                                </tr>
                            <?php }
                        } 

                    //если мы на первой странице и поиск не активирован
                    } elseif(!isset($_GET['page']) && !isset($_GET['search'])) {
                        //отрисовываем список категорий или брендов
                        for ($i = 0; $i < $limit; $i++) {
                            //проверка на то, чтобы выводилось не больше строк, чем есть в БД
                            if(isset($data[$i]['id'])) { ?>
                            <tr role="row" class="list-orders__row">
                                <td><?= $i + 1; ?></td>
                                <td class="list-orders_status"><?= $data[$i]["$keyName"] ?></td>
                                <td></td>
                                <td><svg onclick="deleteCategory(<?= $data[$i]['id'] ?>)" class="garbage" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20px" height="20px" viewBox="0 0 32 32" id="icons" version="1.0" xml:space="preserve" fill="#000000"><g id="SVGRepo_iconCarrier"><rect class="garbage-svg" height="22" id="XMLID_14_" width="16" x="8" y="7"/><line class="garbage-svg" id="XMLID_4_" x1="16" x2="16" y1="10" y2="26"/><line class="garbage-svg" id="XMLID_118_" x1="20" x2="20" y1="10" y2="26"/><line class="garbage-svg" id="XMLID_3_" x1="12" x2="12" y1="10" y2="26"/><line class="garbage-svg" id="XMLID_5_" x1="5" x2="27" y1="7" y2="7"/><rect class="garbage-svg" height="4" id="XMLID_6_" width="6" x="13" y="3"/><g id="XMLID_386_"/></g></svg></td>
                            </tr>
                        <?php }
                        } 
                    } 

                    //если активирован поиск
                    if(isset($_GET['search'])) {
                        $dataJson = file_get_contents("http://nginx/api/" . $_GET['section'] . ".php?search=" . $_GET['search']);
                        $data = json_decode($dataJson, true);
                        //отрисовываем список категорий или брендов, которые совпадают с поисковым запросом
                        $totalNumElements = 0;
                        for ($i = 0; $i < count($data); $i++) { 
                            if(isset($data[$i]['id'])) { 
                                $totalNumElements++; ?>
                                <tr role="row" class="list-orders__row">
                                    <td><?= $i + 1; ?></td>
                                    <td class="list-orders_status"><?= $data[$i]["$keyName"] ?></td>
                                    <td></td>
                                    <td><svg onclick="deleteCategory(<?= $data[$i]['id'] ?>)" class="garbage" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20px" height="20px" viewBox="0 0 32 32" id="icons" version="1.0" xml:space="preserve" fill="#000000"><g id="SVGRepo_iconCarrier"><rect class="garbage-svg" height="22" id="XMLID_14_" width="16" x="8" y="7"/><line class="garbage-svg" id="XMLID_4_" x1="16" x2="16" y1="10" y2="26"/><line class="garbage-svg" id="XMLID_118_" x1="20" x2="20" y1="10" y2="26"/><line class="garbage-svg" id="XMLID_3_" x1="12" x2="12" y1="10" y2="26"/><line class="garbage-svg" id="XMLID_5_" x1="5" x2="27" y1="7" y2="7"/><rect class="garbage-svg" height="4" id="XMLID_6_" width="6" x="13" y="3"/><g id="XMLID_386_"/></g></svg></td>
                                </tr>
                        <?php }
                        } 
                    } ?>
                
                </tbody>
                
            </table>
            <!-- внизу таблицы выдаем общее количество записей -->
            <div class="info-table">Всего записей: <?= $totalNumElements ?></div>
        </section>

        <!-- если НЕ одна страница и НЕ задан поиск, показываем внизу пагинацию -->
        <?php if($totalPages > 1 && !isset($_GET['search'])) { ?>
            <section class="pagination-wrapper">
                <div class="page-switch">                 
                    <a href="?section=<?= $_GET['section']?>&limit=<?= $limit ?>&page=<?= $currentPage - 1; ?>" class="page-switch__prev" <?php if($currentPage <= 1) { ?>  disabled <?php } ?> > 
                        <svg  class="fill" viewBox="0 8 23 16" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>down</title> <path d="M11.125 16.313l7.688-7.688 3.594 3.719-11.094 11.063-11.313-11.313 3.5-3.531z"></path> </g></svg>
                    </a>
                    
                    <span class="current-page"><?= $currentPage ?></span>
                    <a href="?section=<?= $_GET['section']?>&limit=<?= $limit ?>&page=<?= $currentPage + 1; ?>" class="page-switch__next"  <?php if($currentPage == $totalPages) { ?>  disabled <?php } ?> >
                        <svg  class="fill" viewBox="0 8 23 16" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>down</title> <path d="M11.125 16.313l7.688-7.688 3.594 3.719-11.094 11.063-11.313-11.313 3.5-3.531z"></path> </g></svg>
                    </a>
                </div>
                <div class="page-status">стр <span class="current-page"><?= $currentPage ?></span> из <span class="total-page"><?= $totalPages ?></span></div>
            </section>
        <?php } ?>

    <?php } ?>

<!-- подключим футер -->
<?php include('./../components/footer.php'); ?>

