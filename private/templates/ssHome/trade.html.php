<?php
    // 세션으로 설정해서 방문자의 브라우저가 아닌 서버에 저장한다.
    $_SESSION['tradeContentList'] = $tradeContentList;
    $_SESSION['tradeImgFileList'] = $tradeImgFileList;

    setcookie('trade_page', 0, ['samesite' => 'None', 'secure' => true]);
    setcookie('trade_imgListPointer', 0, ['samesite' => 'None', 'secure' => true]);
?>

<div id="trade_wrap">

    <div id="trade_nav">
        <form action="" method="GET">
            <select id="campus_nav" name="campus">
                <option value="0">[-- 캠퍼스 --]</option>
                <option value="1">수정 캠퍼스</option>
                <option value="2">운정 캠퍼스</option>
            </select> 
            <select id="category_nav" name="category">
                <option value="0">[-- 카테고리 --]</option>
                <?php foreach ($tradeCategoryList as $key => $value): ?>
                <option value="<?=($key + 1)?>"><?=$value['category']?></option>
                <?php endforeach; ?>
            </select>
            <div id="trade_nav_subwrap">
                <input type="text" id="search_nav" name="search" 
                    autocomplete="off" value="<?= $_GET['search'] ?>" />
                <input type="submit" id="submit_nav" value="검색" />
            </div>
        </form>
    </div>

    <div id="trade_add">
        <form action="" method="POST" enctype="multipart/form-data">
            <div id="trade_add_subwrap">
                <!-- 제목 (Title) -->
                <input type="text" id="trade_title" name="trade_title" 
                    maxlength="30" placeholder="제목" />
                <div class="subDiv">
                    <!-- 카테고리 (Category)-->
                    <select id="trade_category" name="trade_category">
                        <option value="0">[-- 카테고리 --]</option>
                        <?php foreach ($tradeCategoryList as $key => $value): ?>
                        <option value="<?=($key + 1)?>"><?=$value['category']?></option>
                        <?php endforeach; ?>
                    </select>
                    <!-- 가격 (Price) -->
                    <input type="number" id="trade_price" name="trade_price" placeholder="가격 (원 단위)" />
                </div>
            </div>
            <!-- 설명 (Info) -->
            <textarea id="trade_info" name="trade_info" maxlength="65535"
                placeholder="게시글 내용을 작성해주세요." ></textarea>

            <?php   // 모바일 버전인지 확인한다.
                $isMobile = strpos($_SERVER['REQUEST_URI'], 'Mobile');
            ?>
            <div id="trade_add_wrap"> 
                <label for="add_file">
                    <div class="addImgInputWrap">
                        <img src="./img/trade/add-photo.png" 
                            width="<?=($isMobile ? 32 : 48)?>" height="<?=($isMobile ? 32 : 48)?>" />
                    </div>
                </label>
                <input type="file" id="add_file" name="add_file[]" accept="image/*" multiple hidden />
                
                <?php for ($i = 0; $i < 3; $i++):   // 이미지 미리보기 ?>
                <div class="addImgWrap">
                    <img class="defaultImg" src="./img/trade/number_<?=($i + 1)?>.png" 
                        width="<?=($isMobile ? 24 : 32)?>" height="<?=($isMobile ? 24 : 32)?>" />
                </div>
                <?php endfor; ?>

                <label for="add_submit">
                    <div class="addSubmitWrap">
                        <img src="./img/trade/edit.png" 
                            width="<?=($isMobile ? 32 : 48)?>" height="<?=($isMobile ? 32 : 48)?>" />
                    </div>
                </label>
                <input type="submit" id="add_submit" value="글 등록" hidden />

                <!-- 캠퍼스 (Campus) : 중복 선택 가능 -->
                <div id="add_campus">
                    <label>수정 <input type="checkbox" id="campusCb_1" name="campus_1" value="1"></label> 
                    <label>운정 <input type="checkbox" id="campusCb_2" name="campus_2" value="2"></label> 
                </div>
            </div>
        </form>
    </div>


    <div id="trade_content" data-count="<?=count($tradeContentList)?>" >
        <?php include_once __DIR__ . '/' . 'trade_content.html.php'; ?>
    </div>

    <?php if (count($tradeContentList) > $displayNum) : ?>
    <a id="more" href="ajax/tradeInfiniteScroll.php">more</a>
    <?php endif; ?>

    <!-- [참고] https://www.w3schools.com/howto/howto_js_scroll_to_top.asp -->
    <button onclick="topFunction()" id="gotoTopBtn" title="Go to top">Top</button> 

</div>
