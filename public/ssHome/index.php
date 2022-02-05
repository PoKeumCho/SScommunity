<?php
session_start();


//================================================================================================//
// URL을 이용해 action(액션)을 결정하는 route(라우터)값을 설정하고, 
// URL 경로가 올바른지 확인한다.
//================================================================================================//
$route = str_replace('/public/ssHome/', '', strtok($_SERVER['REQUEST_URI'], '?'));

if ($route == strtolower($route)) {
    if ($route === '') {
        $route = 'about';
    }
} else {
    http_response_code(301);
    header('location: /public/ssHome/' . strtolower($route));
}


//================================================================================================//
// REST 방식으로 경로를 제어한다.
//================================================================================================//

include_once __DIR__ . '/' . '../../private/includes/REST/ssHome.php';

//================================================================================================//


// Template(템플릿)을 불러오는 코드를 별도의 함수로 분리한다.
function loadTemplate($templateFileName, array $variables = []) {
    include_once __DIR__ . '/' . '../../private/includes/lib/util.php';     // es() 함수

    extract($variables);
    
    ob_start();        
    include __DIR__ . $templateFileName;        

    return ob_get_clean();   
}


try {
    /* 필수 파일 불러오기  */
    include_once __DIR__ . '/' . '../../private/includes/file_location/ssHome.php';
    include_once __DIR__ . '/' . '../../private/includes/db/DatabaseConnection.php';
    include_once __DIR__ . '/' . '../../private/classes/DatabaseTable.php';
    include_once __DIR__ . '/' . '../../private/classes/Authentication.php';
    include_once __DIR__ . '/' . '../../private/controllers/SsHomeController.php';

    /**
     *  - 데이터베이스 테이블 인스턴스 생성
     *  - 로그인 기능 구현 인스턴스 생성
     *  - 페이지 기능을 수행하고 $addStyle, $addScript, $title, $aside, $section 변수 생성
     */
    include_once __DIR__ . '/' . '../../private/includes/controller_setting/ssHome.php';

    // 접근 제한 페이지 설정 
    if (isset($ROUTES[$route]['login']) && $ROUTES[$route]['login'] && 
            !$authentication->isLoggedIn()) {                            
              header('location: loginerror');
    } else if (isset($ROUTES[$route]['sungshin']) && $ROUTES[$route]['sungshin'] && 
            !$authentication->isSungshin()) {                            
              header('location: sungshinerror');
    } else {
        $method = $_SERVER['REQUEST_METHOD'];
        $action = $ROUTES[$route][$method]['action'];
        $page = $ssHomeController->$action();
    }

    $addStyle = $page['addStyle'] ?? '';
    $addScriptFile = $page['addScriptFile'] ?? '';
    $addScriptCode = $page['addScriptCode'] ?? '';
    $title = $page['title'] ?? '';


    // 템플릿에서 사용되는 변수들을 Controller(컨트롤러) 반환 배열의 variables 키로 추가한다.

    // aside에 해당하는 레이아웃 템플릿은 로그인 여부에 따라 다르게 작동되도록 구현한다.
    if ($authentication->isLoggedIn()) {
        $asideTemplateFileName = "/{$TEMPLATES_LOCATION}/aside/after_login.html.php";
    } else {
        $asideTemplateFileName = "/{$TEMPLATES_LOCATION}/aside/before_login.html.php";
    }

    if (isset($page['variables']['aside'])) {
        $aside = loadTemplate($asideTemplateFileName, $page['variables']['aside']);
    } else {
        $aside = loadTemplate($asideTemplateFileName);
    }

    $sectionTemplateFileName = "/{$TEMPLATES_LOCATION}/" . $page['template'];

    if (isset($page['variables']['section'])) {
        $section = loadTemplate($sectionTemplateFileName, $page['variables']['section']);
    } else {
        $section = loadTemplate($sectionTemplateFileName);
    }
}
catch (PDOException $e) {
    $title = '오류가 발생했습니다.';    
    $section = '데이터베이스 오류: ' . $e->getMessage() . '<br/>' .    
          '위치: ' . $e->getFile() . ' : ' . $e->getLine();  
}

include __DIR__ . "/{$TEMPLATES_LOCATION}/layout.html.php";
