<div class="msgWrap">
    <div id="msg">
        <?php if ($_GET['mode'] == 'success'): ?>
        <p><?=es($_GET['email'])?>로 이메일을 발송했습니다.</p>
        <?php elseif ($_GET['mode'] == 'fail'): ?>
        <p>입력하신 정보와 일치하는 회원정보가 존재하지 않습니다.</p>
        <?php else: ?>
        <p>올바르지 않은 접근입니다.</p> 
        <?php endif; ?>
    </div>
</div>

<div class="btnWrap">
    <div class="button">
        <a href="login">확인</a>
    </div>
</div>
