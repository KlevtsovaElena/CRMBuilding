<?php 

// проверка куки
// 1. если есть, но пустой - удаляем куки и переадресация на форму
// 2. если куки есть и не пустой - отправляем на сервер для сравнения
// 3. если вообще нет - переадресация на форму входа
if(isset($_COOKIE['profile'])) {
    if(trim($_COOKIE['profile']) == '' ) {
        // 1.
        $return_url =  ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        setcookie('profile', '', -1, '/');
        header('Location: http://localhost/pages/login.php?return_url=' . $return_url);
        exit(0);
    } else {
        // 2.
        $content = "cookie=" . $_COOKIE['profile'];
        $aHttp = array(
            'http' => array (
                'method' => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $content
            )
        );
        $context = stream_context_create($aHttp);

        $responseJson = file_get_contents('http://nginx/api/authorization/check.php', false, $context);
        $response = json_decode($responseJson, true);

        // смотрим, что вернул сервер
        // если данные проверку не прошли, то 
        if (!$response || ($response['success'] == false)) {
            $return_url =  ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            setcookie('profile', '', -1, '/');
            header('Location: http://localhost/pages/login.php?return_url=' . $return_url);
            exit(0);
        // если данные проверку прошли, то 
        } else if ($response['success'] == true) {
            $profile = $response['profile'];
            $vendor_id = $profile['id'];
            $vendor_name = $profile['name'];
            $role = $profile['role'];
            $vendor_tg_id = $profile['tg_id'];
            $vendor_hash_string = $profile['hash_string'];
        }
    }
} else {
    // 3.
    $return_url =  ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    setcookie('profile', '', -1, '/');
    header('Location: http://localhost/pages/login.php?return_url=' . $return_url);
    exit(0);
}
?>