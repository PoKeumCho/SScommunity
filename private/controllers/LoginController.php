<?php

require_once __DIR__ . '/' . '../includes/lib/util.php';
require_once __DIR__ . '/' . '../includes/lib/genRandStr.php';
require_once __DIR__ . '/' . '../includes/mail/sendMail.php';

class LoginController {

    private $CSS_LOCATION;
    private $JS_LOCATION;

    private $tmpUserTable;
    private $userTable;
    private $withdrawalTable;
    private $authentication;

    public function __construct(string $CSS_LOCATION, string $JS_LOCATION, 
                                    DatabaseTable $tmpUserTable, DatabaseTable $userTable, DatabaseTable $withdrawalTable, 
                                    Authentication $authentication) {
        $this->CSS_LOCATION = $CSS_LOCATION; 
        $this->JS_LOCATION = $JS_LOCATION;

        $this->tmpUserTable = $tmpUserTable;
        $this->userTable = $userTable;
        $this->withdrawalTable = $withdrawalTable;

        $this->authentication = $authentication;
    }

    /**
     *  로그인 (폼 출력) 
     */
    public function login() {
        $addStyle = <<<_END
            <link rel="stylesheet" href="{$this->CSS_LOCATION}/login.styles.css">
_END;
      
        $title = '로그인';    

        return [
            'addStyle' => $addStyle,
            'title' => $title,
            'template' => 'login.html.php'
        ];
    }

    /**
     *  로그인 기능 수행
     */
    public function processLogin() {
        if ($this->authentication->login($_POST['userID'], $_POST['userPW'])) { // 로그인 성공 시
            header('location: ../../index.php');
        }
        else {  // 로그인 실패 시
            // 로그인 폼을 다시 출력한다.
            $templateVariable = $this->login();
            // 오류 메시지를 추가한다.
            $templateVariable['variables'] = [ 'error' => '아이디 또는 비밀번호가 잘못 입력되었습니다.' ];
            return $templateVariable;
        }
    }


    /**
     *  회원가입 : 이용약관
     */
    public function terms() {
        $addStyle = <<<_END
            <link rel="stylesheet" href="{$this->CSS_LOCATION}/terms.styles.css">
_END;
    
        $addScriptFile = <<<_END
            <script src="{$this->JS_LOCATION}/terms_onload.js"></script>
_END;
    
        $title = '회원가입 : 이용약관';    

        return [
            'addStyle' => $addStyle,
            'addScriptFile' => $addScriptFile,
            'title' => $title,
            'template' => 'terms.html.php'
        ];
    }


    /**
     *  회원가입
     */
    public function join() {
        $errors = [];

        if (isset($_POST['user'])) {  // 폼이 제출된 경우,
            $user = $_POST['user']; 
            
            // 폼 오류 검증
            $result = $this->joinUser($user);
            $valid = $result['valid'];
            $errors = $result['errors'];
             
            // $valid가 true인 경우 임시 회원 데이터베이스에 저장한다.
            if ($valid) {
                /* 데이터베이스에 저장하는 코드 구현 */
                unset($user['pw_confirm']);

                // 한 자리수로만 입력한 경우 앞에 0을 붙여준다. 
                if (preg_match('/^[1-9]$/', $user['birthdate']['date']) === 1) {
                    $user['birthdate']['date'] = '0' . $user['birthdate']['date'];
                }
                $user['birthdate'] = $user['birthdate']['year'] . '-' . $user['birthdate']['month'] . '-' . $user['birthdate']['date'];

                // 데이터베이스에 저장하기 전에 비밀번호를 해시화
                $user['pw'] = password_hash($user['pw'], PASSWORD_BCRYPT);

                // 본인 인증 코드 생성 후 저장
                $user['code'] = mt_rand(10000000, 99999999);    // 임의의 8자리 숫자를 생성한다.

                $this->tmpUserTable->insert($user);

                // 본인 인증 이메일을 전송한다.
                sendMail(
                            NAVER_SMTP,                                                         // SMTP server
                            CHOPOKEUM96_NAVER, CHOPOKEUM96_NAVER_PW, SITE_NAME,                 // 발신자 정보
                            $user['email'], $user['name'],                                      // 수신자 정보
                            '성신 커뮤니티 본인 인증',                                          // 제목
                            [ 
                                'html' => '인증번호  :   <b>' . $user['code'] . '</b>'          // 본문
                            ]
                        );

                header('location: verifyemail?email=' . $user['email']);
            }
        }

        // 폼을 제출하지 않거나, 제출한 폼의 데이터가 유효하지 않은 경우,
        // 회원가입 폼을 출력한다
        $addStyle = <<<_END
            <link rel="stylesheet" href="{$this->CSS_LOCATION}/join.styles.css">
_END;

        $addScriptFile = <<<_END
            <script src="{$this->JS_LOCATION}/join_onload.js"></script>
            <script src="{$this->JS_LOCATION}/xhr.js"></script>
_END;
            
        $title = '회원가입';    
            
        return [
            'addStyle' => $addStyle,
            'addScriptFile' => $addScriptFile,
            'title' => $title,
            'template' => 'join.html.php',
            'variables' => [
                'errors' => $errors,
                'user' => $user
            ]
        ];
    }

