<div id="block_wrap">

    <div class="error_wrap">
        <img src="./img/error/warning.png" width="64" height="64" />
        <h1><?=(isset($_GET['newReceiverId']) ? $_GET['newReceiverId'] : $_GET['receiverId'])?>님은
            <br/>차단된 채팅 상대입니다.</h1>

        <form action="" method="POST">
            <input type="hidden" name="clickUndoBlock" />
            <input type="hidden" name="receiverId" 
                value="<?=(isset($_GET['newReceiverId']) ? $_GET['newReceiverId'] : $_GET['receiverId'])?>"> 
            <input type="submit" value="차단 해제" />
        </form>
    </div>

</div>
