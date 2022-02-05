<?php
session_start();    // 세션 변수를 사용하여 사용자 아이디를 가져온다.

header('Content-Type: application/json');

try {
    /* 필수 파일 불러오기 */
    include_once __DIR__ . '/' . '../../../private/includes/db/DatabaseConnection.php';
    include_once __DIR__ . '/' . '../../../private/classes/DatabaseTable.php';

    /* 데이터베이스 테이블 인스턴스 생성 */
    $userTable = new DatabaseTable($pdo, 'user', 'id');
    $chatTable = new DatabaseTable($pdo, 'chat', 'no', DatabaseTable::$DATE_FUNC_DATETIME);
    $chatBlockTable = new DatabaseTable($pdo, 'chatblock', 'userid');
    $chatTextTable = new DatabaseTable($pdo, 'chattext', 'no');
    $chatFileTable = new DatabaseTable($pdo, 'chatfile', 'no');

    $response = [];

    /* 채팅을 보내는 경우 동작 */
    if ($_POST['mode'] == 'sendMessage') {

        // 발신자와 수신자 간의 채팅 구분 번호 (contentNo)
        $contentNo = $chatTable->countAndOption([
            'senderid' => $_SESSION['id'],
            'receiverid' => $_POST['id']
        ]); 

        // 메시지가 존재하는 경우
        if (trim($_POST['message'])) {

            $dt = new DateTime();

            $chatTextTable->insert([
                'no' => $contentNo,
                'senderid' => $_SESSION['id'],
                'receiverid' => $_POST['id'],
                'text' => $_POST['message']
            ]);

            $chatTable->insert([
                'senderid' => $_SESSION['id'],
                'receiverid' => $_POST['id'],
                'datetime' => $dt,
                'contenttype' => 'T',
                'contentno' => $contentNo++,
                'readstatus' => 'N'
            ]);

            $response[] = array(
                'type' => 'T',
                'text' => $_POST['message'],
                'time' => $dt,
            );
        }  

        // 이미지 파일이 존재하는 경우
        if ($_POST['imgCount'] > 0) {
            for ($i = 0; $i < $_POST['imgCount']; $i++) {

                $dt = new DateTime();

                $chatFileTable->insert([
                    'no' => $contentNo,
                    'senderid' => $_SESSION['id'],
                    'receiverid' => $_POST['id'],
                    'path' => $_POST['img_path_' . $i],
                    'width' => $_POST['img_width_' . $i]
                ]);

                $chatTable->insert([
                    'senderid' => $_SESSION['id'],
                    'receiverid' => $_POST['id'],
                    'datetime' => $dt,
                    'contenttype' => 'F',
                    'contentno' => $contentNo++,
                    'readstatus' => 'N'
                ]);

                $response[] = array(
                    'type' => 'F',
                    'path' => $_POST['img_path_' . $i],
                    'time' => $dt,
                );
            }
        }
    }
    /* 새 메세지 알림 창 클릭 시 동작 */ 
    else if ($_POST['mode'] == 'receivedNewMessage') {
        // 읽음 상태로 변경한다.
        $chatTable->updateAndOption([
            'senderid' => $_POST['id'],
            'receiverid' => $_SESSION['id']
        ], [
            'readstatus' => 'Y'
        ]);  
    }
    /* 일정한 간격으로 수신된 채팅 확인 */
    else if ($_GET['mode'] == 'checkNewMessage') {

        /**
         *  새로운 메시지를 보낸 아이디 목록 추출 
         */
        $newMessageSenderList_tmp = $chatTable->findAndOptionDistinct([
            'receiverid' => $_SESSION['id'],
            'readstatus' => 'N'
        ], [ 'senderid' ]);

        $newMessageSenderList = [];
        foreach ($newMessageSenderList_tmp as $sender) {
            $newMessageSenderList[] = $sender['senderid'];
        }

        // 차단한 아이디가 포함된 경우 제거한다.
        $blockList = [];
        $chatBlockList = $chatBlockTable->find('userid', $_SESSION['id']); 
        foreach ($chatBlockList as $key => $value) {
            $blockList[] = $value['blockid'];
        }
        $newMessageSenderList = arrayInOrder(array_diff($newMessageSenderList, $blockList));

        $response['senderList'] = $newMessageSenderList;


        /**
         *  현재 채팅 상대가 존재하는 경우 새로 도착한 메시지를 확인한다.
         */
        if (isset($_GET['id'])) {
            $newMessageList = $chatTable->getChatList($_SESSION['id'], $_GET['id'], false);

            // 읽음 상태로 변경한다.
            foreach ($newMessageList as $newMessage) {
                $chatTable->update([
                    'primaryKey' => $newMessage['no'],
                    'readstatus' => 'Y'
                ]);
            }

            $response['messageList'] = $newMessageList; 
        }
    }

    echo json_encode($response);
}
catch (PDOException $e) {
    $now = new DateTime('NOW');
    $errMsg = $now->format('[Y-n-j g:i:s A] ') 
        . '데이터베이스 오류: ' . $e->getMessage() . ', 위치: ' . $e->getFile() . ' : ' . $e->getLine();  
    error_log ($errMsg, 3, "/var/log/apache2/sscommu/ssHome_chat_error.log");
}

// 배열을 순차적으로 정리한다.
function arrayInOrder(array $arr) {
    $temp = [];
    foreach ($arr as $key => $value) {
        $temp[] = $value;
    }
    return $temp;
}
