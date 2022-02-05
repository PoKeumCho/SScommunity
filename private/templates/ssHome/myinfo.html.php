<div class="button_wrap">
    <div class="button" id="change_pw_button">
        <a href="myinfo?mode=viewChangePw">비밀번호 변경</a>
    </div>
    <div class="button" id="change_nickname_button">
        <a href="myinfo?mode=viewChangeNickname">닉네임 변경</a>
    </div>
    <div class="button" id="change_accountimg_button">
        <a href="myinfo?mode=viewChangeAccountimg">프로필 이미지 변경</a>
    </div>
    <div class="button" id="withdrawal_button">
        <a href="myinfo?mode=viewWithdrawal">회원탈퇴</a>
    </div>
</div>

<div class="change_wrap">

    <div id="change_pw">
        <h1>비밀번호 변경</h1>
        <h6>비밀번호는 8~16자의 영문 대소문자와 숫자, 특수문자(!@#$%^&*)를 사용할 수 있습니다.</h6>
        <form action="" method="POST">
            <input name="cur_pw" id="cur_pw" type="password"
                maxlength="16" placeholder="현재 비밀번호" />
            <?php if (!empty($errors['cur_pw'])) : ?>
            <p class="errMsg"><?= $errors['cur_pw'][0] ?></p>
            <?php endif; ?>
            <input name="new_pw" id="new_pw" type="password" 
                maxlength="16" placeholder="새 비밀번호" />
            <?php if (!empty($errors['new_pw'])) : ?>
            <p class="errMsg"><?= $errors['new_pw'][0] ?></p>
            <?php endif; ?>
            <input name="new_pw_confirm" id="new_pw_confirm" type="password" 
                maxlength="16" placeholder="새 비밀번호 확인" />
            <?php if (!empty($errors['new_pw_confirm'])) : ?>
            <p class="errMsg"><?= $errors['new_pw_confirm'][0] ?></p>
            <?php endif; ?>
            <input id="pw_submit" class="submit" type="submit" name="pw_submit" value="확인" />
        </form>
    </div>

    <div id="change_nickname">
        <h1>닉네임 변경</h1>
        <form action="" method="POST">
            <input name="new_nickname" id="new_nickname" type="text" 
                maxlength="8" value="<?= es($user['nickname']) ?>" />
            <?php if (!empty($errors['new_nickname'])) : ?>
            <p class="errMsg"><?= $errors['new_nickname'][0] ?></p>
            <?php endif; ?>
            <input class="submit" type="submit" name="nickname_submit" value="확인" />
        </form>
    </div>

    <div id="change_accountimg">
        <h1>프로필 이미지 변경</h1>
        <form action="" method="POST">
            <select id="accountimg_select" name="accountimg_id">
                <option value="1">기본 이미지</option>
                <option value="2">재학생</option>
                <option value="3">휴학생 / 백수</option>
                <option value="4">졸업생</option>
                <option value="5">직장인</option>
            </select>
            <img id="accountimg_preview" src="./img/account_img/default.png" width="226" />
            <input class="submit" type="submit" name="accountimg_submit" value="확인" />
        </form>
    </div>

    <div id="withdrawal">
        <h1>회원탈퇴</h1>
        <form action="" method="POST">
            <input name="withdrawal_cur_pw" id="withdrawal_cur_pw" type="password" 
                maxlength="16" placeholder="현재 비밀번호" />
            <?php if (!empty($errors['withdrawal_cur_pw'])) : ?>
            <p class="errMsg"><?= $errors['withdrawal_cur_pw'][0] ?></p>
            <?php endif; ?>
            <input class="submit" id="withdrawal_submit" type="submit" name="withdrawal_submit" value="회원탈퇴 신청" />
        </form>
    </div>

</div>
