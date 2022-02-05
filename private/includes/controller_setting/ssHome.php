<?php

/* 데이터베이스 테이블 인스턴스 생성 */
$userTable = new DatabaseTable($pdo, 'user', 'id');
$withdrawalTable = new DatabaseTable($pdo, 'withdrawal', 'userid');
$accountImgTable = new DatabaseTable($pdo,  'accountimg', 'id');
$generalTable = new DatabaseTable($pdo, 'general', 'id', DatabaseTable::$DATE_FUNC_DATETIME);
$generalCategoryTable = new DatabaseTable($pdo, 'generalcategory', 'id');
$generalCategoryBookmarkTable = new DatabaseTable($pdo, 'generalcategorybookmark', 'userid');
$generalCategoryExpelTable = new DatabaseTable($pdo, 'generalcategoryexpel', 'userid');
$generalLikesTable = new DatabaseTable($pdo, 'generallikes', 'userid');
$generalDislikesTable = new DatabaseTable($pdo, 'generaldislikes', 'userid');
$generalExpelTable = new DatabaseTable($pdo, 'generalexpel', 'userid');
$generalImgTable = new DatabaseTable($pdo, 'generalimg', 'generalid');
$generalCommentsTable = new DatabaseTable($pdo, 'generalcomments', 'id', DatabaseTable::$DATE_FUNC_DATETIME);
$generalCommentsLikesTable = new DatabaseTable($pdo, 'generalcommentslikes', 'userid');
$generalCommentsDislikesTable = new DatabaseTable($pdo, 'generalcommentsdislikes', 'userid'); 
$generalCommentsExpelTable = new DatabaseTable($pdo, 'generalcommentsexpel', 'userid');
$scheduleLookupTable = new DatabaseTable($pdo, 'schedulelookuptbl', 'userid');
$userScheduleTable = new DatabaseTable($pdo, 'userscheduletbl', 'userid');
$tradeCategoryTable = new DatabaseTable($pdo, 'tradecategory', 'id');
$tradeTable = new DatabaseTable($pdo, 'trade', 'id', DatabaseTable::$DATE_FUNC_DATETIME);
$tradeImgTable = new DatabaseTable($pdo, 'tradeimg', 'id'); 
$tradeExpelTable = new DatabaseTable($pdo, 'tradeexpel', 'userid'); 

/* 로그인 기능 구현 인스턴스 생성 */
$authentication = new Authentication($userTable, 'id', 'pw'); 

/*
 * 페이지 기능을 수행하고 
 * $addStyle, $addScript, $title, $aside, $section 변수 생성
 */
$ssHomeController = new SsHomeController(
    $CSS_LOCATION, $JS_LOCATION, 
    $userTable, $withdrawalTable, $accountImgTable, 
    $generalTable, $generalCategoryTable, 
    $generalCategoryBookmarkTable, $generalCategoryExpelTable,
    $generalLikesTable, $generalDislikesTable, $generalExpelTable,
    $generalImgTable, $generalCommentsTable,
    $generalCommentsLikesTable, $generalCommentsDislikesTable, $generalCommentsExpelTable,
    $scheduleLookupTable, $userScheduleTable,
    $tradeCategoryTable, $tradeTable, $tradeImgTable, $tradeExpelTable,
    $authentication
);

?>
