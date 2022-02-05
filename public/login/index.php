<?php
session_start();


//================================================================================================//
// URL을 이용해 action(액션)을 결정하는 route(라우터)값을 설정하고, 
// URL 경로가 올바른지 확인한다.
//================================================================================================//
$route = str_replace('/public/login/', '', strtok($_SERVER['REQUEST_URI'], '?'));

if ($route == strtolower($route)) {
    if ($route === '') {
        $route = 'login';
    }
} else {
    http_response_code(301);
    header('location: /public/login/' . strtolower($route));
}


//================================================================================================//
// REST 방식으로 경로를 제어한다.
//================================================================================================//

include_once __DIR__ . '/' . '../../private/includes/REST/login.php';

//================================================================================================//


// Template(템플릿)을 불러오는 코드를 별도의 함수로 분리한다.
function loadTemplate($templateFileName, array $variables = []) {
    extract($variables);
    
    ob_start();        
    include __DIR__ . $templateFileName;        

    return ob_get_clean();   
}


try {
    /* 필수 파일 불러오기 */
    include_once __DIR__ . '/' . '../../private/includes/file_location/login.php';
    include_once __DIR__ . '/' . '../../private/includes/db/DatabaseConnection.php';
    include_once __DIR__ . '/' . '../../private/classes/DatabaseTable.php';
    include_once __DIR__ . '/' . '../../private/classes/Authentication.php';
    include_once __DIR__ . '/' . '../../private/controllers/LoginController.php';


    /* 데이터베이스 테이블 인스턴스 생성 */
    $tmpUserTable = new DatabaseTable($pdo, 'tmpuser', 'email');
    $userTable = new DatabaseTable($pdo, 'user', 'id');
    $withdrawalTable = new DatabaseTable($pdo, 'withdrawal', 'userid');

    /* 로그인 기능 구현 인스턴스 생성 */
    $authentication = new Authentication($userTable, 'id', 'pw'); 

    /*
     * 페이지 기능을 수행하고 
     * $addStyle, $addScript, $title, $content 변수 생성
     */
    $loginController = new LoginController($CSS_LOCATION, $JS_LOCATION, 
                                            $tmpUserTable, $userTable, $withdrawalTable, $authentication);

    $method = $_SERVER['REQUEST_METHOD'];
    $action = $ROUTES[$route][$method]['action'];
    $page = $loginController->$action();

    $addStyle = $page['addStyle'] ?? '';
    $addScriptFile = $page['addScriptFile'] ?? '';
    $title = $page['title'] ?? '';

    $templateFileName = "/{$TEMPLATES_LOCATION}/" . $page['template'];

    // 템플릿에서 사용되는 변수들을 Controller(컨트롤러) 반환 배열의 variables 키로 추가한다.
    if (isset($page['variables'])) {
        $content = loadTemplate($templateFileName, $page['variables']);
    } else {
        $content = loadTemplate($templateFileName);
    }
}
catch (PDOException $e) {
    $title = '오류가 발생했습니다.';    
    $content = '데이터베이스 오류: ' . $e->getMessage() . '<br/>' .    
          '위치: ' . $e->getFile() . ' : ' . $e->getLine();  
}

include __DIR__ . "/{$TEMPLATES_LOCATION}/layout.html.php";
