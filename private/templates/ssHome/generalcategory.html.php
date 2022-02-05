<!-- /generalcategory?i=bookmark페이지 -->

<div id="gc_new">
    <div class="button_wrap">
        <div class="button">
            <a id="add_gc_button" href="#">새 게시판 만들기</a>
        </div>
        <?php
        if (isset($errors['add_gc_new'])):
        echo '<div id="add_gc_errors"><p>' . $errors['add_gc_new'][0] . '</p></div>';
        echo '<span id="add_gc_prevent_resubmit"></span>';
        endif;
        ?>
        <div id="add_gc_cancel_button_wrap" class="button">
            <a id="add_gc_cancel_button" href="#">취소</a>
        </div>
    </div>
    <div id="add_gc_new">
        <form id="add_gc_new_form" action="" method="POST">
            <label for="gc_name">이름 (필수)</label>
            <input type="text" id="gc_name" name="generalcategory[name]" maxlength="16"
                value="<?= $generalcategory['name'] ?? '' ?>" />
            <label for="gc_info">설명 (필수)</label>
            <input type="text" id="gc_info" name="generalcategory[info]" maxlength="320"
                value="<?= $generalcategory['info'] ?? '' ?>" />
            <label for="gc_hashtag">검색어 해시태그</label>
            <input type="text" id="gc_hashtag" name="generalcategory[hashtag]" maxlength="320" 
                placeholder="'#검색어#검색어#검색어...' 형식으로 입력해 주세요."
                value="<?= $generalcategory['hashtag'] ?? '' ?>" />
            <div class="checkbox_wrap">
                <label for="gc_expel">방출 기능을 추가합니다.</label>
                <input type="checkbox" id="gc_expel" name="generalcategory[expel]" value="Y" />
            </div>
        </form>
    </div>
</div>


<div id="gc_bookmark">

    <div id="gc_bookmark_list">
        
        <!-- 한 페이지에 9개 표시 -->
        <?php
        // 한 페이지에 9개 표시 (모바일의 경우 한 페이지에 6개 표시)
        $i_elem_count = strpos($_SERVER['REQUEST_URI'], 'Mobile') ? 6 : 9;
        $i_page = $_GET['i'] ?? 0;

        if ( ($i_page * $i_elem_count + $i_elem_count) > count($bookmarks) ) {
            $i_max = count($bookmarks);
            $i_next = false;  // 다음 페이지 존재 여부
        } else {
            $i_max = $i_page * $i_elem_count + $i_elem_count;
            $i_next = true;  // 다음 페이지 존재 여부
        }

        for ($i = ($i_page * $i_elem_count); $i < $i_max; $i++) : 
        ?> 
        <a href="<?= 'general?category=' . $bookmarks[$i]['id'] ?>" 
            class="gCategory <?= $bookmarks[$i]['expel'] == 'Y' ? 'expel' : '' ?>">
        <div class="left"><p><?= es($bookmarks[$i]['name']) ?></p></div>
            <div class="right">
                <div class="top">
                    <form action="" method="POST">
                        <!-- 
                            label 태그의 for 속성을 사용해서 폼을 submit 하도록 구현하였으므로,
                            input type="submit"에 id 속성을 부여했는데 동일한 id를 갖는 경우 동일한 "remove_bookmark_id" 값을 갖는다.
                            이를 해결하기 위해서 고유한 id를 부여한다. 
                        -->
                        <label for="<?= 'remove_bookmark_' . $bookmarks[$i]['id'] ?>">
                            <img id="remove_bookmark_img" src="./img/generalcategory/star_32.png" width="32" height="32" />
                        </label>
                        <input type="hidden" name="remove_bookmark_id" value="<?= $bookmarks[$i]['id'] ?>" />
                        <input type="submit" class="remove_bookmark" 
                            id="<?= 'remove_bookmark_' . $bookmarks[$i]['id'] ?>" name="remove_bookmark" />
                    </form>
                </div>
                <div class="bottom">
                    <!-- 즐겨찾기를 설정한 사용자 수 -->
                    <p><?= numToStr($bookmarks[$i]['users']) ?></p>
                </div>
            </div>
        </a> 
        <?php endfor; ?>

    </div>

    <div id="gc_bookmark_nav">
        <?php if ($i_page != 0 && $i_page != 1) : ?>
        <a class="first" href="<?= 'generalcategory?i=0' ?>"><< 처음</a>
        <?php endif; ?>
        <?php if ($i_page != 0) : ?>
        <a href="<?= 'generalcategory?i=' . ($_GET['i'] - 1) ?>">< 이전</a>
        <?php endif; ?>
        <?php if ($i_next) : ?>
        <a class="right" href="<?= 'generalcategory?i=' . ($_GET['i'] + 1) ?>">다음 ></a>
        <?php endif; ?>
    </div>

</div>


<div id="gc_search_wrap">
    <form id="gc_search_form" action="" method="POST">
        <input type="text" id="gc_search" name="search" value="<?= $search ?? '' ?>" placeholder="게시판 검색" />
        <label id="gc_search_img_wrap" for="gc_search_submit">
            <img src="./img/generalcategory/search_32.png" width="32" height="32" /> 
        </label>
        <input type="submit" id="gc_search_submit" />
    </form>
</div>


<div id="gc_search_results">

    <div class="ul">
        <!-- 검색 결과 표시 -->
        <?php
            for ($j = 0; $j < count($results); $j++) :
        ?>
        <div class="li <?= $results[$j]['expel'] == 'Y' ? 'expel' : '' ?>">
        <div class="left"><p><?= es($results[$j]['name']) ?></p></div>
            <div class="right">
                <div class="top">
                    <form action="" method="POST">
                        <label for="<?= 'add_bookmark_' . $results[$j]['id'] ?>">
                            <img id="add_bookmark_img" src="./img/generalcategory/star_empty_32.png" width="32" height="32" />
                        </label>
                        <input type="hidden" name="add_bookmark_id" value="<?= $results[$j]['id'] ?>" />
                        <input type="submit" class="add_bookmark" 
                            id="<?= 'add_bookmark_' . $results[$j]['id'] ?>" name="add_bookmark" />
                    </form>
                </div>
                <div class="bottom">
                    <!-- 즐겨찾기를 설정한 사용자 수 -->
                    <p><?= numToStr($results[$j]['users']) ?></p>
                </div>
            </div>
        </div>
        <?php endfor; ?>

        <?php if (!isset($results)) : ?>
        <div class="msg">즐겨찾기 목록에 없는 게시판 검색</div>
        <?php elseif (count($results) == 0) : ?>
        <div class="msg">검색된 게시판이 없습니다.</div>
        <?php endif; ?>
    </div>

    <?php if (isset($searchFormSubmitted)): ?>
    <span id="gc_search_prevent_resubmit"></span>
    <?php endif; ?>

</div>
