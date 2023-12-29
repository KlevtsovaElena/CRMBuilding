<?php require('../handler/check-profile.php'); 
if($role !== 1) {
    setcookie('profile', '', -1, '/');
    header('Location: ' . $mainUrl . '/pages/login.php');
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
        $dataJson = file_get_contents($nginxUrl . "/api/customers/get-with-details.php");
        $data = json_decode($dataJson, true); 
        //print_r($data);

        // $citiesJson = file_get_contents($nginxUrl . "/api/cities.php?deleted=0");
        // $cities = json_decode($citiesJson, true);
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
        }
    ?>

        <!-- далее отрисовываем всю страницу -->
        <p class="page-title">Клиенты</p>

         <section class="form-filters">
            <div class="form-elements-container filters-container-flex">
                
                <!-- фильтрация по городу -->
                <!-- <div class="d-iblock">
                    <div>Город</div> 
                    <select id="city" name="city" value="" required onchange="cityChanged(this)">
                    <?php
                        //запрашиваем данные по городам из БД
                        $dataJsonV = file_get_contents($nginxUrl . '/api/cities.php?is_active=1&deleted=0');
                        $dataV = json_decode($dataJsonV, true);

                        //если город не был задан, устанавливаем в селекте выбранное значение "все"
                        if (!isset($_GET['city_id'])) {
                            
                        ?>
                            <option value="" <?= 'selected' ?> >все</option>
                            <?php for ($v = 0; $v < count($dataV); $v++) { ?>
                                <option value="<?= $dataV[$v]['id'] ?>" ><?= $dataV[$v]['name'] ?></option>
                            <?php }
                        //если город уже задан в гет-параметрах, выводим его
                        } else {
                            $city = $_GET['city_id']; 
                            ?>

                            <option value="" <?php if($city == 'all') { echo 'selected'; } ?> >все</option>
                            <?php 
                            for ($v = 0; $v < count($dataV); $v++) { 
                                if($city == $dataV[$v]['id']) {?>
                                
                                <option value="<?= $dataV[$v]['id'] ?>" <?= 'selected' ?> ><?= $dataV[$v]['name'] ?></option>
                            <?php 
                                } else { ?>
                                    
                                <option value="<?= $dataV[$v]['id'] ?>" ><?= $dataV[$v]['name'] ?></option>
                            <?php 
                                }
                            ?>
                        <?php 
                            }
                        } ?> 
                    </select>
                </div> -->

                <!-- фильтрация по поставщику -->
                <!-- <div class="d-iblock">
                    <div>Поставщик</div> 
                    <select id="vendor" name="vendor" value="" required>
                    <?php
                    
                        $dataV = [];
                        //если в гет-параметре еще не задан город
                        if (!isset($_GET['city_id'])) {
    
                            //запрашиваем данные по поставщикам из БД
                            $dataJsonV = file_get_contents($nginxUrl . '/api/vendors/get-with-details.php?deleted=0&city_deleted=0');
                            $dataV = json_decode($dataJsonV, true);
    
                        } else {
                            //запрашиваем данные по поставщикам из БД с учетом города
                            $dataJsonV = file_get_contents($nginxUrl . '/api/vendors/get-with-details.php?deleted=0&city_deleted=0&city_id=' . $_GET['city_id']);
                            $dataV = json_decode($dataJsonV, true);
                        }

                        //если поставщик не был задан, устанавливаем в селекте выбранное значение "все"
                        if (!isset($_GET['vendor_id'])) {
                            
                        ?>
                            <option value="" <?= 'selected' ?> >все</option>
                            <?php for ($v = 0; $v < count($dataV); $v++) { ?>
                                <option value="<?= $dataV[$v]['id'] ?>"><?= $dataV[$v]['name'] ?></option>
                            <?php }
                        //если поставщик уже задан в гет-параметрах, выводим его
                        } else {
                            $vendor = $_GET['vendor_id']; 
                            ?>

                            <option value="" <?php if($vendor == 'all') { echo 'selected'; } ?> >все</option>
                            <?php 
                            for ($v = 0; $v < count($dataV); $v++) { 
                                if($vendor == $dataV[$v]['id']) {?>
                                
                                <option value="<?= $dataV[$v]['id'] ?>" <?= 'selected' ?>><?= $dataV[$v]['name'] ?></option>
                            <?php 
                                } else { ?>
                                    
                                <option value="<?= $dataV[$v]['id'] ?>"><?= $dataV[$v]['name'] ?></option>
                            <?php 
                                }
                            ?>
                        <?php 
                            }
                        } ?> 
                    </select>
                </div> -->

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
                <input type="search" id="search" name="search" value="<?php if (isset($_GET['search']) && $_GET['search']) { $word = explode(':', $_GET['search']); echo $word[1];} ?>" placeholder="Поиск но названию">
                <!-- кнопка, активирующая выбранный лимит записей на странице и поиск -->
                <button onclick="applyInCustomers('admin-customers')" class="btn btn-ok d-iblock">Применить</button>
                <button id="btn-cancel-filters" class="btn btn-neutral border-neutral d-iblock">Сбросить</button>
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
        $totalNumElements = count($data); 

        //собираем в отдельную переменную фильтры
        $params = '';

        //если заданы гет-параметры поставщика, собираем в переменную
        if (isset($_GET['vendor_id'])) {
            $params = $params . '&vendor_id=' . $_GET['vendor_id'];
        }

        //если заданы гет-параметры города, собираем в переменную
        if (isset($_GET['city_id'])) {
            $params = $params . '&city_id=' . $_GET['city_id'];
        }
        ?>

        <!-- таблица клиентов -->
        <section class="orders">
            <table id="list-orders" data-section="admin-customers"  data-limit="<?php if (isset($_GET['limit'])) {?><?=$limit?><?php } else { ?><?=$limit?><?php } ?>" <?php if (isset($_GET['page'])) { ?> data-page="<?= $_GET['page'] ?>" <?php } else if (isset($_GET['search'])) { ?> data-search="<?= $_GET['search'] ?>" <?php } ?> data-city-select="<?php if (isset($_GET['city_id'])) { echo $_GET['city_id']; } ?>">

                <thead>
                    <tr role="row">

                        <!-- <th class="cell-title" data-id="id" data-sort="<?php if ($sortBy == 'id')  {echo $mark; } ?>">№</th> -->
                        <th class="ta-center">№</th>
                        <!-- <th class="cell-title" data-id="city_name" data-sort="<?php if ($sortBy == 'city_name')  {echo $mark; } ?>">Город</th> -->
                        <!-- <th class="cell-title" data-id="vendor_name" data-sort="<?php if ($sortBy == 'vendor_name')  {echo $mark; } ?>">Поставщик</th> -->
                        <th class="cell-title" data-id="first_name" data-sort="<?php if ($sortBy == 'first_name')  {echo $mark; } ?>">Имя клиента</th>
                        <th class="cell-title" data-id="phone" data-sort="<?php if ($sortBy == 'phone')  {echo $mark; } ?>">Телефон</th>
                        <th data-id="confirmed">Заблокирован</th>                    
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
                        $dataJson = file_get_contents($nginxUrl . '/api/customers/get-with-details.php?limit=' . $limit . '&offset=' . $offset . $params);
                        $data = json_decode($dataJson, true);
                        $num = $offset + 1; //переменная для отображения порядкового номера (чтобы не было пропусков, т.к. некоторые id "удалены")

                        //отдельно соберем информацию об общем кол-ве записей
                        $dataJsonN = file_get_contents($nginxUrl . '/api/customers/get-with-details.php?limit=0'. $params);
                        $dataN = json_decode($dataJsonN, true);
                        $totalNumElements = count($dataN);
                        //и об общем кол-ве страниц
                        $totalPages = ceil(count($dataN) / $limit);
                    
                    //если мы на первой странице
                    } elseif(!isset($_GET['page']) || $_GET['page'] == 1) {
                        //и поиск не активирован
                        if (!isset($_GET['search'])) {
                            //соберём данные для отображения в форме 
                            $dataJson = file_get_contents($nginxUrl . '/api/customers/get-with-details.php?limit=' . $limit .  $params);
                            $data = json_decode($dataJson, true);
                            $num = 1; //переменная для отображения порядкового номера (чтобы не было пропусков, т.к. некоторые id "удалены")
                            //print_r($data);
                            
                            //отдельно соберем информацию об общем кол-ве записей
                            $dataJsonN = file_get_contents($nginxUrl . '/api/customers/get-with-details.php?limit=0'. $params);
                            $dataN = json_decode($dataJsonN, true);
                            $totalNumElements = count($dataN);
                            //и об общем кол-ве страниц
                            $totalPages = ceil(count($dataN) / $limit);
                        }
                    }
                    //если активирован поиск
                    if(isset($_GET['search'])) {
                        $dataJson = file_get_contents($nginxUrl . "/api/customers/get-with-details.php?search=" . $_GET['search'] . $params);
                        $data = json_decode($dataJson, true);
                        //print_r($data);
                        //отрисовываем список элементов, которые совпадают с поисковым запросом
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

                //если уже имеется гет-параметр сортировки полей таблицы по одному ключу
                elseif (isset($_GET['orderby'])) {

                    //если мы НЕ на первой странице
                    if(isset($_GET['page']) && $_GET['page'] > 1) {
                        //соберём данные для отображения в форме 
                        $dataJson = file_get_contents($nginxUrl . '/api/customers/get-with-details.php?offset=' . $offset .'&limit=' . $limit . '&orderby=' . $_GET['orderby'] . $params);
                        $data = json_decode($dataJson, true); 
                        //print_r($data);
                        $num = $offset + 1; //переменная для отображения порядкового номера (чтобы не было пропусков, т.к. некоторые id "удалены")

                        //отдельно соберем информацию об общем кол-ве записей
                        $dataJsonN = file_get_contents($nginxUrl . '/api/customers/get-with-details.php?orderby=' . $_GET['orderby'] . $params);
                        $dataN = json_decode($dataJsonN, true);
                        $totalNumElements = count($dataN);
                        //и об общем кол-ве страниц
                        $totalPages = ceil(count($dataN) / $limit);

                    //если мы на первой странице
                    } elseif(!isset($_GET['page']) || $_GET['page'] == 1) {
                        //и поиск не активирован
                        if (!isset($_GET['search'])) {
                            //соберём данные для отображения в форме 
                            $dataJson = file_get_contents($nginxUrl . "/api/customers/get-with-details.php?limit=" . $limit . '&offset=0&orderby=' . $_GET['orderby'] . $params);
                            $data = json_decode($dataJson, true);
                            $num = 1; //переменная для отображения порядкового номера (чтобы не было пропусков, т.к. некоторые id "удалены")

                            //отдельно соберем информацию об общем кол-ве записей
                            $dataJsonN = file_get_contents($nginxUrl . "/api/customers/get-with-details.php?orderby=" . $_GET['orderby'] . $params);
                            $dataN = json_decode($dataJsonN, true);
                            $totalNumElements = count($dataN);
                            //и об общем кол-ве страниц
                            $totalPages = ceil(count($dataN) / $limit);
                        }
                    }

                    //если активирован поиск
                    if(isset($_GET['search'])) {
                        $dataJson = file_get_contents($nginxUrl . '/api/customers/get-with-details.php?orderby=' . $_GET['orderby'] . '&search=' . $_GET['search'] . $params);
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
                                ?>
                            <!-- вносим в атрибуты общее кол-во страниц и текущую страницу для js -->
                            <tr id="pages-info" role="row" class="list-orders__row" data-pages="<?= $totalPages ?>" data-current-page="<?= $currentPage ?>">
                                <td class="ta-center"><strong><?= $num++; ?></strong></td>
                                <!-- <td><?= $data[$i]['city_name'] ?></td> -->
                                <!-- <td><a href="#"><?= $data[$i]['vendor_name'] ?></a></td> -->
                                <td><?= $data[$i]['first_name'] ?> <?php if ($data[$i]['last_name']) {echo ' ' . $data[$i]['last_name']; } ?></td>
                                <td>
                                    <a href="tel:+<?= $data[$i]['phone'] ?>" >
                                        <?php
                                            if( $data[$i]['phone']) {echo '+' . $data[$i]['phone'];}
                                        ?>
                                    </a> 
                                </td>
                                <td class="checkbox-cell">
                                    <!-- <label> -->
                                        <input type="checkbox" onclick="checkboxCustomerBlocked(<?= $data[$i]['id'] ?>)" <?php if ($data[$i]["is_blocked"] == 1) {?> checked <?php } ?>>
                                        <!-- Заблокирован
                                    </label> -->
                                </td>
                                <!-- <td><svg onclick="deleteOne('admin-vendors', <?=$data[$i]['id'] ?>)" class="garbage" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20px" height="20px" viewBox="0 0 32 32" id="icons" version="1.0" xml:space="preserve" fill="#000000"><g id="SVGRepo_iconCarrier"><rect class="garbage-svg" height="22" id="XMLID_14_" width="16" x="8" y="7"/><line class="garbage-svg" id="XMLID_4_" x1="16" x2="16" y1="10" y2="26"/><line class="garbage-svg" id="XMLID_118_" x1="20" x2="20" y1="10" y2="26"/><line class="garbage-svg" id="XMLID_3_" x1="12" x2="12" y1="10" y2="26"/><line class="garbage-svg" id="XMLID_5_" x1="5" x2="27" y1="7" y2="7"/><rect class="garbage-svg" height="4" id="XMLID_6_" width="6" x="13" y="3"/><g id="XMLID_386_"/></g></svg></td> -->
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
            <a <?php if($currentPage > 1) { ?> href="?limit=<?= $limit ?>&page=<?= $currentPage - 1; ?><?php if(isset($_GET['orderby'])) {?>&orderby=<?= $_GET['orderby'] ?><?php } ?><?= $params ?>"<?php } ?> class="page-switch__prev" <?php if($currentPage <= 1) { ?>  disabled <?php } ?> > 
                <svg  class="fill" viewBox="0 8 23 16" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>down</title> <path d="M11.125 16.313l7.688-7.688 3.594 3.719-11.094 11.063-11.313-11.313 3.5-3.531z"></path> </g></svg>
            </a>
            
            <span class="current-page"><?= $currentPage ?></span>
            <a <?php if($currentPage != $totalPages) { ?> href="?limit=<?= $limit ?>&page=<?= $currentPage + 1; ?><?php if(isset($_GET['orderby'])) {?>&orderby=<?= $_GET['orderby'] ?><?php } ?>?><?= $params ?>"<?php } ?> class="page-switch__next"  <?php if($currentPage == $totalPages) { ?>  disabled <?php } ?> >
                <svg  class="fill" viewBox="0 8 23 16" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>down</title> <path d="M11.125 16.313l7.688-7.688 3.594 3.719-11.094 11.063-11.313-11.313 3.5-3.531z"></path> </g></svg>
            </a>
        </div>
        <div class="page-status">стр <span class="current-page"><?= $currentPage ?></span> из <span class="total-page"><?= $totalPages ?></span></div>
    </section>
    <?php } ?>

<!-- подключим футер -->
<?php include('./../components/footer.php'); ?>

