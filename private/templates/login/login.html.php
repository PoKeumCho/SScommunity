<section id="login_section">
    <form action="" method="POST">
        <input id="login_id" type="text" name="userID" 
            placeholder="아이디" maxlength="16" /><br/>
        <input id="login_pw" type="password" name="userPW" 
            placeholder="비밀번호" maxlength="16" /><br/>
        <?php
        if (isset($error)):
        echo '<div class="error"><p>' . $error . '</p></div>';
        endif;
        ?>
        <input id="login_submit" type="submit" value="로그인"/><br/>
    </form>
</section>
<aside id="login_aside">
    <div id="login_aside_wrap">
    <!-- ------------------------------------------------------------------------ -->    
    <!-- [중요] 경로의 기준은 해당 템플릿을 include 하는 public/login/~ 가 된다. -->    
    <!-- ------------------------------------------------------------------------ -->    
        <ul>
            <li><a href="findid">아이디 찾기</a></li>
            <li><a href="findpw">비밀번호 찾기</a></li>
            <li><a href="terms">회원가입</a></li>
        </ul>
    </div>
</aside>
