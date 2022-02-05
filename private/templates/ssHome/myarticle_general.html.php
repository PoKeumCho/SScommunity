<?php for ($i = ($i_page * $contentPerPage); $i < $i_max; $i++): ?>
<a href="<?='generalview?category=' . $contentList[$i]['categoryid'] . '&id=' . $contentList[$i]['id']?>">
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
        <?php if ($contentList[$i]['img'] > 0): ?>
        <div class="image_wrap">
            <img src="./img/myarticle/image.png" width="<?=strpos($_SERVER['REQUEST_URI'], 'Mobile') ? 16 : 20?>"
                    height="<?=strpos($_SERVER['REQUEST_URI'], 'Mobile') ? 16 : 20?>" />
            <p><?=$contentList[$i]['img']?></p>
        </div>
        <?php endif; ?>
        <div class="likes_wrap">
            <img src="./img/myarticle/like.png" width="<?=strpos($_SERVER['REQUEST_URI'], 'Mobile') ? 16 : 20?>"
                    height="<?=strpos($_SERVER['REQUEST_URI'], 'Mobile') ? 16 : 20?>" />
            <p><?php
                // 좋아요 개수가 음수인 경우에는 '-'으로 표시한다.
                if ($contentList[$i]['likes'] < 0) { echo '-'; }
                else { echo numToStr($contentList[$i]['likes']); }
                ?></p>
        </div>
        <div class="comments_wrap">
            <img src="./img/myarticle/comment.png" width="<?=strpos($_SERVER['REQUEST_URI'], 'Mobile') ? 16 : 20?>"
                    height="<?=strpos($_SERVER['REQUEST_URI'], 'Mobile') ? 16 : 20?>" />
            <p><?=numToStr($contentList[$i]['comments'])?></p>
        </div>
    </div>
</a>
<?php endfor; ?>