    /**
     *  회원가입 폼 오류 검증
     *  검증 결과를 반환한다.
     */
    private function joinUser(array $user) {
        $valid = true;      // 데이터는 처음부터 유효하다고 가정
        $errors = [];       // 오류 메시지
        
        /* 아이디 */
        $errors['id'] = [];
        $id_pattern = '/^[a-z0-9]{5,16}$/u';
        if (empty($user['id'])) {   
            $valid = false;
            $errors['id'][] = '아이디를 입력해야 합니다.';
        } else {
            if (preg_match($id_pattern, $user['id']) !== 1) {
                $valid = false;
                $errors['id'][] = '아이디는 5~16자의 영문 소문자, 숫자만 사용 가능합니다.';
            } 
            // 중복된 아이디가 존재하는 경우
            else if (count($this->userTable->find('id', $user['id'])) > 0 
                        || count($this->tmpUserTable->find('id', $user['id'])) > 0) {
                $valid = false;
                $errors['id'][] = '이미 사용 중인 아이디입니다.';
            } 
            // 회원탈퇴한 사용자의 아이디로 존재하는 경우
            else if (count($this->withdrawalTable->find('userid', $user['id'])) > 0) {
                $valid = false;
                $errors['id'][] = '사용할 수 없는 아이디입니다.';
            }
        }
        
        /* 비밀번호 */
        $errors['pw'] = [];
        $pw_pattern = '/^[\w!@#$%^&*]{8,16}$/u';
        if (empty($user['pw'])) {
            $valid = false;
            $errors['pw'][] = '비밀번호를 입력해야 합니다.';
        } else {
            if (preg_match($pw_pattern, $user['pw']) !== 1) {
                $valid = false;
                $errors['pw'][] = '8~16자 영문 대 소문자, 숫자, 특수문자(!@#$%^&*)를 사용하세요.'; 
            }
        }

        /* 비밀번호 재확인 */
        $errors['pw_confirm'] = [];
        if (empty($user['pw_confirm'])) {
            $valid = false;
            $errors['pw_confirm'][] = '비밀번호를 재입력해야 합니다.';
        } else {
            if ($user['pw_confirm'] !== $user['pw']) {  // 입력한 비밀번호가 다른 경우
                $valid = false;
                $errors['pw_confirm'][] = '비밀번호가 일치하지 않습니다.';
            }
        }

        /* 이름 */
        $errors['name'] = [];
        $name_pattern = '/^([\xE0-\xFF][\x80-\xFF][\x80-\xFF]){2,6}$/';
        if (empty($user['name'])) {
            $valid = false;
            $errors['name'][] = '이름을 입력해야 합니다.';
        } else {
            if (preg_match($name_pattern, $user['name']) !== 1) {
                $valid = false;
                $errors['name'][] = '한글 이름(최대 6자)을 입력해 주세요.';
            }
        }

        /* 생년월일 */
        $errors['birthdate'] = [];
        $year_pattern = '/^[12][0-9]{3}$/';
        $date_pattern = '/^([0][1-9]|[1-9]|[12][0-9]|3[01])$/';
        
        if (empty($user['birthdate']['year'])) {
            $valid = false;
            $errors['birthdate'][] = 'YEAR 4자리를 입력해야 합니다.';
        } else {
            if (preg_match($year_pattern, $user['birthdate']['year']) !== 1) {
                $valid = false;
                $errors['birthdate'][] = 'YEAR 올바르지 않은 형식입니다.';
            }
        }

        if (empty($user['birthdate']['month'])) {
            $valid = false;
            $errors['birthdate'][] = 'MONTH를 선택해야 합니다.';
        }

        if (empty($user['birthdate']['date'])) {
            $valid = false;
            $errors['birthdate'][] = 'DATE를 입력해야 합니다.';
        } else {
            if (preg_match($date_pattern, $user['birthdate']['date']) !== 1) {
                $valid = false;
                $errors['birthdate'][] = 'DATE 올바르지 않은 형식입니다.';
            }
        }

        /* 닉네임 */
        $errors['nickname'] = [];
        if (empty($user['nickname'])) {
            $valid = false;
            $errors['nickname'][] = '닉네임을 입력해야 합니다.';
        } else {
            if (utf8_strlen($user['nickname']) > 8) {   // 8자를 초과하는 경우
                $valid = false;
                $errors['nickname'][] = '최대 8자까지만 가능합니다.';
            }
        }

        /* 학번 */
        $errors['studentid'] = [];
        $studentid_pattern = '/^[\d]{8}$/u';
        if (empty($user['studentid'])) {
            $valid = false;
            $errors['studentid'][] = '학번을 입력해야 합니다.';
        } else {
            if (preg_match($studentid_pattern, $user['studentid']) !== 1) {
                $valid = false;
                $errors['studentid'][] = '올바르지 않은 형식입니다.';
            } 
            // 중복된 학번이 존재하는 경우
            else if (count($this->userTable->find('studentid', $user['studentid'])) > 0) {
                $valid = false;
                $errors['studentid'][] = '이미 사용 중인 학번입니다. 하단에 기재된 이메일로 문의 바랍니다.';
            }
            // 본인 확인 인증을 기다리는 학번인 경우
            else if (count($this->tmpUserTable->find('studentid', $user['studentid'])) > 0) {
                $valid = false; 
                $errors['studentid'][] = '본인 인증 진행 중입니다. ( ' .
                    '<a href="verifyemail?email=' . 
                   $this->tmpUserTable->find('studentid', $user['studentid'])[0]['email'] . 
                   '">Click here</a> )';
            } 
        }

        /* 이메일 */
        $errors['email'] = [];
        if (empty($user['email'])) {
            $valid = false;
            $errors['email'][] = '이메일을 입력해야 합니다.';
        } else {
            if (utf8_strlen($user['email']) > 320) {   // 320자를 초과하는 경우
                $valid = false;
                $errors['email'][] = '유효하지 않은 이메일 주소입니다.';
            } else if (filter_var($user['email'], FILTER_VALIDATE_EMAIL) == false) {
                $valid = false;
                $errors['email'][] = '유효하지 않은 이메일 주소입니다.';
            } else if (count($this->userTable->find('email', $user['email'])) > 0) {  // 중복된 이메일이 존재하는 경우
                $valid = false;
                $errors['email'][] = '이미 사용 중인 이메일입니다. 하단에 기재된 이메일로 문의 바랍니다.';
            } else if (count($this->tmpUserTable->find('email', $user['email'])) > 0) { // 본인 확인 인증을 기다리는 이메일인 경우
                $valid = false; 
                $errors['email'][] = '본인 인증 진행 중입니다. ( ' .
                    '<a href="verifyemail?email=' . $user['email'] . '">Click here</a> )';
            } 
        }

        return [
            'valid' => $valid,
            'errors' => $errors
        ];
    }

