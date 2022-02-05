<?php
session_start();

try {
    /* 필수 파일 불러오기 */
    include_once __DIR__ . '/' . '../../private/includes/db/DatabaseConnection.php';
    include_once __DIR__ . '/' . '../../private/classes/DatabaseTable.php';
    include_once __DIR__ . '/' . '../../private/classes/Authentication.php';

    /* 데이터베이스 테이블 인스턴스 생성 */
    $userTable = new DatabaseTable($pdo, 'user', 'id');
    $chatTable = new DatabaseTable($pdo, 'chat', 'no', DatabaseTable::$DATE_FUNC_DATETIME);
    $chatBlockTable = new DatabaseTable($pdo, 'chatblock', 'userid');

    /* 로그인 기능 구현 인스턴스 생성 */
    $authentication = new Authentication($userTable, 'id', 'pw'); 

    // 정상적으로 접근하는 경우 새로운 브라우저에 열리므로, 기존 브라우저의 아이디와 비밀번호를 가져온다.
    if (isset($_POST['id'])) {
        $_SESSION['id'] = $_POST['id'];
    } 
    if (isset($_POST['pw'])) {
        $_SESSION['pw'] = $_POST['pw'];
    }

    /** ==============================================================================
     *  채팅 구현 내부의 POST 처리
     *  ==============================================================================
     */

    // 채팅 차단 버튼을 클릭한 경우 동작
    if (isset($_POST['clickBlock'])) {

        /**
         * newReceiverId 가 존재하면 현재 채팅 상대방은 newReceiverId 이고, 
         * newReceiverId 가 존재하지 않으면 현재 채팅 상대방은 receiverId 이다.
         */
        $blockId = isset($_POST['newReceiverId'])? $_POST['newReceiverId'] : $_POST['receiverId'];

        $chatBlockTable->insert([
            'userid' => $_SESSION['id'],
            'blockid' => $blockId
        ]);

        $location = "?newReceiverId=0";
        if (isset($_POST['receiverId'])) {
            $location .= "&receiverId=" . $_POST['receiverId'];
        }
        header('Location: ' . $location);
        exit(0);
    }

    // 채팅 차단 해제 버튼을 클릭한 경우 동작
    if (isset($_POST['clickUndoBlock'])) {
        $chatBlockTable->deleteLookup($_SESSION['id'], 'blockid', $_POST['receiverId']);
    }

    /* ============================================================================== */

    // 채팅 상대방을 지정한 경우 POST로 전달된 값을 GET로 변경한다. (Redirect)
    if (isset($_POST['receiverId'])) {
        header('Location: ?receiverId=' . $_POST['receiverId']);
    }

    // error 메시지의 종류를 판별하는데 추가로 사용하기 위해 따로 저장한다.
    $isLoggedIn = $authentication->isLoggedIn();

    // 로그인과 성신인증이 완료된 경우에만 채팅 기능을 이용할 수 있다.
    $isAvailable = $isLoggedIn && $authentication->isSungshin();

    $title = '채팅방';

    /* chat */
    if ($isAvailable) {

        /**
         *  기존의 채팅 상대방 배열 생성 ($receiverList)
         */
        $receiverList = [];
        $chatList = $chatTable->findOrOptionDistinct([
            'senderid' => $_SESSION['id'],
            'receiverid' => $_SESSION['id']
        ]);
        // 최근에 채팅한 상대가 먼저 오도록 배열을 역순으로 나열한다.
        $chatList = array_reverse($chatList);
        foreach ($chatList as $connection) {
            if ($connection['senderid'] == $_SESSION['id']) {
                $receiverList[] = $connection['receiverid'];
            } else {
                $receiverList[] = $connection['senderid'];
            }
        }
        // 채팅 상대방을 지정한 경우 해당 아이디를 가장 앞에 위치시킨다.
        if (isset($_GET['receiverId'])) {
            array_unshift($receiverList , $_GET['receiverId']); 
        }
        // 중복되는 채팅 상대방을 배열에서 제거한다.
        $receiverList = array_unique($receiverList);
        // 차단한 상대방을 배열에서 제거한다.
        $blockList = [];
        $chatBlockList = $chatBlockTable->find('userid', $_SESSION['id']); 
        foreach ($chatBlockList as $key => $value) {
            $blockList[] = $value['blockid'];
        }
        $receiverList = array_diff($receiverList, $blockList);

        // 새로 도착한 메시지가 있는지 확인한다.
        foreach ($receiverList as $index => $receiver) {
            if ($chatTable->findAndOptionDistinct([
                'senderid' => $receiver,
                'receiverid' => $_SESSION['id'],
                'readstatus' => 'N'
            ]) ) {
                $receiverList[$index] = [
                    'id' => $receiver,
                    'hasNewMessage' => true
                ];
            } else {
                $receiverList[$index] = [
                    'id' => $receiver,
                    'hasNewMessage' => false
                ];
            }
        }

        // 현재 채팅 상대방이 차단한 상대방인지 확인한다.
        $currentOpponentId = isset($_GET['newReceiverId']) ? 
                                $_GET['newReceiverId'] : 
                                $_GET['receiverId'];
        $isBlock = in_array($currentOpponentId, $blockList);

        // 임의로 채팅 상대를 정하지 않은 창을 로드하는 경우
        if (!$isBlock && $currentOpponentId == 0) {
            $isBlock = false;
        }

        if ($isBlock) {
            ob_start();
            include __DIR__ . '/' . '../../private/templates/chat/chat_block.html.php';
            $content = ob_get_clean();
        } else {
            // 채팅 데이터 목록
            $chatDataList = $chatTable->getChatList($_SESSION['id'], $currentOpponentId);

            ob_start();
            include __DIR__ . '/' . '../../private/templates/chat/chat_content.html.php';
            $content = ob_get_clean();
        }

    }   // EndOf_if($isAvailable)
}
catch (PDOException $e) {
    $title = '오류가 발생했습니다.';    
    $content = '데이터베이스 오류: ' . $e->getMessage() . '<br/>' .    
          '위치: ' . $e->getFile() . ' : ' . $e->getLine();  
}

// html 코드
if ($isAvailable) {
    include __DIR__ . '/' . '../../private/templates/chat/chat.html.php';
} else {
    include __DIR__ . '/' . '../../private/templates/chat/error.html.php';
}
