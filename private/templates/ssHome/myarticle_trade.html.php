<?php for ($i = ($i_page * $contentPerPage); $i < $i_max; $i++): ?>
<article 
    id="<?='article_' . $contentList[$i]['id']?>" 
    data-id="<?=$contentList[$i]['id']?>">
    <div class="top">
        <div class="left">
            <img src="<?='../../file/images/trade/' . $contentList[$i]['path']?>" />
        </div>
        <div class="right">
            <p class="title"><?=es($contentList[$i]['title'])?></p>
            <p class="date" data-date="<?=$contentList[$i]['date']?>">
                <?=datetimeFormatted($contentList[$i]['date'])?>
            </p>
            <p class="campus"><?=$contentList[$i]['campus']?></p>
            <p class="category"><?=$contentList[$i]['category']?></p>
            <a data-id="trade_changePrice" class="content_priceWrap submit" href="#">
                <p class="price" data-price="<?=$contentList[$i]['price']?>">
                    <?=priceCommaFormatted($contentList[$i]['price'])?> 원
                </p>
            </a>
        </div>
    </div>
    <div class="bottom">
        <a data-id="trade_delete" class="left submit" href="#">거래완료 (글 삭제)</a>
        <a data-id="trade_update" class="right submit" href="#">끌어올리기</a>
    </div>
</article>
<?php endfor; ?>

<form action="" method="POST">
    <input type="hidden" id="tradeId" name="tradeId" value="" />
    <input type="hidden" id="tradeOpt" name="tradeOpt" value="" />
    <input type="hidden" id="tradePrice" name="tradePrice" value="" />
    <input type="submit" id="tradeSubmit" name="submit" class="hidden" /> 
</form>
