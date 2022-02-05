<div id="after_account_wrap">
    <div id="my_account">
        <div class="profile_wrap">
            <div class="img_wrap">
                <img src="<?='./img/account_img/' . $user['accountimg'] . '.png'?>" 
                    width="64" height="64"/>
            </div>
            <div class="nickname_wrap">
                <p><?=es($user['nickname'])?></p>
            </div>
            <div class="logout_wrap">
                <a class="logout" href="logout" onclick="logoutChat()">로그아웃</a>
            </div>
        </div>
    </div>

    <div id="ads_wrap">
        <!-- 광고를 넣는 경우 사용 -->
    </div>

    <!-- 성신 인증 여부 구현 --> 
    <?php if ($user['issungshin'] !== 'Y'): ?>
    <a id="issungshin_wrap" 
        href="<?='http://sscommu.com:' . mt_rand(8000, 8019) . '/cgi-bin/authorize.py?userid=' 
                . $user['id']. '&studentid=' . $user['studentid']?>">
        <p>성신 인증 하기 Click</p>
    </a>
    <?php endif; ?>
</div>
