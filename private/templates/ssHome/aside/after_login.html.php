<div id="after_account_wrap">
    <!-- ------------------------------------------------------------------------ --> 
    <!-- [중요] 경로의 기준은 해당 템플릿을 include 하는 public/ssHome/~ 가 된다. -->
    <!-- ------------------------------------------------------------------------ -->
    <div id="my_account">

        <div class="profile_wrap">
            <div class="img_wrap">
                <img src="<?='./img/account_img/' . $user['accountimg'] . '.png'?>" 
                    width="64" height="64"/>
            </div>
            <div class="nickname_wrap">
                <p><?=es($user['nickname'])?></p>
            </div>
            <div class="nav_wrap">
                <a class="info" href="myinfo">내 정보</a>
                <a class="logout" href="logout" onclick="logoutChat()">로그아웃</a>
            </div>
        </div>

    <!-- 성신 인증 여부 구현 --> 
    <?php if ($user['issungshin'] === 'Y'): ?> 
    <div class="issungshin_wrap">
        <p>성신 인증 완료</p>
    </div>
    <?php else: ?>
    <a id="issungshin_wrap" 
        href="<?='http://sscommu.com:' . mt_rand(8000, 8019) . '/cgi-bin/authorize.py?userid=' 
                . $user['id']. '&studentid=' . $user['studentid']?>">
        <p>성신 인증 하기</p>
    </a>
    <?php endif; ?>
    </div>

    <!-- 글 관리 -->
    <a class="button buttonText" href="myarticle?select=general">글 관리</a>

    <!-- 채팅 -->
    <div class="button" 
        onclick="<?="openChat('" . $user['id'] . "', '" . $user['pw'] . "')"?>">
        <p class="buttonText">채팅</p>
    </div>

    <div id="ads_wrap">
        <!-- 광고를 넣는 경우 사용 -->

    </div>

</div>
