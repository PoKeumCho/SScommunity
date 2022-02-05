<form action="" method="POST">

    <div class="wrap">
        <div><label for="code">인증번호 입력</label></div>
        <input name="code" id="code" type="text" maxlength="10" />
        <?php if (!empty($errors)) : ?>
            <p><?= $errors[0] ?></p>
        <?php endif; ?>
    </div>

    <p>
        이메일(<?= es($_GET['email']) ?>)로 인증번호를 발송했습니다.<br/>
        인증번호가 오지 않은 경우 하단에 기재된 이메일로 문의 바랍니다.
    </p>


    <div class="wrap">
        <input type="submit" name="submit" value="이메일 인증 하기" />
    </div>

</form>
