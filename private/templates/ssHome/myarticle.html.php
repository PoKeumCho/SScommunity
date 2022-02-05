<div class="navWrap">
    <a href="?select=general" id="select_general">글</a>
    <a href="?select=generalcomment" id="select_generalcomment">댓글</a>
    <a href="?select=trade" id="select_trade">중고거래</a>
    <div class="placeholder"></div>
</div>

<div id="myarticle_content">
    <?php
    // 한 페이지에 포함하는 글의 개수
    switch($_GET['select']) {
        case 'general':
            $contentPerPage = 10;
            break;
        case 'generalcomment':
            $contentPerPage = 15;
            break;
        case 'trade':
            $contentPerPage = 5;
            break;
    }
    $i_page = (int)$_GET['i'] ?? 0;

    if ( ($i_page * $contentPerPage + $contentPerPage) >= count($contentList) ) {
        $i_max = count($contentList);
        $i_next = false;  // 다음 페이지 존재 여부
    } else {
        $i_max = $i_page * $contentPerPage + $contentPerPage;
        $i_next = true;  // 다음 페이지 존재 여부
    }

    include_once __DIR__ . '/myarticle_' . $_GET['select'] . '.html.php';
    ?>
</div>

<div id="myarticle_content_nav">
    <?php if ($i_page != 0 && $i_page != 1) : ?>
    <a class="first" href="<?='myarticle?select=' . $_GET['select'] . '&i=0'?>"><< 처음</a>
    <?php endif; ?>
    <?php if ($i_page != 0) : ?>
    <a href="<?='myarticle?select=' . $_GET['select'] . '&i=' . ((int)$_GET['i']-1)?>" >< 이전</a>
    <?php endif; ?>
    <?php if ($i_next) : ?>
    <a class="right" href="<?='myarticle?select=' . $_GET['select'] . '&i=' . ((int)$_GET['i']+1)?>">다음 ></a>
    <?php endif; ?>
</div>
