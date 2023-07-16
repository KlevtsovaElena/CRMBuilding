<?php 
// проверка куки
// 1. если есть, но пустой - удаляем куки и переадресация на форму
// 2. если куки есть и не пустой - отправляем на сервер для сравнения
// 3. если вообще нет - переадресация на форму входа
if(isset($_COOKIE['profile'])) {
    if(trim($_COOKIE['profile']) == '' ) {
        // 1.
        setcookie('profile', '', time() - 3600);
        header('Location: pages/login.php');
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

        if (!$response || ($response['success'] == false)) {
            setcookie('profile', '', time() - 3600);
            header('Location: pages/login.php');
            exit(0);
        } else if ($response['success'] == true) {
            $profile = $response['profile'];
            $vendor_id = $profile['id'];
            $vendor_name = $profile['name'];
            $role = $profile['role'];
        }
    }
} else {
    // 3.
    setcookie('profile', '', time() - 3600);
    header('Location: pages/login.php');
    exit(0);
}
?>