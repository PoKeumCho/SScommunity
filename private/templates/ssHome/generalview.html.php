<div id="generalview_category_info">
    <h1><?= es($gView['categoryname']) ?></h1>
</div>

<div id="generalview_nav">
    <a href="<?= 'general?category=' . $_GET['category'] ?>">목록</a>
    <!-- 내가 쓴 글 지우기 기능 -->
    <?php if ($user['id'] === $gView['userid']) : ?>
    <div id="generalview_remove_general">
        <form action="" method="POST">
            <input type="submit" value="글 삭제하기" name="remove_general" />
        </form>
    </div>
    <?php endif; ?>
</div>

      
<div id="generalview_writer_info">
    <!-- 작성자 프로필 이미지 -->
    <div class="img_wrap">
        <img src="<?= './img/account_img/' . $gView['accountimg'] . '.png' ?>" width="64" height="64"/>
    </div>
    <div class="text_wrap">
        <?php if ($user['id'] === $gView['userid'] || $gView['accountimg'] === 'ghost'): ?>
        <a class="nickname">
        <?php else: ?>
        <a class="nickname chat" 
            onclick="<?="openChat('" . $user['id'] . "', '" . $user['pw'] . "', '" . $gView['userid'] . "')"?>">
        <?php endif; ?>
            <?= es($gView['nickname']) ?>
        </a>
        <p class="datetime"><?= datetimeFormatted($gView['date']) ?></p>
    </div>
</div>

<div id="generalview_article">

    <div class="text"><?=es($gView['text'])?></div>

    <!-- 이미지 파일이 존재하는 경우 -->
    <?php 
    if (isset($gView['imgpath'])) : 
    // 이미지 넓이 최대 한계
    $max_width = strpos($_SERVER['REQUEST_URI'], 'Mobile') ? 315 : 575;
    ?>
    <div class="img">
        <?php foreach ($gView['imgpath'] as $key => $imgpath) : ?>
        <img src="<?= '../../file/images/general/' . $imgpath ?>" 
            width="<?= $gView['imgwidth'][$key] < $max_width ? $gView['imgwidth'][$key] : $max_width ?>" />
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="clicks">

        <!-- 좋아요 -->
        <form action="" method="POST">
            <label id="likes_icon" for="likes"></label>
            <input id="likes" name="likes" type="submit" /> 
        </form>
        <!-- 좋아요 개수 -->
        <p><?php
            // 좋아요 개수가 음수인 경우에는 '-'으로 표시한다.
            if ($gView['likes'] < 0) { echo '-'; }
            else { echo numToStr($gView['likes']); }
            ?></p>
        <!-- 싫어요 -->
        <form action="" method="POST">
            <label id="dislikes_icon" for="dislikes"></label>
            <input id="dislikes" name="dislikes" type="submit" /> 
        </form>
        <label class="detail" for="dislikes">비추</label> 
        <!-- 외부인 expel --> 
        <form action="" method="POST" class="stranger">
            <div><label id="stranger_icon" for="stranger"></label></div>
            <input type="hidden" name="stranger_userid" value="<?= $gView['userid'] ?>" />
            <input id="stranger" name="stranger" type="submit" /> 
        </form>
        <label class="detail" for="stranger">신고</label> 
    </div>
</div>



<div id="generalview_add_comment">
    <form action="" method="POST">
        <textarea id="new_comment" name="generalcomment" maxlength="65535"></textarea>

        <div id="add_wrap"> 
            <label id="add_submit_icon" for="add_submit"></label>
            <input type="submit" id="add_submit" value="댓글 등록" />
        </div>
    </form>
</div>


