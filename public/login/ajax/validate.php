<?php
header('Content-Type: application/json');

try {
    /* 필수 파일 불러오기 */
    include_once __DIR__ . '/' . '../../../private/includes/db/DatabaseConnection.php';
    include_once __DIR__ . '/' . '../../../private/classes/DatabaseTable.php';

    /* 데이터베이스 테이블 인스턴스 생성 */
    $tmpUserTable = new DatabaseTable($pdo, 'tmpuser', 'email');
    $userTable = new DatabaseTable($pdo, 'user', 'id');
    $withdrawalTable = new DatabaseTable($pdo, 'withdrawal', 'userid');

    /* 아이디 중복 확인 */
    if ($_GET['mode'] == 'id') {
        if (count($userTable->find('id', $_GET['value'])) > 0 
                || count($tmpUserTable->find('id', $_GET['value'])) > 0) {   // 중복된 아이디가 존재하는 경우
            $response = array(
                'mode' => $_GET['mode'],
                'result' => false,
                'code' => 1
            );
        } else if (count($withdrawalTable->find('userid', $_GET['value'])) > 0) {  // 회원탈퇴한 사용자의 아이디로 존재하는 경우
            $response = array(
                'mode' => $_GET['mode'],
                'result' => false,
                'code' => 2
            );
        } else {
            $response = array(
                'mode' => $_GET['mode'],
                'result' => true
            );
        }
    } 
    /* 학번, 이메일 중복 확인 */
    else if ($_GET['mode'] == 'studentid' || $_GET['mode'] == 'email') {
        if (count($userTable->find($_GET['mode'], $_GET['value'])) > 0) { // 중복된 학번 또는 이메일이 존재하는 경우
            $response = array(
                'mode' => $_GET['mode'],
                'result' => false,
                'code' => 1
            );
        } else if (count($tmpUserTable->find($_GET['mode'], $_GET['value'])) > 0) {   // 본인 인증 진행 중인 학번 또는 이메일인 경우
            $response = array(
                'mode' => $_GET['mode'],
                'result' => false,
                'code' => 2,
                'email' => $tmpUserTable->find($_GET['mode'], $_GET['value'])[0]['email']
            );
        } else {
            $response = array(
                'mode' => $_GET['mode'],
                'result' => true
            );
        }
    } 

    echo json_encode($response);
}
catch (PDOException $e) {
    $now = new DateTime('NOW');
    $errMsg = $now->format('[Y-n-j g:i:s A] ') 
        . '데이터베이스 오류: ' . $e->getMessage() . ', 위치: ' . $e->getFile() . ' : ' . $e->getLine();  
    error_log ($errMsg, 3, "/var/log/apache2/sscommu/login_idValidate_error.log");
}
