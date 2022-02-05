<form action="" method="POST">
        

    <div id="id_wrap" class="wrap">
        <div><label for="id">아이디</label></div>
        <input name="user[id]" id="id" type="text" 
            value="<?= $user['id'] ?? '' ?>" maxlength="16" autocomplete="off"
            onblur="validate('id', this.value)"/>
        <?php if (!empty($errors['id'])) : ?>
            <p id="id_duplicate_prevention"><?= $errors['id'][0] ?></p>
            <span class="prevent_resubmit"></span>
        <?php endif; ?>

        <!-- ajax를 사용해서 중복확인을 구현한다. --> 
        <p id="idValidateP"></p>
        
        <!--
            join_onload.js 에서  
            <h3 class="msgElem"> 요소를 생성한다. 
        -->
    </div>

    <div id="pw_wrap" class="wrap">
        <div><label for="pw">비밀번호</label></div>
        <input name="user[pw]" id="pw" type="password" 
            value="<?= $user['pw'] ?? '' ?>" maxlength="16" />
        <?php if (!empty($errors['pw'])) : ?>
            <p><?= $errors['pw'][0] ?></p>
            <span class="prevent_resubmit"></span>
        <?php endif; ?>
    </div>

    <div id="pw_confirm_wrap" class="wrap">
        <div><label for="pw_confirm">비밀번호 재확인</label></div>
        <input name="user[pw_confirm]" id="pw_confirm" type="password" 
            maxlength="16" />
        <?php if (!empty($errors['pw_confirm'])) : ?>
            <p><?= $errors['pw_confirm'][0] ?></p>
            <span class="prevent_resubmit"></span>
        <?php endif; ?>
    </div>

    <div id="name_wrap" class="wrap">
        <div><label for="name">이름</label></div>
        <input name="user[name]" id="name" type="text" 
            value="<?= $user['name'] ?? '' ?>" maxlength="6" />
        <?php if (!empty($errors['name'])) : ?>
            <p><?= $errors['name'][0] ?></p>
            <span class="prevent_resubmit"></span>
        <?php endif; ?>
    </div>

    <div id="birthdate_wrap" class="wrap">
        <div><label for="birthdate_year">생년월일</label></div>
        <div id="birthdate_subwrap">
            <input name="user[birthdate][year]" id="birthdate_year" type="text" maxlength="4"
                value="<?= $user['birthdate']['year'] ?? '' ?>"/> 
            <select name="user[birthdate][month]">
                <?php for($month = 1; $month < 10; $month++) : ?>
                <option value="0<?=$month?>" <?= 
                    '0' . $month == $user['birthdate']['month'] ? 'selected' : '' ?>><?=$month?></option>
                <?php endfor; ?>
                <?php for($month = 10; $month <= 12; $month++) : ?>
                <option value="<?=$month?>" <?=
                    $month == $user['birthdate']['month'] ? 'selected' : '' ?>><?=$month?></option>
                <?php endfor; ?>
            </select>
            <input name="user[birthdate][date]" id="birthdate_date" type="text" maxlength="2"
                value="<?= $user['birthdate']['date'] ?? '' ?>"/> 
        </div>
        <?php if (!empty($errors['birthdate'])) : ?>
            <p><?= $errors['birthdate'][0] ?></p>
            <span class="prevent_resubmit"></span>
        <?php endif; ?>
    </div>

    <div id="nickname_wrap" class="wrap">
        <div><label for="nickname">닉네임</label></div>
        <input name="user[nickname]" id="nickname" type="text" 
            value="<?= $user['nickname'] ?? '익명' ?>" maxlength="8" />
        <?php if (!empty($errors['nickname'])) : ?>
            <p><?= $errors['nickname'][0] ?></p>
            <span class="prevent_resubmit"></span>
        <?php endif; ?>
    </div>

    <div id="student_id_wrap" class="wrap">
        <div><label for="student_id">학번 (본인 확인)</label></div>
        <input name="user[studentid]" id="student_id" type="text" 
            placeholder="(example : 20150000)" value="<?= $user['studentid'] ?? '' ?>" maxlength="8" autocomplete="off"
            onblur="validate('studentid', this.value)" />
        <?php if (!empty($errors['studentid'])) : ?>
            <p id="studentid_duplicate_prevention"><?= $errors['studentid'][0] ?></p>
            <span class="prevent_resubmit"></span>
        <?php endif; ?>
        <!-- ajax를 사용해서 중복확인을 구현한다. --> 
        <p id="studentidValidateP"></p>
    </div>

    <div id="email_wrap" class="wrap">
        <div><label for="email">이메일 (본인 확인)</label></div>
        <input name="user[email]" id="email" type="email" 
            value="<?= $user['email'] ?? '' ?>" maxlength="320" autocomplete="off"
            onblur="validate('email', this.value)" />
        <?php if (!empty($errors['email'])) : ?>
            <p id="email_duplicate_prevention"><?= $errors['email'][0] ?></p>
            <span class="prevent_resubmit"></span>
        <?php endif; ?>
        <!-- ajax를 사용해서 중복확인을 구현한다. --> 
        <p id="emailValidateP"></p>
    </div>

    <div class="wrap">
        <input type="submit" name="submit" value="가입하기" />
    </div>

</form>