    /**
     *  회원가입 : 이메일 확인
     */
    public function verifyemail() {
        $valid = true;
        $errors = [];

        if (isset($_POST['code'])) {  // 폼이 제출된 경우,

            // 불필요한 공백이나 개행 제거
            $_POST['code'] = trim($_POST['code']);
            
            // 폼 오류 검증
            $user = $this->tmpUserTable->findById($_GET['email']);

            if (!$user) {
                $valid = false;
                $errors[] = '본인 인증 진행 중인 이메일에 해당하지 않습니다.';
            } else {
                if ($user['code'] != $_POST['code']) {
                    $valid = false;
                    $errors[] = '잘못된 인증번호입니다.';

                    // 3번만 입력 가능하도록 설정한다. 
                    $joinEmailVerifyCount = $_COOKIE['joinEmailVerifyCount'] ?? 0;
                    if ($joinEmailVerifyCount > 1) {
                        setcookie('joinEmailVerifyCount', '', time()-3600);     // 쿠키 삭제
                        $this->tmpUserTable->delete($_GET['email']);            // 임시 회원 테이블에서 제거
                        header('location: join');
                        exit();
                    }
                    setcookie('joinEmailVerifyCount', $joinEmailVerifyCount + 1);
                }
            }
             
            // $valid가 true인 경우 데이터베이스에 저장한다.
            if ($valid) {
                /* 데이터베이스에 저장하는 코드 구현 */
                $this->userTable->insert([
                    'id' => $user['id'],
                    'pw' => $user['pw'],
                    'name' => $user['name'],
                    'birthdate' => $user['birthdate'],
                    'issungshin' => 'N',
                    'ssexpel' => 0,
                    'nickname' => $user['nickname'],
                    'studentid' => $user['studentid'],
                    'email' => $user['email'],
                    'accountimgid' => 1
                ]);

                // 임시 회원 테이블에서 제거
                $this->tmpUserTable->delete($_GET['email']);

                header('location: ../../index.php');
            }
        }

        // 폼을 제출하지 않거나, 제출한 폼의 데이터가 유효하지 않은 경우,
        // 회원가입 폼을 출력한다
        $addStyle = <<<_END
            <link rel="stylesheet" href="{$this->CSS_LOCATION}/verifyemail.styles.css">
_END;
    
        $title = '회원가입 : 이메일 확인';    

        return [
            'addStyle' => $addStyle,
            'title' => $title,
            'template' => 'verifyemail.html.php',
            'variables' => [
                'errors' => $errors
            ]
        ];
    }


