<!DOCTYPE html>
<html lang="ko">
    <head>
        <meta charset="UTF-8" />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1.0, mimimum-scale=1.0, user-scalable=no"
        />
        
        <!-- favicon (https://www.favicon-generator.org/) -->
        <link rel="apple-touch-icon" sizes="57x57" href="../favicon/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="../favicon/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="../favicon/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="../favicon/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="../favicon/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="../favicon/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="../favicon/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="../favicon/apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="../favicon/apple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="192x192"  href="../favicon/android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="../favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="../favicon/favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="../favicon/favicon-16x16.png">
        <link rel="manifest" href="../favicon/manifest.json">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="../favicon/ms-icon-144x144.png">
        <meta name="theme-color" content="#ffffff">

        <?php include __DIR__ . '/../../includes/html/googleFonts.html.php'; ?>

        <!-- ------------------------------------------------------------------------ --> 
        <!-- [중요] 경로의 기준은 해당 템플릿을 include 하는 public/Mobile/~ 가 된다. -->
        <!-- ------------------------------------------------------------------------ --> 
        <!-- 스타일 시트 초기화 -->
        <link rel="stylesheet" href="./css/cssReset.css">

        <link rel="stylesheet" href="./css/layout.styles.css">
        <!-- ------------------------------------------------------------------------- --> 

        <!-- 추가할 스타일 시트 -->
        <?=$addStyle?>

        <!-- 추가할 자바스크립트 -->
        <?=$addScriptFile?> 
        <script type="text/javascript">
            <?=$addScriptCode?>
        </script>

        <script type="text/javascript" src="./js/chat.js"></script>

        <title><?=$title?></title>
    </head>
    <body>
        <input id="toggle_menu" type="checkbox" />
        <input id="toggle_info" type="checkbox" />
        <div id="wrap">
            <!-- 1행 -->
            <header id="main_header">
                <label class="left" for="toggle_info">Toggle Info</label>
                <h1>SScommunity</h1>
                <label class="right" for="toggle_menu">Toggle Menu</label>
            </header>
            <!-- 2행 -->
            <div id="toggle_menu_gnd_wrap">
                <div id="toggle_menu_gnd">
                    <ul>
                        <li><a href="about">About</a></li>
                        <li><a href="general">게시판</a></li>
                        <li><a href="schedule">시간표</a></li>
                        <li><a href="trade">중고거래</a></li>
                    </ul>
                </div>
            </div>
            <!-- 3행 -->
            <div id="toggle_info_gnd_wrap">
                <div id="toggle_info_gnd">
                    <ul>
                        <li><a href="myinfo">내 정보</a></li>
                        <li><a href="myarticle?select=general">글 관리</a></li>
                        <li>
                            <a href="#" onclick="<?="openChat('" . $mobileChatSettings['id'] . 
                                "', '" . $mobileChatSettings['pw'] . "')"?>">채팅</a>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- 4행 -->
            <aside id="main_aside">
                <?=$aside?>
            </aside>
            <!-- 5행 -->
            <section id="main_section">
                <?=$section?>
            </section>
            <!-- 6행 -->
            <footer id="main_footer">
                <?php include __DIR__ . '/../../includes/html/footer.html.php'; ?>
            </footer>
        </div>
    </body>
</html>
