<!DOCTYPE html>
<html lang="ko">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">

        <!-- favicon (https://www.favicon-generator.org/) -->
        <link rel="apple-touch-icon" sizes="57x57" href="../favicon/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="../favicon/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="../favicon/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="../favicon/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="../favicon/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="../favicon/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="../favicon/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="../favicon/apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="../favicon/apple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="192x192"  href="../favicon/android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="../favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="../favicon/favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="../favicon/favicon-16x16.png">
        <link rel="manifest" href="../favicon/manifest.json">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="../favicon/ms-icon-144x144.png">
        <meta name="theme-color" content="#ffffff">

        <?php include __DIR__ . '/../../includes/html/googleFonts.html.php'; ?>

        <link rel="stylesheet" href="./css/cssReset.css">    
        <link rel="stylesheet" href="./css/chat.styles.css">    

        <script src="./js/jquery.js"></script>
        <script src="./js/xmlhttp.js"></script>
        <script src="./js/chat.js"></script>

        <script src="./js/alarm.js"></script>
        <script src="./js/loading.js"></script>

        <title><?=$title?></title>
    </head>
    <body>
        <!-- 1행 -->
        <nav id="chat_nav">
            <form id="selectReceiverForm" action="" method="GET">
                <select id="selectReceiver" name="newReceiverId">
                    <option value="0" <?php if (!isset($_GET['receiverId']) && !isset($_GET['newReceiverId'])) {
                            echo 'selected="selected"';
                        } ?>
                    >[-- 채팅 상대 --]</option>
                    <?php foreach ($receiverList as $index => $receiver): ?>
                    <option id="<?=$receiver['id']?>" value="<?=$receiver['id']?>"
                        class="<?php if ($receiver['hasNewMessage']) { echo 'hasNewMessage'; } ?>"
                        <?php 
                        if ((isset($_GET['receiverId']) && $index == 0 && !isset($_GET['newReceiverId'])) ||
                        (isset($_GET['newReceiverId']) && ($receiver['id'] == $_GET['newReceiverId']))) {
                            echo 'selected="selected"';
                        }    
                        ?>
                    ><?=$receiver['id']?> <?php if ($receiver['hasNewMessage']) { echo ' &#128276;'; } ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($_GET['receiverId'])): ?>
                <input type="hidden" name="receiverId" value="<?=$_GET['receiverId']?>" />
                <?php endif; ?>
            </form>

            <img id="notification" class="hidden" src="./img/chat/notification.png" width="28" height="28" />              

            <img id="block" class="clickable" src="./img/chat/block.png" width="28" height="28" />              
            <form id="blockFrom" action="" method="POST">
                <input type="hidden" name="clickBlock" />
                <?php if (isset($_GET['receiverId'])): ?>
                <input type="hidden" name="receiverId" value="<?=$_GET['receiverId']?>" />
                <?php endif; ?>
                <?php if (isset($_GET['newReceiverId'])): ?>
                <input type="hidden" name="newReceiverId" value="<?=$_GET['newReceiverId']?>" />
                <?php endif; ?>
            </form>
        </nav>
        <!-- 2행 -->
        <div id="chat_content">
            <!--
                * div [id = chat_wrap]
                * div [id = block_wrap]     :   차단된 상대방인 경우
            -->
            <?php /* 디버깅 용 */
                //print_r($receiverList);
                //print_r($chatDataList);
            ?>
            <?= $content ?>
        </div>
        <!-- 3행 -->
        <div id="chat_sendMsg">
            <div class="top">
            <!-- 입력창을 확대(축소)하기 위한 용도로 사용하고자 했으나 
                 javascript와 css를 이용하여 textarea 요소를 입력에 따라 자동으로 확대(축소)가 가능하게 하였다. -->
            </div>
            <div class="middle">
                <div class="left">
                    <label for="files-list" class="clickable">
                        <img src="./img/chat/clip.png" width="24" height="24" /> 
                    </label>              
                    <input type="file" id="files-list" accept="image/*" multiple hidden />
                </div>
                <div class="center">
                    <div id="chat">
                        <div id="ta-frame">
                            <textarea placeholder="메시지를 입력하세요" rows="1"></textarea>
                        </div>
                    </div>
                </div>
                <div id="submit_chat" class="right clickable">
                    <img src="./img/chat/send.png" width="24" height="24" />
                </div> 
            </div>
            <div class="bottom hidden">
                <div id="img_wrap" class="img_wrap">
                </div>
            </div>
        </div>
    </body>
</html>
