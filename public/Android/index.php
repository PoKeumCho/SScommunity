<?php
header('Content-Type: application/json');

// URL을 이용해 action(액션)을 결정하는 route(라우터)값을 설정한다.
$route = str_replace('/public/Android/', '', strtok($_SERVER['REQUEST_URI'], '?'));

// REST 방식으로 경로를 제어한다.
include_once __DIR__ . '/' . '../../private/includes/REST/android.php';

try {
    /* 필수 파일 불러오기 */
    include_once __DIR__ . '/' . '../../private/includes/db/DatabaseConnection.php';
    include_once __DIR__ . '/' . '../../private/classes/DatabaseTable.php';
    include_once __DIR__ . '/' . '../../private/controllers/AndroidController.php';

    /* 데이터베이스 테이블 인스턴스 생성 */
    $userTable = new DatabaseTable($pdo, 'user', 'id');
    $generalCategoryTable = new DatabaseTable($pdo, 'generalcategory', 'id');
    $generalCategoryBookmarkTable = new DatabaseTable($pdo, 'generalcategorybookmark', 'userid');
    $generalCategoryExpelTable = new DatabaseTable($pdo, 'generalcategoryexpel', 'userid');
    $generalTable = new DatabaseTable($pdo, 'general', 'id', DatabaseTable::$DATE_FUNC_DATETIME);
    $generalLikesTable = new DatabaseTable($pdo, 'generallikes', 'userid');
    $generalDislikesTable = new DatabaseTable($pdo, 'generaldislikes', 'userid');
    $generalExpelTable = new DatabaseTable($pdo, 'generalexpel', 'userid');
    $generalImgTable = new DatabaseTable($pdo, 'generalimg', 'generalid');
    $generalCommentsTable = new DatabaseTable($pdo, 'generalcomments', 'id', DatabaseTable::$DATE_FUNC_DATETIME);
    $generalCommentsLikesTable = new DatabaseTable($pdo, 'generalcommentslikes', 'userid');
    $generalCommentsDislikesTable = new DatabaseTable($pdo, 'generalcommentsdislikes', 'userid'); 
    $generalCommentsExpelTable = new DatabaseTable($pdo, 'generalcommentsexpel', 'userid');
    $scheduleTable = new DatabaseTable($pdo, 'scheduletbl', 'no');
    $scheduleLookupTable = new DatabaseTable($pdo, 'schedulelookuptbl', 'userid');
    $userScheduleTable = new DatabaseTable($pdo, 'userscheduletbl', 'userid');
    $tradeCategoryTable = new DatabaseTable($pdo, 'tradecategory', 'id');
    $tradeTable = new DatabaseTable($pdo, 'trade', 'id', DatabaseTable::$DATE_FUNC_DATETIME);
    $tradeImgTable = new DatabaseTable($pdo, 'tradeimg', 'id'); 
    $tradeExpelTable = new DatabaseTable($pdo, 'tradeexpel', 'userid'); 
    $chatTable = new DatabaseTable($pdo, 'chat', 'no', DatabaseTable::$DATE_FUNC_DATETIME);
    $chatBlockTable = new DatabaseTable($pdo, 'chatblock', 'userid');
    $chatTextTable = new DatabaseTable($pdo, 'chattext', 'no');
    $chatFileTable = new DatabaseTable($pdo, 'chatfile', 'no');

    $androidController = new AndroidController(
        $userTable, 
        $generalCategoryTable, 
        $generalCategoryBookmarkTable,
        $generalCategoryExpelTable,
        $generalTable, 
        $generalLikesTable, 
        $generalDislikesTable, 
        $generalExpelTable,
        $generalImgTable, 
        $generalCommentsTable, 
        $generalCommentsLikesTable, 
        $generalCommentsDislikesTable, 
        $generalCommentsExpelTable,
        $scheduleTable,
        $scheduleLookupTable, 
        $userScheduleTable,
        $tradeCategoryTable, 
        $tradeTable, 
        $tradeImgTable, 
        $tradeExpelTable,
        $chatTable, 
        $chatBlockTable, 
        $chatTextTable, 
        $chatFileTable);

    $method = $_SERVER['REQUEST_METHOD'];
    $action = $ROUTES[$route][$method]['action'];
    $response = $androidController->$action();

    echo json_encode($response);
}
catch (PDOException $e) {
    $now = new DateTime('NOW');
    $errMsg = $now->format('[Y-n-j g:i:s A] ') 
        . '데이터베이스 오류: ' . $e->getMessage() . ', 위치: ' . $e->getFile() . ' : ' . $e->getLine();  
    error_log ($errMsg, 3, "/var/log/apache2/sscommu/Android_error.log");
}
