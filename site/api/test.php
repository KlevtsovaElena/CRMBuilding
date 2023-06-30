<?php
header('Access-Control-Allow-Origin: *');

include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');


if(isset($_GET['sqlText'])) {
    $query = $_GET['sqlText'];
} else {
    echo "параметров нет";
}



$pdo =  \DbContext::getConnection();

        //отправить запрос в БД
         $result = $pdo->query($query);
 
         $row = $result->fetch();

         if(count($row) === 0) {
            $row["COUNT(id)"] = 0;
         }
 
 
         echo $row["COUNT(id)"];


