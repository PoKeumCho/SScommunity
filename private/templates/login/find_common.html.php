<form action="" method="POST">

    <div class="titleWrap">
        <h1>
            <?php if ($findCommonType == 'id'): ?>
            아이디 찾기
            <?php elseif ($findCommonType == 'pw'): ?>
            비밀번호 찾기
            <?php endif; ?>
        </h1>
    </div>

    <div class="inputWrap">
        <?php if ($findCommonType == 'pw'): ?>
        <input name="id" id="id" type="text" placeholder="아이디" maxlength="16" autocomplete="off" />
        <?php endif; ?>
        <input name="name" id="name" type="text" placeholder="이름" maxlength="6" autocomplete="off" />
    </div>

    <div class="navWrap">
        <a href="#" id="selectEmail" class="selected">이메일</a>
        <a href="#" id="selectStudentid">학번</a>
    </div>
    <div class="inputWrap">
        <input name="email" id="email" type="email" 
            placeholder="example@example.com" maxlength="320" autocomplete="off" />
        <input name="studentid" id="studentid" type="text" class="hidden"
            placeholder="20150000" maxlength="8" autocomplete="off" />
    </div>

    <div class="submitWrap">
        <input type="submit" name="submit" id="submit" value="Continue" />
    </div>

</form>