    /**
     *  아이디 찾기
     */
    public function findid() {

        /* POST */
        if (isset($_POST['name'])) {        // 폼이 제출된 경우
            $isValid = false;

            // 이메일로 본인 인증 진행
            if (empty($_POST['studentid'])) {
                $userInfo = $this->userTable->findAndOptionDistinct([
                    'name' => $_POST['name'],
                    'email' => $_POST['email']
                ], [ 'id', 'name', 'email' ]);
                if ($userInfo) {
                    $isValid = true;
                } else {
                    header('location: findmsg?mode=fail');
                }  
            } 
            // 학번으로 본인 인증 진행
            else {
                $userInfo = $this->userTable->findAndOptionDistinct([
                    'name' => $_POST['name'],
                    'studentid' => $_POST['studentid']
                ], [ 'id', 'name', 'email' ]);
                if ($userInfo) {
                    $isValid = true;
                } else {
                    header('location: findmsg?mode=fail');
                }  
            }

            if ($isValid) {
                // 아이디 정보 이메일을 전송한다.
                $this->sendFindEmail('id', $userInfo[0]['email'], $userInfo[0]['name'], $userInfo[0]['id']);
                header('location: findmsg?mode=success&email=' . emailSecureString($userInfo[0]['email']));
            }
        }

        /* GET */
        $addStyle = <<<_END
            <link rel="stylesheet" href="{$this->CSS_LOCATION}/findcommon.styles.css">
_END;
    
        $addScriptFile = <<<_END
            <script src="{$this->JS_LOCATION}/jquery.js"></script>
            <script src="{$this->JS_LOCATION}/findcommon_jquery.js"></script>
_END;
    
        $title = '아이디 찾기';

        return [
            'addStyle' => $addStyle,
            'addScriptFile' => $addScriptFile,
            'title' => $title,
            'template' => 'find_common.html.php',
            'variables' => [
                'findCommonType' => 'id'
            ]
        ];
    }

