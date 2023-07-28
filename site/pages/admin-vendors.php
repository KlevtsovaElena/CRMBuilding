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
        $dataJson = file_get_contents("http://nginx/api/vendors/get-with-details.php?deleted=0&city_deleted=0");
        $data = json_decode($dataJson, true); 
        //print_r($data);

        $citiesJson = file_get_contents("http://nginx/api/cities.php?deleted=0");
        $cities = json_decode($citiesJson, true);
        //print_r($cities);

        if(isset($_GET['orderby'])) {
            $orderByArray = explode(";", $_GET['orderby']);
            $orderBy = explode(":", $orderByArray[0]);
            $sortBy = $orderBy[0];
            $mark = $orderBy[1];
            //print_r($sortBy);
            //print_r($mark);
        } else {
            $sortBy = '';
            $_GET['orderby'] = 'status:asc';
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
                <button onclick="applyInVendors()" class="btn btn-ok d-iblock">Применить</button>
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
            <table id="list-orders" data-section="admin-vendors"  data-limit="<?php if (isset($_GET['limit'])) {?><?=$limit?><?php } else { ?><?=$limit?><?php } ?>" <?php if (isset($_GET['page'])) { ?> data-page="<?= $_GET['page'] ?>" <?php } else if (isset($_GET['search'])) { ?> data-search="<?= $_GET['search'] ?>" <?php } ?> >

                <thead>
                    <tr role="row">

                        <!-- <th class="cell-title" data-id="id" data-sort="<?php if ($sortBy == 'id')  {echo $mark; } ?>">№</th> -->
                        <th>№</th>
                        <th class="cell-title" data-id="city_name" data-sort="<?php if ($sortBy == 'city_name')  {echo $mark; } ?>">Город</th>
                        <th class="cell-title" data-id="name" data-sort="<?php if ($sortBy == 'name')  {echo $mark; } ?>">Название</th>
                        <th class="cell-title" data-id="status" data-sort="<?php if ($sortBy == 'status')  {echo $mark; } ?>">Статус</th>
                        <th class="cell-title" data-id="phone" data-sort="<?php if ($sortBy == 'phone')  {echo $mark; } ?>">Телефон</th>
                        <th class="cell-title" data-id="email" data-sort="<?php if ($sortBy == 'email')  {echo $mark; } ?>">Email</th>
                        <th data-id="confirmed">Подтвердил</th>
                        <th data-id="owns">Должен</th>
                        <th data-id="incoming">Поступление</th>
                        <th class="cell-title" data-id="percent" data-sort="<?php if ($sortBy == 'percent')  {echo $mark; } ?>">Процент</th>                      
                        <th></th>
                    </tr>
                </thead>

                <tbody class="list-orders__body">
                <tr role="row" class="list-orders__row">

                <!-- если еще НЕ задан гет-параметр сортировки полей таблицы по одному ключу -->
                <?php if (!isset($_GET['orderby'])) { 
                    
                    //если мы НЕ на первой странице
                    if(isset($_GET['page']) && $_GET['page'] > 1) {
                        //соберём данные для отображения в форме 
                        $dataJson = file_get_contents('http://nginx/api/vendors/get-with-details.php?deleted=0&city_deleted=0&limit=' . $limit . '&offset=' . $offset);
                        $data = json_decode($dataJson, true);
                        $num = $offset + 1; //переменная для отображения порядкового номера (чтобы не было пропусков, т.к. некоторые id "удалены")
                    
                    //если мы на первой странице
                    } elseif(!isset($_GET['page']) || $_GET['page'] == 1) {
                        //и поиск не активирован
                        if (!isset($_GET['search'])) {
                            //соберём данные для отображения в форме 
                            $dataJson = file_get_contents('http://nginx/api/vendors/get-with-details.php?deleted=0&city_deleted=0&limit=' . $limit . '&offset=0');
                            $data = json_decode($dataJson, true);
                            $num = 1; //переменная для отображения порядкового номера (чтобы не было пропусков, т.к. некоторые id "удалены")
                        }
                    }
                    //если активирован поиск
                    if(isset($_GET['search'])) {
                        $dataJson = file_get_contents("http://nginx/api/vendors/get-with-details.php?deleted=0&city_deleted=0&search=" . $_GET['search']);
                        $data = json_decode($dataJson, true);
                        //print_r($data);
                        //отрисовываем список элементов, которые совпадают с поисковым запросом
                        //если по данному поисковому запросу записей нет, записываем в переменную 0
                        if (!$data) {
                            $totalNumElements = 0;
                        //если есть, записываем в переменную их количество
                        } else {
                            $totalEntries = count($data);
                            $num = 1; //переменная для отображения порядкового номера (чтобы не было пропусков, т.к. некоторые id "удалены")
                        }
                    }
                }

                //если уже имеется гет-параметр сортировки полей таблицы по одному ключу
                elseif (isset($_GET['orderby'])) {

                    //если мы НЕ на первой странице
                    if(isset($_GET['page']) && $_GET['page'] > 1) {
                        //соберём данные для отображения в форме 
                        $dataJson = file_get_contents('http://nginx/api/vendors/get-with-details.php?deleted=0&city_deleted=0&offset=' . $offset .'&limit=' . $limit . '&orderby=' . $_GET['orderby']);
                        $data = json_decode($dataJson, true); 
                        //print_r($data);
                        $num = $offset + 1; //переменная для отображения порядкового номера (чтобы не было пропусков, т.к. некоторые id "удалены")

                    //если мы на первой странице
                    } elseif(!isset($_GET['page']) || $_GET['page'] == 1) {
                        //и поиск не активирован
                        if (!isset($_GET['search'])) {
                            //соберём данные для отображения в форме 
                            $dataJson = file_get_contents("http://nginx/api/vendors/get-with-details.php?deleted=0&city_deleted=0&limit=" . $limit . '&offset=0&orderby=' . $_GET['orderby']);
                            $data = json_decode($dataJson, true);
                            $num = 1; //переменная для отображения порядкового номера (чтобы не было пропусков, т.к. некоторые id "удалены")
                        }
                    }

                    //если активирован поиск
                    if(isset($_GET['search'])) {
                        $dataJson = file_get_contents('http://nginx/api/vendors/get-with-details.php?deleted=0&city_deleted=0&limit=all&offset=0&orderby=' . $_GET['orderby'] . '&search=' . $_GET['search']);
                        $data = json_decode($dataJson, true);
                        //print_r($data);
                        //если по данному поисковому запросу записей нет, записываем в переменную 0
                        if (!$data) {
                            $totalNumElements = 0;
                        //если есть, записываем в переменную их количество
                        } else {
                            $totalNumElements = count($data);
                            $num = 1; //переменная для отображения порядкового номера (чтобы не было пропусков, т.к. некоторые id "удалены")
                        }
                    }
                }

                if($data) {
                    //отрисовываем строки в цикле от начальной до конечной цифры оффсетного значения
                        //$num = 1; //переменная для отображения порядкового номера (чтобы не было пропусков, т.к. некоторые id "удалены")
                        for ($i = 0; $i < count($data); $i++) {
                            //проверка на то, чтобы выводилось не больше строк, чем есть в БД
                            if(isset($data[$i]['id'])) {
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
                                <td class="ta-center"><a href="#"><strong><?= $num++; ?></strong></a></td>
                                <td><?= $data[$i]['city_name'] ?></td>
                                <td><a href="javascript: editVendor(<?= $data[$i]['id'] ?>)"><?= $data[$i]['name'] ?></a></td>
                                <td><?= $status ?></td>
                                <td><?= $data[$i]['phone'] ?> </td>
                                <td><?= $data[$i]['email'] ?> </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="ta-center"><?= $data[$i]['percent'] ?>%</td>
                                <td><svg onclick="deleteOne('vendors', <?=$data[$i]['id'] ?>)" class="garbage" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20px" height="20px" viewBox="0 0 32 32" id="icons" version="1.0" xml:space="preserve" fill="#000000"><g id="SVGRepo_iconCarrier"><rect class="garbage-svg" height="22" id="XMLID_14_" width="16" x="8" y="7"/><line class="garbage-svg" id="XMLID_4_" x1="16" x2="16" y1="10" y2="26"/><line class="garbage-svg" id="XMLID_118_" x1="20" x2="20" y1="10" y2="26"/><line class="garbage-svg" id="XMLID_3_" x1="12" x2="12" y1="10" y2="26"/><line class="garbage-svg" id="XMLID_5_" x1="5" x2="27" y1="7" y2="7"/><rect class="garbage-svg" height="4" id="XMLID_6_" width="6" x="13" y="3"/><g id="XMLID_386_"/></g></svg></td>
                            </tr>
                        <?php }
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
            <a href="?limit=<?= $limit ?>&page=<?= $currentPage - 1; ?><?php if(isset($_GET['orderby'])) {?>&orderby=<?= $_GET['orderby'] ?><?php } ?>" class="page-switch__prev" <?php if($currentPage <= 1) { ?>  disabled <?php } ?> > 
                <svg  class="fill" viewBox="0 8 23 16" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>down</title> <path d="M11.125 16.313l7.688-7.688 3.594 3.719-11.094 11.063-11.313-11.313 3.5-3.531z"></path> </g></svg>
            </a>
            
            <span class="current-page"><?= $currentPage ?></span>
            <a href="?limit=<?= $limit ?>&page=<?= $currentPage + 1; ?><?php if(isset($_GET['orderby'])) {?>&orderby=<?= $_GET['orderby'] ?><?php } ?>" class="page-switch__next"  <?php if($currentPage == $totalPages) { ?>  disabled <?php } ?> >
                <svg  class="fill" viewBox="0 8 23 16" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>down</title> <path d="M11.125 16.313l7.688-7.688 3.594 3.719-11.094 11.063-11.313-11.313 3.5-3.531z"></path> </g></svg>
            </a>
        </div>
        <div class="page-status">стр <span class="current-page"><?= $currentPage ?></span> из <span class="total-page"><?= $totalPages ?></span></div>
    </section>
    <?php } ?>

<!-- подключим футер -->
<?php include('./../components/footer.php'); ?>

