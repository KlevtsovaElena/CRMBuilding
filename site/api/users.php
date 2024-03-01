<?php

include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

$db = \DbContext::getConnection();

$tg_id = (int)$_GET['tg_id'];

$result = $db->query("SELECT
                        id,
                        tg_id,
                        step,
                        phone,
                        is_blocked,
                        is_provider,
                        category_id,
                        brand_id,
                        language,
                        city_id
                        FROM customers
                        WHERE tg_id = " . $tg_id . "
                        UNION
                        SELECT
                            id,
                            tg_id,
                            step,
                            phone,
                            is_blocked,
                            is_provider,
                            category_id,
                            brand_id,
                            language,
                            city_id
                        FROM vendors
                        WHERE tg_id = " . $tg_id
                        );

// Извлечение результатов
$row = $result->fetch(PDO::FETCH_ASSOC);

if ($row == false) {
    $row = ['lol'=> 'kek'];
} else {
    $row['is_provider'] = (bool) $row['is_provider'];
}

echo json_encode($row);

