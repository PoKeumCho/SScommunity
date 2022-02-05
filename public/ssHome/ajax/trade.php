<?php
session_start();    // 세션 변수를 사용하여 사용자 아이디를 가져온다.

header('Content-Type: application/json');

require_once __DIR__ . '/' . '../../../private/includes/lib/defineMaxExpel.php';

try {
    /* 필수 파일 불러오기 */
    include_once __DIR__ . '/' . '../../../private/includes/db/DatabaseConnection.php';
    include_once __DIR__ . '/' . '../../../private/classes/DatabaseTable.php';

    /* 데이터베이스 테이블 인스턴스 생성 */
    $userTable = new DatabaseTable($pdo, 'user', 'id');
    $tradeTable = new DatabaseTable($pdo, 'trade', 'id', DatabaseTable::$DATE_FUNC_DATETIME);
    $tradeImgTable = new DatabaseTable($pdo, 'tradeimg', 'id');
    $tradeExpelTable = new DatabaseTable($pdo, 'tradeexpel', 'userid'); 

    $response = [];

    /* AJAX을 사용한 '부적절한 글 신고' 처리 */
    if ($_GET['mode'] == 'expelProcess') {
        $expelTradeInfo = $tradeTable->findById($_GET['tradeId']);

        if ($expelTradeInfo) {
            $expel = $expelTradeInfo['expel'] + 1;

            if ($tradeExpelTable->countAndOption([
                'userid' => $_SESSION['id'],
                'tradeid' => $_GET['tradeId']
            ]) == 0) {  // 신고는 한 번만 가능하다.

                /* 해당 글을 삭제하는 경우 */
                if ($expel >= TRADE_EXPEL_MAX) {
                     
                    // 이미지 삭제
                    $imgList = $tradeImgTable->findAndOptionDistinct([
                        'userid' => $expelTradeInfo['userid'],
                        'id' => $expelTradeInfo['imgid']
                    ], [ 'path' ]);
                    if ($imgList) {
                        $dir = __DIR__ . '/' . '../../../file/images/trade/'; 
                        foreach ($imgList as $img) {
                            if (file_exists($dir . $img['path'])) {
                                unlink($dir . $img['path']);
                            }
                        } 
                        $tradeImgTable->deleteLookup($expelTradeInfo['imgid'], 'userid', $expelTradeInfo['userid']);
                    }

                    // 글 삭제
                    $tradeTable->delete($_GET['tradeId']);

                    // 'expel' 룩업 테이블 삭제
                    $tradeExpelTable->deleteColumn('tradeid', $_GET['tradeId']);
                } else {
                    $tradeExpelTable->insert([
                        'userid' => $_SESSION['id'],
                        'tradeid' => $_GET['tradeId']
                    ]);

                    $tradeTable->update([
                        'primaryKey' => $_GET['tradeId'],
                        'expel' => $expel
                    ]);
                } 
            }
        }
    }

    echo json_encode($response);
}
catch (PDOException $e) {
    $now = new DateTime('NOW');
    $errMsg = $now->format('[Y-n-j g:i:s A] ') 
        . '데이터베이스 오류: ' . $e->getMessage() . ', 위치: ' . $e->getFile() . ' : ' . $e->getLine();  
    error_log ($errMsg, 3, "/var/log/apache2/sscommu/ssHome_trade_error.log");
}
