<?php if ($user['id'] === $gCategory['userid']) : ?>
<div id="general_remove_category">
    <form action="" method="POST">
        <input type="submit" value="게시판 삭제하기" name="remove_category" />
    </form>
</div>
<?php endif; ?>

<div id="general_info">
    <div>
        <h1><?= es($gCategory['name']) ?></h1>
        <?php if ($gCategory['userid'] !== 'root'): // 운영자가 존재하지 않는 기본 게시판은 제외 ?>
        <p>운영자 : <a href="#"><?= $gCategory['userid'] ?></a></p>
        <?php endif; ?>
    </div>
    <p><?= es($gCategory['info']) ?></p>
</div>

<div id="general_nav">
    <form id="general_nav_form" action="" method="GET">
        <input type="hidden" name="category" value="<?= $gCategory['id'] ?>" />
        <select id="general_order" name="order">
            <option value="0">전체 글</option>
            <?php if ($_GET['order'] == 1) : ?>
            <option value="1" selected>오늘의 인기 글</option>
            <?php else: ?>
            <option value="1">오늘의 인기 글</option>
            <?php endif; ?>
        </select>

            <input type="text" id="general_search" name="search"
                autocomplete="off" value="<?= $_GET['search'] ?>" />
        <input type="submit" id="nav_submit" value="검색" />
    </form>
</div>

<div id="general_add">
    <form action="" method="POST" enctype="multipart/form-data">
        <textarea id="new_text" name="generaltext" maxlength="65535"></textarea>

        <div id="add_wrap"> 
            <label id="add_file_icon" for="add_file"></label>
            <input type="file" id="add_file" name="add_file[]" accept="image/*" multiple/>
            <label id="add_submit_icon" for="add_submit"></label>
            <input type="submit" id="add_submit" value="글 등록" />
        </div>
    </form>
</div>
<!-- 업로드한 이미지 미리보기 -->
<div id="general_add_img_wrap">
</div>


