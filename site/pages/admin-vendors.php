<?php require('../handler/check-profile.php'); 
if($role !== 1) {
    setcookie('profile', '', -1, '/');
    header('Location: http://localhost/pages/login.php');
    exit(0);
};
?>

<?php 
    // собираем массив из подключаемых файлов css и js
    $styleSrc = [
        "<link rel='stylesheet' href='./../assets/css/base.css'>",
        "<link rel='stylesheet' href='./../assets/css/list-products.css'>",
        "<link rel='stylesheet' href='./../assets/css/list-orders.css'>",
        "<link rel='stylesheet' href='./../assets/css/admin.css'>"
    ];
    $scriptsSrc = [
        "<script src='./../assets/js/main.js'></script>",
        "<script src='./../assets/js/joint.js'></script>",
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

        //соберём данные для отображения в форме 
        $dataJson = file_get_contents("http://nginx/api/vendors.php");
        $data = json_decode($dataJson, true); 
        //print_r($data);

        $citiesJson = file_get_contents("http://nginx/api/cities.php");
        $cities = json_decode($citiesJson, true);

        if(isset($_GET['orderby'])) {
            $orderByArray = explode(";", $_GET['orderby']);
            $orderBy = explode(":", $orderByArray[0]);
            $sortBy = $orderBy[0];
            $mark = $orderBy[1];
            print_r($sortBy);
            print_r($mark);
        } else {
            $sortBy = "";
        }
    ?>

        <!-- далее отрисовываем всю страницу -->
        <p class="page-title">Поставщики</p>

        
        <a href="admin-add-vendor.php" class="btn btn-ok d-iblock">+ Добавить поставщика</a>

         <section class="form-filters">
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
                            <!-- если задан поиск, лимит переключается на "все" -->
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
                <input type="search" id="search" name="search" value="" placeholder="Поиск но названию">
                <!-- кнопка, активирующая выбранный лимит записей на странице и поиск -->
                <button onclick="apply('admin-vendors')" class="btn btn-ok d-iblock">Применить</button>
            </div>

        </section>

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
        $totalNumElements = count($data); ?>

        <!-- таблица поставщиков -->
        <section class="orders">
            <table id="list-orders">

                <thead>
                    <tr role="row">

                        <th data-id="id" data-sort="<?php if ($sortBy == 'id')  {echo $mark; } ?>">№</th>
                        <th data-id="name" data-sort="<?php if ($sortBy == 'name')  {echo $mark; } ?>">Название</th>
                        <th data-id="city_id" data-sort="<?php if ($sortBy == 'city_id')  {echo $mark; } ?>">Город</th>
                        <th data-id="tg_id" data-sort="<?php if ($sortBy == 'tg_id')  {echo $mark; } ?>">Телеграм ID</th>
                        <th data-id="tg_username" data-sort="<?php if ($sortBy == 'tg_username')  {echo $mark; } ?>">Телеграм имя</th>
                        <th data-id="coordinates" data-sort="<?php if ($sortBy == 'coordinates')  {echo $mark; } ?>">Координаты</th>
                        <th data-id="phone" data-sort="<?php if ($sortBy == 'phone')  {echo $mark; } ?>">Телефон</th>
                        <th data-id="email">Email</th>
                        <th data-id="comment">Комментарий</th>
                        <th data-id="total_price" data-sort="<?php if ($sortBy == 'status')  {echo $mark; } ?>">Статус</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody class="list-orders__body">
                <tr role="row" class="list-orders__row">

                    <?php //если мы НЕ на первой странице
                    if(isset($_GET['page']) && $_GET['page'] > 1) {
                        //отрисовываем строки в цикле от начальной до конечной цифры оффсетного значения
                        for ($i = $offset; $i < $limit + $offset; $i++) {
                            //проверка на то, чтобы выводилось не больше строк, чем есть в БД, и что не удален
                            if(isset($data[$i]['id']) && $data[$i]['deleted'] == 0) {
                                //расфишровка статусов
                                $status;
                                if($data[$i]['is_active'] == 1) {
                                    $status = 'Активен';
                                }
                                if($data[$i]['is_active'] == 0) {
                                    $status = 'Не активен';
                                }
                                ?>
                            <!-- вносим в атрибуты общее кол-во страниц и текущую страницу для js -->
                            <tr id="pages-info" role="row" class="list-orders__row" data-pages="<?= $totalPages ?>" data-current-page="<?= $currentPage ?>">
                                <td><a href="#"><strong><?= $data[$i]['id'] ?></strong></a></td>
                                <td><a href="javascript: editVendor(<?= $data[$i]['id'] ?>)"><?= $data[$i]['name'] ?></a></td>
                                <?php
                                //достаем название города через 2 бд и цикл 
                                for ($c = 0; $c < count($cities); $c++) {
                                    if ($data[$i]['city_id'] == $cities[$c]['id']) {?>
                                    <td><?= $cities[$c]['name'] ?></td>
                                <?php } 
                                }?>
                                <td><?= $data[$i]['tg_id'] ?></td>
                                <td><?= $data[$i]['tg_username'] ?></td>
                                <!-- в отдельном цикле отрисовываем координаты поставщика (пока что с проверкой на их наличие) -->
                                <?php if ($data[$i]['coordinates']) { ?>
                                    <td class="list-orders_products">
                                        <?= $data[$i]['coordinates']['latitude'] ?>, <?= $data[$i]['coordinates']['longitude'] ?>
                                    </td> 
                                <?php } else {?>
                                    <td></td> <?php } ?>
                                <td><?= $data[$i]['phone'] ?> </td>
                                <td><?= $data[$i]['email'] ?> </td>
                                <td><?= $data[$i]['comment'] ?></td>
                                <td><?= $status ?></td>
                                <td><svg onclick="deleteOne('admin-vendors', <?=$data[$i]['id'] ?>)" class="garbage" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20px" height="20px" viewBox="0 0 32 32" id="icons" version="1.0" xml:space="preserve" fill="#000000"><g id="SVGRepo_iconCarrier"><rect class="garbage-svg" height="22" id="XMLID_14_" width="16" x="8" y="7"/><line class="garbage-svg" id="XMLID_4_" x1="16" x2="16" y1="10" y2="26"/><line class="garbage-svg" id="XMLID_118_" x1="20" x2="20" y1="10" y2="26"/><line class="garbage-svg" id="XMLID_3_" x1="12" x2="12" y1="10" y2="26"/><line class="garbage-svg" id="XMLID_5_" x1="5" x2="27" y1="7" y2="7"/><rect class="garbage-svg" height="4" id="XMLID_6_" width="6" x="13" y="3"/><g id="XMLID_386_"/></g></svg></td>
                            </tr>
                            <?php }
                        } 
                    
                    //если мы на первой странице
                    } elseif(!isset($_GET['page']) || $_GET['page'] == 1) {
                        //и поиск не активирован
                        if (!isset($_GET['search'])) {
                            //отрисовываем строки в цикле по заданному лимиту отображаемых элементов на 1 странице
                            for ($i = 0; $i < $limit; $i++) {
                                //проверка на то, чтобы выводилось не больше строк, чем есть в БД, и что не удален
                                if(isset($data[$i]['id']) && $data[$i]['deleted'] == 0) { 
                                    //расфишровка статусов
                                    $status;
                                    if($data[$i]['is_active'] == 1) {
                                        $status = 'Активен';
                                    }
                                    if($data[$i]['is_active'] == 0) {
                                        $status = 'Не активен';
                                    }
                                    ?>
                                <!-- вносим в атрибуты общее кол-во страниц и текущую страницу для js -->
                                <tr id="pages-info" role="row" class="list-orders__row" data-pages="<?= $totalPages ?>" data-current-page="<?= $currentPage ?>">
                                    <td><a href="#"><strong><?= $data[$i]['id'] ?></strong></a></td>
                                    <td><a href="javascript: editVendor(<?= $data[$i]['id'] ?>)"><?= $data[$i]['name'] ?></a></td>
                                    <?php 
                                    //достаем название города через 2 бд и цикл
                                    for ($c = 0; $c < count($cities); $c++) {
                                        if ($data[$i]['city_id'] == $cities[$c]['id']) {?>
                                        <td><?= $cities[$c]['name'] ?></td>
                                    <?php } 
                                    }?>
                                    <td><?= $data[$i]['tg_id'] ?></td>
                                    <td><?= $data[$i]['tg_username'] ?></td>
                                    <!-- в отдельном цикле отрисовываем координаты поставщика  (пока что с проверкой на их наличие) -->
                                    <?php if ($data[$i]['coordinates']) { ?>
                                        <td class="list-orders_products">
                                            <?= $data[$i]['coordinates']['latitude'] ?>, <?= $data[$i]['coordinates']['longitude'] ?>
                                        </td> 
                                    <?php } else {?>
                                        <td></td> <?php } ?>
                                    <td><?= $data[$i]['phone'] ?> </td>
                                    <td><?= $data[$i]['email'] ?> </td>
                                    <td><?= $data[$i]['comment'] ?></td>
                                    <td><?= $status ?></td>
                                    <td><svg onclick="deleteOne('admin-vendors', <?=$data[$i]['id'] ?>)" class="garbage" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20px" height="20px" viewBox="0 0 32 32" id="icons" version="1.0" xml:space="preserve" fill="#000000"><g id="SVGRepo_iconCarrier"><rect class="garbage-svg" height="22" id="XMLID_14_" width="16" x="8" y="7"/><line class="garbage-svg" id="XMLID_4_" x1="16" x2="16" y1="10" y2="26"/><line class="garbage-svg" id="XMLID_118_" x1="20" x2="20" y1="10" y2="26"/><line class="garbage-svg" id="XMLID_3_" x1="12" x2="12" y1="10" y2="26"/><line class="garbage-svg" id="XMLID_5_" x1="5" x2="27" y1="7" y2="7"/><rect class="garbage-svg" height="4" id="XMLID_6_" width="6" x="13" y="3"/><g id="XMLID_386_"/></g></svg></td>
                                </tr>
                            <?php }
                            } 
                        }
                    } 

                    //если активирован поиск
                    if(isset($_GET['search'])) {
                        $dataJson = file_get_contents("http://nginx/api/vendors.php?search=" . $_GET['search']);
                        $data = json_decode($dataJson, true);
                        //print_r($data);
                        //отрисовываем список элементов, которые совпадают с поисковым запросом
                        $totalNumElements = 0; //для подсчета общего кол-ва записей
                        if ($data) {
                            for ($i = 0; $i < count($data); $i++) { 
                                //проверка на то, чтобы выводилось не больше строк, чем есть в БД, и что не удален
                                if(isset($data[$i]['id']) && $data[$i]['deleted'] == 0) { 
                                    $totalNumElements++; 
                                    //расфишровка статусов
                                    $status;
                                    if($data[$i]['is_active'] == 1) {
                                        $status = 'Активен';
                                    }
                                    if($data[$i]['is_active'] == 0) {
                                        $status = 'Не активен';
                                    }
                                    ?>
                                    <!-- вносим в атрибуты общее кол-во страниц и текущую страницу для js -->
                                    <tr id="pages-info" role="row" class="list-orders__row" data-pages="<?= $totalPages ?>" data-current-page="<?= $currentPage ?>">
                                        <td><a href="#"><strong><?= $data[$i]['id'] ?></strong></a></td>
                                        <td><a href="javascript: editVendor(<?= $data[$i]['id'] ?>)"><?= $data[$i]['name'] ?></a></td>
                                        <?php
                                        //достаем название города через 2 бд и цикл 
                                        for ($c = 0; $c < count($cities); $c++) {
                                            if ($data[$i]['city_id'] == $cities[$c]['id']) {?>
                                            <td><?= $cities[$c]['name'] ?></td>
                                        <?php } 
                                        }?>
                                        <td><?= $data[$i]['tg_id'] ?></td>
                                        <td><?= $data[$i]['tg_username'] ?></td>
                                        <!-- в отдельном цикле отрисовываем координаты поставщика (пока что с проверкой на их наличие) -->
                                        <?php if ($data[$i]['coordinates']) { ?>
                                            <td class="list-orders_products">
                                                <?= $data[$i]['coordinates']['latitude'] ?>, <?= $data[$i]['coordinates']['longitude'] ?>
                                            </td> 
                                        <?php } else {?>
                                            <td></td> <?php } ?>
                                        <td><?= $data[$i]['phone'] ?> </td>
                                        <td><?= $data[$i]['email'] ?> </td>
                                        <td><?= $data[$i]['comment'] ?></td>
                                        <td><?= $status ?></td>
                                    </tr>
                            <?php }
                            } 
                        }
                        
                    } ?>

                </tr>
            </tbody>

        </table>

        <!-- внизу таблицы выдаем общее количество записей -->
        <div class="info-table">Всего записей: <?= $totalNumElements ?></div>
    </section>

    <!-- если НЕ одна страница и НЕ задан поиск, показываем внизу пагинацию -->
    <?php if($totalPages > 1 && !isset($_GET['search'])) { ?>
    <section class="pagination-wrapper">
        <div class="page-switch">                 
            <a href="?limit=<?= $limit ?>&page=<?= $currentPage - 1; ?>" class="page-switch__prev" <?php if($currentPage <= 1) { ?>  disabled <?php } ?> > 
                <svg  class="fill" viewBox="0 8 23 16" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>down</title> <path d="M11.125 16.313l7.688-7.688 3.594 3.719-11.094 11.063-11.313-11.313 3.5-3.531z"></path> </g></svg>
            </a>
            
            <span class="current-page"><?= $currentPage ?></span>
            <a href="?limit=<?= $limit ?>&page=<?= $currentPage + 1; ?>" class="page-switch__next"  <?php if($currentPage == $totalPages) { ?>  disabled <?php } ?> >
                <svg  class="fill" viewBox="0 8 23 16" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>down</title> <path d="M11.125 16.313l7.688-7.688 3.594 3.719-11.094 11.063-11.313-11.313 3.5-3.531z"></path> </g></svg>
            </a>
        </div>
        <div class="page-status">стр <span class="current-page"><?= $currentPage ?></span> из <span class="total-page"><?= $totalPages ?></span></div>
    </section>
    <?php } ?>

<!-- подключим футер -->
<?php include('./../components/footer.php'); ?>