    /**
     *  비밀번호 찾기
     */
    public function findpw() {

        /* POST */
        if (isset($_POST['id'])) {        // 폼이 제출된 경우
            $isValid = false;

            // 이메일로 본인 인증 진행
            if (empty($_POST['studentid'])) {
                $userInfo = $this->userTable->findAndOptionDistinct([
                    'id' => $_POST['id'],
                    'name' => $_POST['name'],
                    'email' => $_POST['email']
                ], [ 'id', 'name', 'email' ]);
                if ($userInfo) {
                    $isValid = true;
                } else {
                    header('location: findmsg?mode=fail');
                }  
            } 
            // 학번으로 본인 인증 진행
            else {
                $userInfo = $this->userTable->findAndOptionDistinct([
                    'id' => $_POST['id'],
                    'name' => $_POST['name'],
                    'studentid' => $_POST['studentid']
                ], [ 'id', 'name', 'email' ]);
                if ($userInfo) {
                    $isValid = true;
                } else {
                    header('location: findmsg?mode=fail');
                }  
            }

            if ($isValid) {
                // 임시 비밀번호를 생성한다.
                $tmpPw = generate_string(10);

                $this->userTable->update([
                    'primaryKey' => $userInfo[0]['id'],
                    'pw' => password_hash($tmpPw, PASSWORD_BCRYPT)
                ]);

                // 비밀번호 정보 이메일을 전송한다.
                $this->sendFindEmail('pw', $userInfo[0]['email'], $userInfo[0]['name'], $tmpPw);
                header('location: findmsg?mode=success&email=' . emailSecureString($userInfo[0]['email']));
            }
        }

        /* GET */
        $addStyle = <<<_END
            <link rel="stylesheet" href="{$this->CSS_LOCATION}/findcommon.styles.css">
_END;
    
        $addScriptFile = <<<_END
            <script src="{$this->JS_LOCATION}/jquery.js"></script>
            <script src="{$this->JS_LOCATION}/findcommon_jquery.js"></script>
_END;
    
        $title = '비밀번호 찾기';

        return [
            'addStyle' => $addStyle,
            'addScriptFile' => $addScriptFile,
            'title' => $title,
            'template' => 'find_common.html.php',
            'variables' => [
                'findCommonType' => 'pw'
            ]
        ];
    }

    /**
     *  아이디/비밀번호 찾기 알림 메시지 창
     */
    public function findMsg() {
        $addStyle = <<<_END
            <link rel="stylesheet" href="{$this->CSS_LOCATION}/findmsg.styles.css">
_END;
    
        $title = '아이디/비밀번호 찾기 : 알림';

        return [
            'addStyle' => $addStyle,
            'title' => $title,
            'template' => 'findmsg.html.php',
            'variables' => [
            ]
        ];
    }

    /**
     *  [아이디/비밀번호 찾기] 이메일을 전송하는 helper 함수
     */
    private function sendFindEmail(string $type, string $email, string $name, $findValue) {
        if ($type == 'id') {
            $typeStr = '아이디';
        } else if ($type == 'pw') {
            $typeStr = '임시 비밀번호';
        }

        sendMail(GMAIL_SMTP, STUDYHARDWORKOUT_GMAIL, STUDYHARDWORKOUT_GMAIL_PW, SITE_NAME,
                    $email, $name, '성신 커뮤니티 ' . $typeStr, [
                        'html' => '<div 
                                style="width: 50%; height: 30px;     
                                padding: 10px;     
                                border: 1px solid black;     
                                background-color: #d6cee2;">
                                    <p style="line-height: 30px; font-size: 1.2em;">' .
                            $name . '님의 ' . $typeStr . '는 [<b>' . $findValue . 
                            '</b>] 입니다.</p></div>'
                    ]);
    }


} // END OF 'LoginController' CLASS

?>