<div id="general_list">

    <!-- 한 페이지 당 8개 글 표시 -->
    <?php
    $i_page = (int)$_GET['i'] ?? 0;

    if ( ($i_page * 8 + 8) >= count($generalList) ) {
        $i_max = count($generalList);
        $i_next = false;  // 다음 페이지 존재 여부
    } else {
        $i_max = $i_page * 8 + 8;
        $i_next = true;  // 다음 페이지 존재 여부
    }

    for ($i = ($i_page * 8); $i < $i_max; $i++) : 
    ?> 
    <article id="<?= 'article_' . $generalList[$i]['id'] ?>">
        <a href="<?= 'generalview?category=' . $_GET['category'] . '&id=' . $generalList[$i]['id'] ?>" 
            onclick="<?= 'saveCameFromCookie(' . $generalList[$i]['id'] . ')'?>" >
            <div class="article_top">
                <div class="account_wrap">
                    <!-- 작성자 프로필 이미지 -->
                    <div class="img_wrap">
                        <img src="<?= './img/account_img/' . $generalList[$i]['accountimg'] . '.png' ?>" width="32" height="32"/>
                    </div>
                    <div class="info_wrap">
                        <p class="nickname"><?= es($generalList[$i]['nickname']) ?></p>
                        <p class="datetime"><?= datetimeFormatted($generalList[$i]['date']) ?></p>
                    </div>
                </div>
                <div class="delete_wrap">
                    <!-- 내가 쓴 글 지우기 기능 -->
                    <?php if ($user['id'] === $generalList[$i]['userid']) : ?>
                    <form action="" method="POST">
                        <div class="delete"><label class="delete_icon" 
                            for="<?= 'delete_' . $generalList[$i]['id'] ?>"></label></div>
                        <input type="hidden" name="delete_id" value="<?= $generalList[$i]['id'] ?>" />
                        <input id="<?= 'delete_' . $generalList[$i]['id'] ?>" 
                            class="delete" name="delete" type="submit" />
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            <div class="article_middle">
                <p><?=es($generalList[$i]['text'])?></p>
            </div>
            <div class="article_bottom">
                <div class="left">
                    <!-- 좋아요 -->
                    <form action="" method="POST"
                            onsubmit="<?= 'saveCookie(' . $generalList[$i]['id'] . ')'?>" >
                        <label class="likes_icon" for="<?= 'likes_' . $generalList[$i]['id'] ?>"></label>
                        <input type="hidden" name="likes_id" value="<?= $generalList[$i]['id'] ?>" />
                        <input id="<?= 'likes_' . $generalList[$i]['id'] ?>" 
                            class="likes" name="likes" type="submit" /> 
                    </form>
                    <!-- 좋아요 개수 -->
                    <p><?php
                        // 좋아요 개수가 음수인 경우에는 '-'으로 표시한다.
                        if ($generalList[$i]['likes'] < 0) { echo '-'; }
                        else { echo numToStr($generalList[$i]['likes']); }
                        ?></p>
                    <!-- 싫어요 -->
                    <form action="" method="POST"
                            onsubmit="<?= 'saveCookie(' . $generalList[$i]['id'] . ')'?>" >
                        <label class="dislikes_icon" for="<?= 'dislikes_' . $generalList[$i]['id'] ?>"></label>
                        <input type="hidden" name="dislikes_id" value="<?= $generalList[$i]['id'] ?>" />
                        <input id="<?= 'dislikes_' . $generalList[$i]['id'] ?>" 
                            class="dislikes" name="dislikes" type="submit" /> 
                    </form>
                    <label class="detail" data-type="비추" for="<?='dislikes_' . $generalList[$i]['id']?>">비추</label>
                    <!-- 외부인 expel --> 
                    <form action="" method="POST"
                            onclick="<?='saveCookie(' . $generalList[$i]['id'] . ', 10)'?>" 
                            class="stranger" >
                        <div class="stranger"><label class="stranger_icon" 
                            for="<?= 'stranger_' . $generalList[$i]['id'] ?>"></label></div>
                        <input type="hidden" name="stranger_id" value="<?= $generalList[$i]['id'] ?>" />
                        <input type="hidden" name="stranger_userid" value="<?= $generalList[$i]['userid'] ?>" />
                        <input id="<?= 'stranger_' . $generalList[$i]['id'] ?>" 
                            class="stranger" name="stranger" type="submit" /> 
                    </form>
                    <label class="detail" data-type="신고" for="<?='stranger_' . $generalList[$i]['id']?>">신고</label>
                </div>
                <div class="right">
                    <!-- 이미지 개수 -->
                    <?php if ($generalList[$i]['img']) : ?>
                    <img class="img" data-type="image" src="./img/general/image_24.png" width="24" height="24" />
                    <div class="img_wrap"><p><?= $generalList[$i]['img'] ?></p></div>
                    <?php endif; ?>
                    <img class="comments" data-type="comment" src="./img/general/comment_24.png" width="24" height="24" />
                    <!-- 댓글 개수 -->
                    <div class="comment_wrap"><p><?= numToStr($generalList[$i]['comments']) ?></p></div>
                </div>
            </div>
        </a>
    </article>
    <?php endfor; ?>

</div>

<div id="general_list_nav">
    <?php if ($i_page != 0 && $i_page != 1): ?>
    <a class="first" href="<?= 'general?category=' . $_GET['category'] . '&order=' . ($_GET['order'] ?? 0) 
        . '&search=' . ($_GET['search'] ?? '') . '&i=0' ?>" onclick="removeCookie()"><< 처음</a>
    <?php endif; ?>
    <?php if ($i_page != 0): ?>
    <a href="<?= 'general?category=' . $_GET['category'] . '&order=' . ($_GET['order'] ?? 0)
         . '&search=' . ($_GET['search'] ?? '') . '&i=' . ((int)$_GET['i']-1) ?>" onclick="removeCookie()">< 이전</a>
    <?php endif; ?>
    <?php if ($i_next): ?>
    <a class="right" href="<?= 'general?category=' . $_GET['category'] . '&order=' . ($_GET['order'] ?? 0)
        . '&search=' . ($_GET['search'] ?? '') . '&i=' . ((int)$_GET['i']+1) ?>" onclick="removeCookie()">다음 ></a>
    <?php endif; ?>
</div>
