<?php
    include_once __DIR__ . '/' . '../../includes/lib/util.php';
?>

<div id="chat_wrap">

    <?php 

    $findNewChat = false;

    foreach($chatDataList as $key => $chatData):
        $dt = DateTime::createFromFormat('Y-m-d H:i:s', $chatData['datetime']);
    ?>
    <div class="<?php 
        if ($chatData['senderid'] == $_SESSION['id']) {
            if ($findNewChat == false && $key == (count($chatDataList)-1)) {
                echo 'me newChat';      // most recent
                $findNewChat = true;
            } else { echo 'me'; }
        } else {
            if ($findNewChat == false && 
                    ($chatData['readstatus'] == 'N'                 // first new
                        || $key == (count($chatDataList)-1))) {     // most recent
                echo 'other newChat';
                $findNewChat = true;
            } else { echo 'other'; }
        }?>" data-chat-no="<?=$chatData['no']?>" >
        <?php if ($chatData['contenttype'] == 'T'): // Text 처리 ?> 
        <p class="text"><?=es($chatData['text'])?></p>
        <?php else: // File 처리 ?>
        <div class="file">
            <img src="../../file/images/chat/<?=$chatData['path']?>" />
        </div>
        <?php endif; ?>
        <p class="datetime"><?=$dt->format("y.m.d")?><br/><?=$dt->format("H:i")?></p>
    </div>
    <?php endforeach; ?>

</div>
