<?php

class Authentication {
    private $userTable;     // 사용자 계정 테이블을 처리할 DatabaseTable 인스턴스
    private $idColumn;      // 로그인 아이디가 저장된 칼럼명
    private $pwColumn;      // 로그인 비밀번호가 저장된 칼럼명

    public function __construct(DatabaseTable $userTable, string $idColumn, string $pwColumn)
    {
        $this->userTable = $userTable;
        $this->idColumn = $idColumn;
        $this->pwColumn = $pwColumn;
    }

    /**
     *  로그인 성공 여부를 반환한다.
     */
    public function login($id, $pw) {
        $user = $this->userTable->find($this->idColumn, strtolower($id));
        
        if (!empty($user)                                               // 해당 아이디를 가진 사용자가 존재하지 않거나
            && password_verify($pw, $user[0][$this->pwColumn])) {       // 비밀번호가 다른 경우

            // 사용자가 로그인하기 전에 이미 세션 ID가 유출됐을 경우를 대비해,
            // 로그인 후 세션 ID를 교체한다. 
            session_regenerate_id();    

            $_SESSION['id'] = $id;
            $_SESSION['pw'] = $user[0][$this->pwColumn]; 
            return true;
        } else {
            return false;
        }
    }

    /**
     *  현재 로그인되어 있는지 여부를 반환한다.
     */
    public function isLoggedIn() {
        if (empty($_SESSION['id'])) {
            return false;
        }

        $user = $this->userTable->find($this->idColumn, strtolower($_SESSION['id']));

        if (!empty($user) && $user[0][$this->pwColumn] === $_SESSION['pw']) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *  로그인 여부를 확인하고 사용자 정보 레코드를 배열로 반환한다.
     */
    public function getUser() {
        if ($this->isLoggedIn()) {
            return $this->userTable->find($this->idColumn, strtolower($_SESSION['id']))[0];
        } else {
            return false;
        }
    }

    /** [ 프로젝트 전용 코드 ]
     *  성신 인증되어 있는지 여부를 반환한다.
     */
    public function isSungshin() {
        if (empty($_SESSION['id'])) {
            return false;
        }

        $user = $this->userTable->find($this->idColumn, strtolower($_SESSION['id']));

        if (!empty($user) && $user[0][$this->pwColumn] === $_SESSION['pw'] 
                && $user[0]['issungshin'] === 'Y') {
            return true;
        } else {
            return false;
        }
    }

} // END OF 'Authentication' CLASS

?>