<div id="generalview_comments">

    <!-- 한 페이지 당 10개 글 표시 -->
    <?php
    $i_page = $_GET['i'] ?? 0;

    if ( ($i_page * 10 + 10) >= count($gBaseComments) ) {
        $i_max = count($gBaseComments);
        $i_next = false;  // 다음 페이지 존재 여부
    } else {
        $i_max = $i_page * 10 + 10;
        $i_next = true;  // 다음 페이지 존재 여부
    }

    for ($i = ($i_page * 10); $i < $i_max; $i++) : 
    ?> 
    <!-- 댓글과 대댓글을 감싼다 -->
    <div class="comment_wrap" id="<?= 'comment_wrap_' . $gBaseComments[$i]['id'] ?>">

        <!-- 댓글 -->
        <div class="base">
            <div class="base_top">
                <!-- 작성자 프로필 -->
                <div class="account_wrap">
                    <div class="img_wrap">
                        <img src="<?= './img/account_img/' . $gBaseComments[$i]['accountimg'] . '.png' ?>" width="32" height="32"/>
                    </div>
                    <div class="info_wrap">
                        <?php if ($user['id'] === $gBaseComments[$i]['userid'] || $gBaseComments[$i]['accountimg'] === 'ghost'): ?>
                        <a class="nickname">
                        <?php else: ?>
                        <a class="nickname chat"
                            onclick="<?="openChat('" . $user['id'] . "', '" . $user['pw'] . "', '" . 
                                                    $gBaseComments[$i]['userid'] . "')"?>" >
                        <?php endif; ?>
                            <?= es($gBaseComments[$i]['nickname']) ?>
                        </a>
                        <p class="datetime"><?= datetimeFormatted($gBaseComments[$i]['date']) ?></p>
                    </div>
                </div>
                <!-- 내가 쓴 댓글 지우기 기능 -->
                <div class="delete_wrap">
                    <?php if ($user['id'] === $gBaseComments[$i]['userid']) : ?>
                    <form action="" method="POST">
                        <div class="base_comment_delete"><label class="base_comment_delete_icon" 
                            for="<?= 'base_comment_delete_' . $gBaseComments[$i]['id'] ?>"></label></div>
                        <input type="hidden" name="base_comment_delete_id" 
                            value="<?= $gBaseComments[$i]['id'] ?>" />
                        <input id="<?= 'base_comment_delete_' . $gBaseComments[$i]['id'] ?>" 
                            class="base_comment_delete" name="base_comment_delete" type="submit" />
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            <div class="base_middle">
                <p><?=es($gBaseComments[$i]['text'])?></p>
            </div>
            <div class="base_bottom">
                <div class="left">
                    <!-- 좋아요 -->
                    <form action="" method="POST"
                        onsubmit="<?= 'saveCommentCookie(0, ' . $gBaseComments[$i]['id'] . ')'?>">
                        <label class="base_comment_likes_icon" 
                            for="<?= 'base_comment_likes_' . $gBaseComments[$i]['id'] ?>"></label>
                        <input type="hidden" name="comment_likes_id" value="<?= $gBaseComments[$i]['id'] ?>" />
                        <input id="<?= 'base_comment_likes_' . $gBaseComments[$i]['id'] ?>" 
                            class="base_comment_likes" name="comment_likes" type="submit" /> 
                    </form>
                    <!-- 좋아요 개수 -->
                    <p><?php
                        // 좋아요 개수가 음수인 경우에는 '-'으로 표시한다.
                        if ($gBaseComments[$i]['likes'] < 0) { echo '-'; }
                        else { echo numToStr($gBaseComments[$i]['likes']); }
                        ?></p>
                    <!-- 싫어요 -->
                    <form action="" method="POST"
                        onsubmit="<?= 'saveCommentCookie(0, ' . $gBaseComments[$i]['id'] . ')'?>">
                        <label class="base_comment_dislikes_icon" 
                            for="<?= 'base_comment_dislikes_' . $gBaseComments[$i]['id'] ?>"></label>
                        <input type="hidden" name="comment_dislikes_id" value="<?= $gBaseComments[$i]['id'] ?>" />
                        <input id="<?= 'base_comment_dislikes_' . $gBaseComments[$i]['id'] ?>" 
                            class="base_comment_dislikes" name="comment_dislikes" type="submit" /> 
                    </form>
                    <label class="detail" data-type="비추" for="<?='base_comment_dislikes_' . $gBaseComments[$i]['id']?>">
                        비추</label>
                    <!-- 외부인 expel --> 
                    <form action="" method="POST" class="stranger"
                        onclick="<?= 'saveCommentCookie(0, ' . $gBaseComments[$i]['id'] . ', 10)'?>">
                        <div class="base_comment_stranger"><label class="base_comment_stranger_icon" 
                            for="<?= 'base_comment_stranger_' . $gBaseComments[$i]['id'] ?>"></label></div>
                        <input type="hidden" name="comment_stranger_id" value="<?= $gBaseComments[$i]['id'] ?>" />
                        <input type="hidden" name="comment_stranger_userid" value="<?= $gBaseComments[$i]['userid'] ?>" />
                        <input id="<?= 'base_comment_stranger_' . $gBaseComments[$i]['id'] ?>" 
                            class="base_comment_stranger" name="comment_stranger" type="submit" /> 
                    </form>
                    <label class="detail" data-type="신고" for="<?='base_comment_stranger_' . $gBaseComments[$i]['id']?>">
                        신고</label>
                </div>
                <div class="right">
                    <a href="<?= 'generalview?category=' . $_GET['category'] . '&id=' . $_GET['id'] . '&i=' . ($_GET['i'] ?? 0)
                        . '&j_' . $gBaseComments[$i]['id'] . '=' . ($_GET['j_' . $gBaseComments[$i]['id']] ?? 0) ?>"
                        onclick="<?= 'saveCommentCookie(0, ' . $gBaseComments[$i]['id'] . ')' ?>" >
                        <img class="child_comments" src="./img/general/comment_16.png" width="16" height="16" />
                        <!-- 댓글 개수 -->
                        <p><?=numToStr($gBaseComments[$i]['comments'])?></p>
                    </a>
                </div>
            </div>

        </div>
        <!-- 댓글 -->


        <!-- 대댓글 쓰기 -->
        <div class="add_child_comment" id="<?= 'add_child_comment_' . $gBaseComments[$i]['id'] ?>">
            <form action="" method="POST"
                onsubmit="<?= 'saveCommentCookie(0, ' . $gBaseComments[$i]['id'] . ')'?>" >
                <textarea id="new_child_comment" name="child_comment" maxlength="65535"></textarea>
                <input type="hidden" name="base_comment_id" value="<?= $gBaseComments[$i]['id'] ?>" />

                <div id="add_wrap"> 
                    <label class="add_child_icon" for="<?= 'add_child_submit_' . $gBaseComments[$i]['id'] ?>"></label>
                    <input type="submit" class="add_child_submit" 
                        id="<?= 'add_child_submit_' . $gBaseComments[$i]['id'] ?>" value="대댓글 등록" />
                </div>
            </form>
        </div>


        <!-- 대댓글을 감싼다 -->
        <div class="child_comment_wrap" id="<?= 'child_comment_wrap_' . $gBaseComments[$i]['id'] ?>">

            <!-- 한 페이지 당 5개 글 표시 -->
            <?php
            $j_page = $_GET[ 'j_' . $gBaseComments[$i]['id'] ] ?? 0;

            if ( ($j_page * 5 + 5) >= $gBaseComments[$i]['comments'] ) {
                $j_max = $gBaseComments[$i]['comments'];
                $j_next = false;  // 다음 페이지 존재 여부
            } else {
                $j_max = $j_page * 5 + 5;
                $j_next = true;  // 다음 페이지 존재 여부
            }

            $group = $gBaseComments[$i]['group'];

            for ($j = ($j_page * 5); $j < $j_max; $j++) : 
            ?> 
            <!-- 대댓글 -->
            <div class="child" id="<?= 'child_' . $gChildComments[$group][$j]['id'] ?>">

                <div class="child_top">
                    <!-- 작성자 프로필 -->
                    <div class="account_wrap">
                        <div class="img_wrap">
                            <img src="<?= './img/account_img/' . $gChildComments[$group][$j]['accountimg'] . '.png' ?>" 
                                width="32" height="32"/>
                        </div>
                        <div class="info_wrap">
                            <?php if ($user['id'] === $gChildComments[$group][$j]['userid'] || 
                                        $gChildComments[$group][$j]['accountimg'] === 'ghost'): ?>
                            <a class="nickname">
                            <?php else: ?>
                            <a class="nickname chat"
                                onclick="<?="openChat('" . $user['id'] . "', '" . $user['pw'] . "', '" . 
                                $gChildComments[$group][$j]['userid'] . "')"?>"
                            >
                            <?php endif; ?>
                                <?= es($gChildComments[$group][$j]['nickname']) ?>
                            </a>
                            <p class="datetime"><?= datetimeFormatted($gChildComments[$group][$j]['date']) ?></p>
                        </div>
                    </div>
                    <!-- 내가 쓴 대댓글 지우기 기능 -->
                    <div class="delete_wrap">
                        <?php if ($user['id'] === $gChildComments[$group][$j]['userid']) : ?>
                        <form action="" method="POST"
                            onsubmit="<?= 'saveCommentCookie(0, ' . $gBaseComments[$i]['id'] . ')'?>">
                            <div class="child_comment_delete"><label class="child_comment_delete_icon" 
                                for="<?= 'child_comment_delete_' . $gChildComments[$group][$j]['id'] ?>"></label></div>
                            <input type="hidden" name="child_comment_delete_id" 
                                value="<?= $gChildComments[$group][$j]['id'] ?>" />
                            <input id="<?= 'child_comment_delete_' . $gChildComments[$group][$j]['id'] ?>" 
                                class="child_comment_delete" name="child_comment_delete" type="submit" />
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="child_middle">
                    <p><?=es($gChildComments[$group][$j]['text'])?></p>
                </div>
                <div class="child_bottom">
                    <div class="left">
                        <!-- 좋아요 -->
                        <form action="" method="POST"
                            onsubmit="<?= 'saveCommentCookie(1, ' . $gChildComments[$group][$j]['id'] . ')'?>">
                            <label class="child_comment_likes_icon" 
                                for="<?= 'child_comment_likes_' . $gChildComments[$group][$j]['id'] ?>"></label>
                            <input type="hidden" name="comment_likes_id" value="<?= $gChildComments[$group][$j]['id'] ?>" />
                            <input id="<?= 'child_comment_likes_' . $gChildComments[$group][$j]['id'] ?>" 
                                class="child_comment_likes" name="comment_likes" type="submit" /> 
                        </form>
                        <!-- 좋아요 개수 -->
                        <p><?php
                            // 좋아요 개수가 음수인 경우에는 '-'으로 표시한다.
                            if ($gChildComments[$group][$j]['likes'] < 0) { echo '-'; }
                            else { echo numToStr($gChildComments[$group][$j]['likes']); }
                            ?></p>
                        <!-- 싫어요 -->
                        <form action="" method="POST"
                            onsubmit="<?= 'saveCommentCookie(1, ' . $gChildComments[$group][$j]['id'] . ')'?>">
                            <label class="child_comment_dislikes_icon" 
                                for="<?= 'child_comment_dislikes_' . $gChildComments[$group][$j]['id'] ?>"></label>
                            <input type="hidden" name="comment_dislikes_id" value="<?= $gChildComments[$group][$j]['id'] ?>" />
                            <input id="<?= 'child_comment_dislikes_' . $gChildComments[$group][$j]['id'] ?>" 
                                class="child_comment_dislikes" name="comment_dislikes" type="submit" /> 
                        </form>
                        <label class="detail" data-type="비추" 
                            for="<?='child_comment_dislikes_' . $gChildComments[$group][$j]['id']?>">
                            비추</label>
                        <!-- 외부인 expel --> 
                        <form action="" method="POST" class="stranger"
                            onclick="<?= 'saveCommentCookie(1, ' . $gChildComments[$group][$j]['id'] . ', 10)'?>">
                            <div class="child_comment_stranger"><label class="child_comment_stranger_icon" 
                                for="<?= 'child_comment_stranger_' . $gChildComments[$group][$j]['id'] ?>"></label></div>
                            <input type="hidden" name="comment_stranger_id" value="<?= $gChildComments[$group][$j]['id'] ?>" />
                            <input type="hidden" name="comment_stranger_userid" value="<?= $gChildComments[$group][$j]['userid'] ?>" />
                            <input id="<?= 'child_comment_stranger_' . $gChildComments[$group][$j]['id'] ?>" 
                                class="child_comment_stranger" name="comment_stranger" type="submit" /> 
                        </form>
                        <label class="detail" data-type="신고" 
                            for="<?='child_comment_stranger_' . $gChildComments[$group][$j]['id']?>">
                            신고</label>
                    </div>
                </div>
            </div>
            <!-- 대댓글 -->
            <?php endfor; ?>

        </div>
        <!-- 대댓글을 감싼다 -->

        <div class="child_comment_nav" id="<?= 'child_comment_nav_' . $gBaseComments[$i]['id'] ?>">
            <?php if ($j_page != 0 && $j_page != 1) : ?>
            <a class="first" href="<?= 'generalview?category=' . $_GET['category'] . '&id=' . $_GET['id'] 
                . '&i=' . ($_GET['i'] ?? 0) . '&j_' . $gBaseComments[$i]['id'] . '=0' ?>" 
                onclick="<?= 'saveCommentCookie(0, ' . $gBaseComments[$i]['id'] . ')'?>"><< 처음</a>
            <?php endif; ?>
            <?php if ($j_page != 0) : ?>
            <a href="<?= 'generalview?category=' . $_GET['category'] . '&id=' . $_GET['id'] . '&i=' . ($_GET['i'] ?? 0) 
                . '&j_' . $gBaseComments[$i]['id'] . '=' . ($_GET[ 'j_' . $gBaseComments[$i]['id'] ] - 1) ?>"
                onclick="<?= 'saveCommentCookie(0, ' . $gBaseComments[$i]['id'] . ')'?>" >< 이전</a>
            <?php endif; ?>
            <?php if ($j_next) : ?>
            <a class="right" href="<?= 'generalview?category=' . $_GET['category'] . '&id=' . $_GET['id'] . '&i=' . ($_GET['i'] ?? 0) 
                . '&j_' . $gBaseComments[$i]['id'] . '=' . ($_GET[ 'j_' . $gBaseComments[$i]['id'] ] + 1) ?>"
                onclick="<?= 'saveCommentCookie(0, ' . $gBaseComments[$i]['id'] . ')'?>" >다음 ></a>
            <?php endif; ?>
        </div>

    </div> 
    <!-- 댓글과 대댓글을 감싼다 -->
    <?php endfor; ?>

</div>

<div id="generalview_comments_nav">
    <?php if ($i_page != 0 && $i_page != 1) : ?>
    <a class="first" href="<?= 'generalview?category=' . $_GET['category'] . '&id=' . $_GET['id'] . '&i=0' ?>"><< 처음</a>
    <?php endif; ?>
    <?php if ($i_page != 0) : ?>
    <a href="<?= 'generalview?category=' . $_GET['category'] . '&id=' . $_GET['id'] . '&i=' . ($_GET['i'] - 1) ?>">< 이전</a>
    <?php endif; ?>
    <?php if ($i_next) : ?>
    <a class="right" href="<?= 'generalview?category=' . $_GET['category'] . '&id=' . $_GET['id'] . '&i=' . ($_GET['i'] + 1) ?>">다음 ></a>
    <?php endif; ?>
</div>
