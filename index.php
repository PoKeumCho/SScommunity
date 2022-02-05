<?php    

$isServerUnderMaintenance = false;
//$isServerUnderMaintenance = true;

if ($isServerUnderMaintenance) {
    
    include __DIR__ . "/private/templates/maintenance/layout.html.php";
}
else {

    // 모바일에서 접속한 경우 처리
    $mobileKeyWords = array('iPhone', 'iPod', 'BlackBerry', 'Android',     
                        'Windows CE', 'LG', 'MOT', 'SAMSUNG', 'SonyEricsson');    
    for ($i = 0; $i < count($mobileKeyWords); $i++) {    
        if (strpos($_SERVER['HTTP_USER_AGENT'], $mobileKeyWords[$i]) == true) {    
            // 모바일 전용 페이지로 자동 이동한다.
            header('Location: ./public/Mobile/');
            exit;    
        }    
    }

    header('Location: ./public/ssHome/');
}
?>
