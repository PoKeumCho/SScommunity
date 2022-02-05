<?php
$tradeContentList = $_SESSION['tradeContentList'];
$tradeImgFileList = $_SESSION['tradeImgFileList'];

$page = $_COOKIE['trade_page'];
$imgListPointer = $_COOKIE['trade_imgListPointer'];

$displayNum = 20;       // 한 번에 보여줄 글 개수
$maxImgNum = 3;         // 업로드 가능한 이미지 최대 개수

// 아직 출력되지 않은 글 개수
$remainContentNum = count($tradeContentList) - ($page * $displayNum);

// 현재 출력할 글 개수 
$maxDisplayNum = $remainContentNum < $displayNum ? $remainContentNum : $displayNum; 

for ($i = ($page * $displayNum); $i < ($page * $displayNum) + $maxDisplayNum; $i++):
?>
<div class="content">
    <div class="left">
        <div class="content_imgWrap" onclick="showSlides(this)">
            <?php 
            $curPointer = $imgListPointer; 
            for ($j = 0; $j < $maxImgNum; $j++) {
                if ($tradeImgFileList[$curPointer + $j]['id'] === $tradeContentList[$i]['id']) {
                    $imgElem = '<img src="../../file/images/trade/' . $tradeImgFileList[$curPointer + $j]['path'] . '"';
                    if ($j != 0) { $imgElem .= ' class="hidden"'; } 
                    else { $imgElem .= ' class=""'; }
                    $imgElem .= '/>';

                    echo $imgElem;

                    $imgListPointer++;
                } else { break; }
            }
            ?>
        </div>
        <div class="content_dotWrap">
            <?php for ($k = 1; $k < $j; $k++): ?>
            <span class="<?= ($k == 1) ? 'dot select' : 'dot' ?>"></span>
            <?php endfor; ?>
            <?php if ($k != 1): ?>
            <span class="dot"></span>
            <?php endif; ?>
        </div>
        <div class="content_priceWrap">
            <p><?=priceCommaFormatted($tradeContentList[$i]['price'])?> 원</p> 
        </div>
        <div class="content_expelWrap" 
            onclick="<?="contentExpelProcess(" . $tradeContentList[$i]['id'] . ")"?>">
            <p>부적절한 글 신고  &#128680;</p>
        </div>
    </div>
    <div class="right">
        <div class="content_titleWrap"
            <?php if ($_SESSION['id'] != $tradeContentList[$i]['userid']): ?>
            onclick="<?="openChat('" . $_SESSION['id'] . "', '" . $_SESSION['pw'] . 
                                    "', '" . $tradeContentList[$i]['userid'] . "')"?>"
            <?php endif; ?>>
            <p><?=es($tradeContentList[$i]['title'])?></p>
        </div>
        <div class="content_dateWrap">
            <p><?=datetimeFormatted($tradeContentList[$i]['date'])?></p>
        </div>
        <div class="content_campusWrap">
            <p class="campus"><?php
                if ($tradeContentList[$i]['campus'] == 'S') { echo '수정 캠퍼스'; }
                else if ($tradeContentList[$i]['campus'] == 'U') { echo '운정 캠퍼스'; }
                else { echo '수정 / 운정 캠퍼스'; }
            ?></p>
        </div>
        <div class="content_infoWrap">
            <p><?=es($tradeContentList[$i]['info'])?></p>
        </div>
        <div class="content_categoryWrap">
            <p><?=es($tradeContentList[$i]['category'])?></p>
        </div>
    </div>
</div>
<?php endfor; ?>
<span id="page" data-value="<?=$page?>"></span>
<span id="imgListPointer" data-value="<?=$imgListPointer?>"></span>
