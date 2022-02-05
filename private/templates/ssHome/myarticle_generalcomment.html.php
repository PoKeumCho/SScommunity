<?php for ($i = ($i_page * $contentPerPage); $i < $i_max; $i++): ?>
<?php if ($contentList[$i]['class'] == 0):  // 댓글 ?>
<a href="<?='generalview?category=' . $contentList[$i]['categoryid'] . '&id=' . $contentList[$i]['generalid'] . 
                '&i=' . $contentList[$i]['page']?>" >
<?php else: // 대댓글 ?>
<a href="<?='generalview?category=' . $contentList[$i]['categoryid'] . '&id=' . $contentList[$i]['generalid'] . 
                '&i=' . $contentList[$i]['page'] . '&j_' . $contentList[$i]['baseid'] . '=0' ?>" >
<?php endif; ?>
    <div class="date_wrap">
        <p><?=datetimeFormatted($contentList[$i]['date'])?></p>
    </div>
    <div class="text_wrap">
        <p><?=es($contentList[$i]['text'])?></p>
    </div>
    <div class="category_wrap">
        <p><?=es($contentList[$i]['category'])?></p>
    </div>
    <div class="notice_wrap">
        <div class="likes_wrap">
            <img src="./img/myarticle/like.png" width="<?=strpos($_SERVER['REQUEST_URI'], 'Mobile') ? 16 : 20?>"
                    height="<?=strpos($_SERVER['REQUEST_URI'], 'Mobile') ? 16 : 20?>" />
            <p><?php
                // 좋아요 개수가 음수인 경우에는 '-'으로 표시한다.
                if ($contentList[$i]['likes'] < 0) { echo '-'; }
                else { echo numToStr($contentList[$i]['likes']); }
                ?></p>
        </div>
    </div>
</a>
<?php endfor; ?>
