<!DOCTYPE html>
<html lang="ko">
    <head>
        <meta charset="UTF-8">

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
        <!-- [중요] 경로의 기준은 해당 템플릿을 include 하는 public/login/~ 가 된다. -->
        <!-- ------------------------------------------------------------------------ --> 
        <!-- 스타일 시트 초기화 -->    
        <link rel="stylesheet" href="./css/cssReset.css">    
    
        <link rel="stylesheet" href="./css/layout.styles.css">    
        <!-- ------------------------------------------------------------------------ --> 

        <!-- 추가할 스타일 시트 -->
        <?=$addStyle?>

        <!-- 추가할 자바스크립트 -->
        <?=$addScriptFile?> 

        <title><?=$title?></title>
    </head>
    <body>
        <!-- 1행 -->
        <header id="login_header">
            <h1>SScommunity</h1>
        </header>
        <!-- 2행 -->
        <div id="login_content">
            <?=$content?>
        </div>
        <!-- 3행 -->
        <footer id="login_footer">
            <?php include __DIR__ . '/../../includes/html/footer.html.php'; ?>
        </footer>
    </body>
</html>
