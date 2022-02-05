<!DOCTYPE html>
<html lang="ko">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
        
        <?php include __DIR__ . '/../../includes/html/googleFonts.html.php'; ?>

        <link rel="stylesheet" href="./css/cssReset.css">    
        <link rel="stylesheet" href="./css/error.styles.css">    

        <title><?=$title?></title>
    </head>
    <body>
        <!-- 1행 -->
        <div id="chat_accessError">
            <div id="error_wrap">
                <img src="./img/error/warning.png" width="64" height="64" />
                <?php if ($isLoggedIn): ?>
                <h1>성신 인증 후 이용 가능합니다.</h1>
                <?php else: ?>
                <h1>로그인 후 이용 가능합니다.</h1>
                <?php endif; ?>
            </div>
        </div>
        <!-- 2행 -->
        <footer id="chat_footer">
            <?php include __DIR__ . '/../../includes/html/footer.html.php'; ?>
        </footer>
    </body>
</html>
