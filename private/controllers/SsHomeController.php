<?php

require_once __DIR__ . '/' . '../includes/lib/defineMaxExpel.php';
require_once __DIR__ . '/' . '../includes/lib/util.php';
require_once __DIR__ . '/' . '../includes/lib/modifyImage.php';


class SsHomeController {
    
    private $CSS_LOCATION;
    private $JS_LOCATION;

    private $userTable;
    private $withdrawalTable;
    private $accountImgTable;
    private $generalTable;
    private $generalCategoryTable;
    private $generalCategoryBookmarkTable;
    private $generalCategoryExpelTable;
    private $generalLikesTable;
    private $generalDislikesTable;
    private $generalExpelTable; 
    private $generalImgTable; 
    private $generalCommentsTable;
    private $generalCommentsLikesTable;
    private $generalCommentsDislikesTable; 
    private $generalCommentsExpelTable;
    private $scheduleLookupTable;
    private $userScheduleTable;
    private $tradeCategoryTable;
    private $tradeTable;
    private $tradeImgTable; 
    private $tradeExpelTable;

    private $authentication;


    public function __construct(string $CSS_LOCATION, string $JS_LOCATION, 
            DatabaseTable $userTable, DatabaseTable $withdrawalTable, DatabaseTable $accountImgTable,
            DatabaseTable $generalTable, DatabaseTable $generalCategoryTable, 
            DatabaseTable $generalCategoryBookmarkTable, DatabaseTable $generalCategoryExpelTable,
            DatabaseTable $generalLikesTable, DatabaseTable $generalDislikesTable, DatabaseTable $generalExpelTable,
            DatabaseTable $generalImgTable, DatabaseTable $generalCommentsTable,
            DatabaseTable $generalCommentsLikesTable, DatabaseTable $generalCommentsDislikesTable, 
            DatabaseTable $generalCommentsExpelTable,
            DatabaseTable $scheduleLookupTable, DatabaseTable $userScheduleTable,
            DatabaseTable $tradeCategoryTable, DatabaseTable $tradeTable, 
            DatabaseTable $tradeImgTable, DatabaseTable $tradeExpelTable,
            Authentication $authentication) {
        $this->CSS_LOCATION = $CSS_LOCATION; 
        $this->JS_LOCATION = $JS_LOCATION;

        $this->userTable = $userTable;
        $this->withdrawalTable = $withdrawalTable;
        $this->accountImgTable = $accountImgTable;
        $this->generalTable = $generalTable;
        $this->generalCategoryTable = $generalCategoryTable;
        $this->generalCategoryBookmarkTable = $generalCategoryBookmarkTable;
        $this->generalCategoryExpelTable = $generalCategoryExpelTable;
        $this->generalLikesTable = $generalLikesTable;
        $this->generalDislikesTable = $generalDislikesTable;
        $this->generalExpelTable = $generalExpelTable;
        $this->generalImgTable = $generalImgTable;
        $this->generalCommentsTable = $generalCommentsTable;
        $this->generalCommentsLikesTable = $generalCommentsLikesTable;
        $this->generalCommentsDislikesTable = $generalCommentsDislikesTable;
        $this->generalCommentsExpelTable = $generalCommentsExpelTable;
        $this->scheduleLookupTable = $scheduleLookupTable;
        $this->userScheduleTable = $userScheduleTable;
        $this->tradeCategoryTable = $tradeCategoryTable;
        $this->tradeTable = $tradeTable;
        $this->tradeImgTable = $tradeImgTable; 
        $this->tradeExpelTable = $tradeExpelTable;

        $this->authentication = $authentication;
    }


    /**
     *  로그인 상태의 사용자의 회원정보를 가져온다.
     */
    private function getUser() {
        $user = $this->authentication->getUser();
        if ($user === false) { return false; }
        $user['accountimg'] = $this->getAccountImgFilename($user['accountimgid']);
        return $user;
    }

    /**
     *  accountimgid를 매개변수로 받아서 사용자 이미지 파일명을 반환한다. 
     */
    private function getAccountImgFilename($accountimgid) {
        return $this->accountImgTable->findById($accountimgid)['filename'];
    }


    /**
     *  성신 커뮤니티  
     */
    public function about() {
        $addStyle = <<<_END
            <link rel="stylesheet" href="{$this->CSS_LOCATION}/about.styles.css">    
            <link rel="stylesheet" href="{$this->CSS_LOCATION}/aside.styles.css">
_END;
            
        $title = '성신 커뮤니티';    

        $user = $this->getUser();

        return [
            'addStyle' => $addStyle,
            'title' => $title,
            'template' => 'about.html.php',
            'variables' => [
                'aside' => [
                    'user' => $user
                ]
            ]
        ];
    } 


    /**
     *  성신 게시판 카테고리 (GET)
     */
    public function generalCategory() {
        $addStyle = <<<_END
            <link rel="stylesheet" href="{$this->CSS_LOCATION}/generalcategory.styles.css">
            <link rel="stylesheet" href="{$this->CSS_LOCATION}/aside.styles.css">
_END;
      
        $addScriptFile = <<<_END
            <script src="{$this->JS_LOCATION}/generalcategory_onload.js"></script>
_END;

        // general 페이지에서 스크롤 위치를 지정하는 역할을 하는 쿠키를 삭제한다.
        if (isset($_COOKIE['generalScrollTop'])) {
            setcookie('generalScrollTop', '', [
                'expires' => time()-3600, 
                'samesite' => 'None', 
                'secure' => true
            ]);
        }

        // generalview 페이지에서 '목록'버튼을 눌렀을 때 이전 위치로 이동시키는 쿠키를 삭제한다.
        if (isset($_COOKIE['generalQueryString'])) {
            setcookie('generalQueryString', '', [
                'expires' => time()-3600, 
                'samesite' => 'None', 
                'secure' => true
            ]);
        }


        $title = '성신 게시판 카테고리';

        $user = $this->getUser();

        // 사용자가 만든 게시판과 즐겨찾기에 추가한 게시판 목록
        $bookmarks = $this->getUserBookmarks($user);

        return [
            'addStyle' => $addStyle,
            'addScriptFile' => $addScriptFile,
            'title' => $title,
            'template' => 'generalcategory.html.php',
            'variables' => [
                'section' => [
                    'bookmarks' => $bookmarks
                ],
                'aside' => [
                    'user' => $user
                ]
            ]
        ];
    }

    /**
     *  성신 게시판 카테고리 (POST)
     */
    public function generalCategoryPost() {
        // 현재 접속한 사용자의 정보
        $user = $this->authentication->getUser();

        // 폼 오류 메시지
        $errors = [];

        /* 새 게시판을 만드는 경우 */ 
        if (isset($_POST['generalcategory'])) {
            $generalcategory = $_POST['generalcategory'];

            // 문자열의 앞뒤의 불필요한 공백이나 개행을 제거한다.
            $generalcategory['name'] = trim($generalcategory['name']);
            $generalcategory['info'] = trim($generalcategory['info']);
            $generalcategory['hashtag'] = trim($generalcategory['hashtag']);

            // 폼 오류 검증
            $result = $this->addNewGeneralCategory($generalcategory);
            $valid = $result['valid'];
            $errors['add_gc_new'] = $result['errors'];
        
            if ($valid == true) {   // 새 게시판 생성
                // 새 게시판의 임시 id 생성
                $tmpId = $user['id'] . '_' . ($user['generalcategorycount'] + 1);

                /* 데이터베이스에 저장하는 코드 구현 */
                $this->generalCategoryTable->insert([
                    'tmpid' => $tmpId,
                    'userid' => $user['id'],
                    'name' => $generalcategory['name'],
                    'info' => $generalcategory['info'],
                    'hashtag' => $generalcategory['hashtag'],
                    'expel' => $generalcategory['expel'] ?? 'N',
                    'users' => 1
                ]);

                // 새 게시판의 임시 id를 이용해서 id 값을 구한다.
                $findCategoryResult = $this->generalCategoryTable->find('tmpid', $tmpId); 
                if ($findCategoryResult) {  // 게시판 생성 성공 시
                    // userTable 의 'generalcategorycount' 칼럼의 값 1 증가
                    $this->IncreaseOne($this->userTable, $user['id'], 'generalcategorycount');
                    // 해당 카테고리의 게시판으로 이동
                    header('location: general?category=' . $findCategoryResult[0]['id']);
                } else {
                    header('location: generalcategory');
                }
            } else {    // 폼 오류 검증에 실패한 경우 에러 메시지를 추가해서 '성신 게시판 카테고리'를 출력한다.
                $template = $this->generalCategory();

                $template['variables']['section']['errors'] = $errors;
                $template['variables']['section']['generalcategory'] = $generalcategory;

                return $template;
            }
        }

        /* 게시판 검색 */ 
        if (isset($_POST['search'])) {
            // 문자열의 앞뒤의 불필요한 공백이나 개행을 제거한다.
            $search = trim($_POST['search']);

            // 데이터 베이스 검색 기능 수행
            if ($search[0] == '#') {    // '검색어 해시태그'를 사용해서 검색한다.
                /**
                 *  공백을 발견하면 그 후의 단어들은 무시한다.
                 *  ex) '#검색한 단어' --> '#검색한'으로 검색한다. 공백 뒤의 '단어'는 무시한다.
                 */
                $substrlen = strpos($search, ' '); 
                if ($substrlen) {
                    $search = substr($search, 0, $substrlen);
                }
                $results = $this->generalCategoryTable->search('hashtag', $search);
            } else {    // '이름'을 사용해서 검색한다.
                $results = $this->generalCategoryTable->search('name', $search);
            }

            // 사용자가 만든 게시판과 즐겨찾기에 추가한 게시판 목록과 중복되는 검색 결과는 삭제한다.
            // 방출된 게시판 결과도 삭제한다.
            $excludes = $this->getUserBookmarks($user);
            foreach ($this->generalCategoryExpelTable->find('userid', $user['id']) as $result) {
                $excludes[] = $this->generalCategoryTable->findById($result['generalcategoryid']);
            }

            foreach ($excludes as $exclude) {
                $index = array_search($exclude, $results);
                if ($index !== false) {
                    unset($results[$index]);
                }
            }

            // 중복되는 검색 결과를 배열에서 unset함수를 사용해서 삭제하고 나면 인덱스가 비어지는 상황이 발생하므로,
            // 인덱스를 다시 0부터 1씩 증가하는 순서로 맞추어준다.
            changeToSequentialArray($results);

            // 검색 결과를 반환한다.
            $template = $this->generalCategory();
            $template['variables']['section']['search'] = $search;
            $template['variables']['section']['results'] = $results;
            $template['variables']['section']['searchFormSubmitted'] = true; // 뒤로가기로 인한 폼 재전송 방지를 위해 정의한 변수
            return $template;
        }

        /** 
         *  즐겨찾기(bookmark)를 제거하는 경우
         *
         *  [참고]
         *  - 자유게시판과 사용자가 생성한 게시판은 즐겨찾기를 제거할 수 없다.
         *  - 사용자가 생성한 게시판은 즐겨찾기 목록(generalcategorybookmark)에 추가되어 있지 않다.
         *  - 자유게시판은 즐겨찾기 목록(generalcategorybookmark)에 추가된다는 점에 유의한다.
         */ 
        if (isset($_POST['remove_bookmark'])) {
            // 즐겨찾기 제거 가능 여부
            $isRemovable = false;

            $bookmarkUserList = $this->generalCategoryBookmarkTable->find('generalcategoryid', $_POST['remove_bookmark_id']);
            foreach ($bookmarkUserList as $bookmarkUser) {
                // 즐겨찾기 목록(generalcategorybookmark)에 추가되어 있고 자유게시판이 아닌 경우
                if ($_POST['remove_bookmark_id'] != 1 && $user['id'] == $bookmarkUser['userid']) {
                    $isRemovable = true;
                    break;
                }
            }

            // 즐겨찾기를 제거한다.
            if ($isRemovable) {

                // 즐겨찾기 룩업 테이블에서 제거한다.
                $this->generalCategoryBookmarkTable->deleteLookup($user['id'],
                        'generalcategoryid', $_POST['remove_bookmark_id']);

                // 해당 게시판이 삭제되지 않은 경우에만 실행한다.
                if ($this->generalCategoryTable->findById($_POST['remove_bookmark_id'])) {

                    // 게시판의 users 칼럼의 값 1 감소
                    $this->DecreaseOne($this->generalCategoryTable, $_POST['remove_bookmark_id'], 'users');

                    // 게시판에 방출 기능이 설정되어있는 경우, 좋아요 수가 음수면 +1을 한다.
                    $this->removeBookmarkLikesUpdate($_POST['remove_bookmark_id']);
                }
            }

            // 폼 처리를 완료하고 페이지를 이동시켜야지 폼 재전송이 발생하지 않는다.    
            header('location: generalcategory');
        }

        /* 즐겨찾기(bookmark)를 추가하는 경우 */ 
        if (isset($_POST['add_bookmark'])) {

            // 해당 게시판이 삭제되지 않은 경우에만 실행한다.
            if ($this->generalCategoryTable->findById($_POST['add_bookmark_id'])) {

                // 즐겨찾기 룩업 테이블에 추가한다.
                $this->generalCategoryBookmarkTable->insert([
                    'userid' => $user['id'],
                    'generalcategoryid' => $_POST['add_bookmark_id']
                ]);

                // 게시판의 users 칼럼의 값 1 증가
                $this->IncreaseOne($this->generalCategoryTable, $_POST['add_bookmark_id'], 'users');
            }

            header('location: generalcategory');
        }
    }


    /**
     *  게시판에 방출 기능이 설정되어있는 경우, 좋아요 수가 음수면 +1을 하는 기능을 추가한다.
     */
    private function removeBookmarkLikesUpdate($categoryid) {
        $category = $this->generalCategoryTable->findById($categoryid);
        if ($category) {
            if ($category['expel'] == 'Y') {    // 게시판에 방출 기능이 설정되어있는 경우
                $generalList = $this->generalTable->find('categoryid', $categoryid);
                if ($generalList) {
                    foreach ($generalList as $general) {
                        $generalCommentList = $this->generalCommentsTable->find('generalid', $general['id']);
                        if ($generalCommentList) {
                            foreach ($generalCommentList as $generalComment) {
                                if ($generalComment['likes'] < 0) { // 댓글의 좋아요 수가 음수인 경우
                                    $this->IncreaseOne($this->generalCommentsTable, $generalComment['id'], 'likes');
                                }
                            }
                        }
                        if ($general['likes'] < 0) {    // 글의 좋아요 수가 음수인 경우
                            $this->IncreaseOne($this->generalTable, $general['id'], 'likes');
                        }
                    }
                }
            }
        }
    }
    

    /**
     *  '새 게시판 만들기' 폼 오류 검증
     *  검증 결과를 반환한다. 
     */
    private function addNewGeneralCategory(array $generalcategory) {
        // 데이터는 처음부터 유효하다고 가정
        $valid = true;
        $errors = [];

        /*  ===========================================================
         *  - name, info 항목은 빈 문자열을 허용하지 않는다.
         *  - name 항목은 중복될 수 없다.
         * =========================================================== */

        /* 이름 */
        if (empty($generalcategory['name'])) {
            $valid = false;
            $errors[] = "이름과 설명은 필수기재 사항입니다.";
        } else {
            // 중복된 게시판 이름이 존재하는 경우
            if (count($this->generalCategoryTable->find('name', $generalcategory['name'])) > 0) {
                $valid = false;
                $errors[] = "이미 사용 중인 게시판 이름입니다.";
            }
        }
    
        /* 설명 */
        if (empty($generalcategory['info'])) {
            $valid = false;
            $errors[] = "이름과 설명은 필수기재 사항입니다.";
        }
        
        return [
            'valid' => $valid,
            'errors' => $errors
        ];
    }

    /**
     *  사용자가 만든 게시판과 즐겨찾기에 추가한 게시판 목록을 반환한다.
     */
    private function getUserBookmarks(array $user) {
        $bookmarks = [];
        // 사용자가 만든 게시판 목록
        $bookmarks = $this->generalCategoryTable->find('userid', $user['id']);
        // 즐겨찾기에 추가한 게시판 목록
        foreach ($this->generalCategoryBookmarkTable->find('userid', $user['id']) as $result) {
            $bookmarks[] = $this->generalCategoryTable->findById($result['generalcategoryid']);
        }
        return $bookmarks;
    }


    /**
     *  성신 게시판 (GET)
     */
    public function general() {
        $addStyle = <<<_END
            <link rel="stylesheet" href="{$this->CSS_LOCATION}/general.styles.css">
            <link rel="stylesheet" href="{$this->CSS_LOCATION}/aside.styles.css">
_END;
      
        $addScriptFile = <<<_END
            <script src="{$this->JS_LOCATION}/util.js"></script>
            <script src="{$this->JS_LOCATION}/loading.js"></script>
            <script src="{$this->JS_LOCATION}/general_scroll.js"></script>
            <script src="{$this->JS_LOCATION}/general_onload.js"></script>
_END;

        // generalview 페이지에서 '목록'버튼을 눌렀을 때 이전 위치로 이동한다.
        if (isset($_COOKIE['generalQueryString'])) {
            $queryString = $_COOKIE['generalQueryString'];
            setcookie('generalQueryString', '', time()-3600);
            header('location: general' . $queryString);
        }

        $title = '성신 게시판';

        $user = $this->getUser();

        // 사용자가 만든 게시판과 즐겨찾기에 추가한 게시판 이외의 게시판은 접근을 허용하지 않는다.
        $isCategoryAccessible = false;
        $bookmarks = $this->getUserBookmarks($user);
        foreach ($bookmarks as $bookmark) {
            if ($bookmark['id'] === $_GET['category']) {
                $isCategoryAccessible = true;
                break;
            } 
        }
        if (!$isCategoryAccessible) {
            header('location: generalcategory');
        }

        // 접속한 페이지의 카테고리 정보
        $gCategory = $this->generalCategoryTable->findById($_GET['category']);

        // 검색한 단어가 없는 경우
        if (empty($_GET['search'])) {

            // 접속한 페이지의 카테고리의 전체 글 목록
            $generalList = $this->generalTable->find('categoryid', $_GET['category']);

            // 접속한 페이지의 카테고리 글 목록에 작성자 정보 추가
            $this->addUsersInfo($generalList);

            // 배열을 뒤집어서 최신 글이 위에 있도록 설정한다.
            $generalList = array_reverse($generalList);
        
            // 오늘의 인기 글
            if ($_GET['order']) {
                // 오늘 작성한 글을 분리한다.
                $todayGeneralList = [];
                foreach ($generalList as $general) {
                    $today = new DateTime();
                    $today = $today->format('Y-m-d');
                    if (strncmp($today, $general['date'], strlen($today)) == 0) {
                        $todayGeneralList[] = $general;
                    }
                }
                
                /* '좋아요' 순서대로 내림차순 정렬 */ 
                $sortList = [];
                foreach ($todayGeneralList as $key => $value) {
                    $sortList[$key] = $value['likes'];  
                }
                arsort($sortList);

                $generalList = [];
                foreach ($sortList as $key => $value) {
                    $generalList[] = $todayGeneralList[$key];
                }
            } 
        } 
        // 검색한 단어가 있는 경우 (전체 글 기준)
        else {
            // 검색 단어를 포함한 글 목록
            $generalList = $this->generalTable->searchAndOption([
                'categoryid' => $_GET['category']
            ], 'text', trim($_GET['search']));

            // 작성자 정보 추가
            $this->addUsersInfo($generalList);

            // 배열을 뒤집어서 최신 글이 위에 있도록 설정한다.
            $generalList = array_reverse($generalList);
        }

        return [
            'addStyle' => $addStyle,
            'addScriptFile' => $addScriptFile,
            'title' => $title,
            'template' => 'general.html.php',
            'variables' => [
                'section' => [
                    'user' => $user,
                    'gCategory' => $gCategory,
                    'generalList' => $generalList
                ],
                'aside' => [
                    'user' => $user
                ]
            ]
        ];
    }

    /**
     *  성신 게시판 (POST)
     */
    public function generalPost() {
        $user = $this->getUser();

        /* 게시판을 삭제하는 경우 */
        if (isset($_POST['remove_category'])) {
            $this->deleteGeneralCategory($_GET['category']);
            header('location: generalcategory');
        }

        /** 
         *  신규 글을 작성하는 경우 (아래에 해당하는 경우에 신규 글이 등록된다.)
         *
         *  - 텍스트 부분이 공백으로만 이루어지지 않은 경우
         *  - 파일(이미지) 업로드가 적어도 1개 이상 성공한 경우
         */
        if (isset($_POST['generaltext'])) {

            // 해당 게시판 카테고리가 존재하는지 확인한다.
            if (!$this->generalCategoryTable->findById($_GET['category'])) {
                header('location: generalcategory');
            }

            // 불필요한 공백이나 개행을 제거한다.
            $_POST['generaltext'] = trim($_POST['generaltext']);

            // 공백으로만 이루어진 경우에는 처리하지 않는다.
            if ($_POST['generaltext'] !== '') {
                // 새 게시판의 임시 id 생성
                $tmpId = $user['id'] . '_' . ($user['generalcount'] + 1);

                $this->generalTable->insert([    
                    'tmpid' => $tmpId, 
                    'userid' => $user['id'], 
                    'categoryid' => $_GET['category'],    
                    'text' => $_POST['generaltext'],    
                    'img' => 0,   
                    'date' => new DateTime(),    
                    'likes' => 0,
                    'comments' => 0,
                    'groupid' => 0
                ]);     

                // 파일을 업로드한 경우에만 실행한다. 
                if (!empty($_FILES['add_file']['name'][0])) {
                    // 새 게시판의 임시 id를 이용해서 id 값을 구한다.
                    $findGeneralResult = $this->generalTable->find('tmpid', $tmpId); 
                    if ($findGeneralResult) {
                        // 업로드한 이미지 파일을 서버에 저장하고 실행 결과를 반환한다.
                        $saveUploadImageFilesResults = $this->saveUploadImageFiles($_FILES['add_file'], 
                            'general', $findGeneralResult[0]['id'], true, 500); 

                        if ($saveUploadImageFilesResults['upload_success']) {
                            // 중간에 서버 저장에 실패해서 파일의 일부가 누락된 경우, 누락된 파일은 무시하고 진행한다.
                            if (count($saveUploadImageFilesResults['path']) > 0) {
                                // 이미지 파일 개수를 설정한다.
                                $this->generalTable->update([
                                    'primaryKey' => $findGeneralResult[0]['id'],
                                    'img' => count($saveUploadImageFilesResults['path'])
                                ]);
                                // 이미지 파일 경로와 넓이 정보를 테이블에 저장한다.
                                foreach ($saveUploadImageFilesResults['path'] as $key => $path) {
                                    $this->generalImgTable->insert([
                                        'generalid' => $findGeneralResult[0]['id'],
                                        'path' => $path,
                                        'width' => $saveUploadImageFilesResults['width'][$key]
                                    ]);
                                }
                            }
                        }
                    }
                }

                // userTable 의 'generalcount' 칼럼의 값 1 증가
                $this->IncreaseOne($this->userTable, $user['id'], 'generalcount');
            }

            // 폼 처리를 완료하고 페이지를 이동시켜야지 폼 재전송이 발생하지 않는다.    
            header('location: general?category=' . $_GET['category']);
        }

        /* 좋아요를 클릭한 경우 */
        if (isset($_POST['likes'])) {
            $this->generalClickLikes($_POST['likes_id'], $user['id']);
            header('location: general?' . $this->getURLQueryString());
        }

        /* 싫어요를 클릭한 경우 */
        if (isset($_POST['dislikes'])) {
            /**
             *  게시판이 방출기능을 허용한 경우에만 동작한다.
             *  다수가 싫어요를 누른 경우에 해당 글은 삭제된다.
             */
            $category = $this->generalCategoryTable->findById($_GET['category']);
            if ($category) {
                $this->generalClickDislikes($category, $_POST['dislikes_id'], $user['id']);
            } else {    // 해당 게시판이 삭제된 경우
                header('location: generalcategory');
            }

            header('location: general?' . $this->getURLQueryString());
        }

        /* ssexpel (stranger) 클릭한 경우 */
        if (isset($_POST['stranger'])) {
            $this->generalClickStranger($_POST['stranger_userid'], $_POST['stranger_id'], $user['id']);
            header('location: general?' . $this->getURLQueryString());
        }


        /* 내가 쓴 글을 삭제하는 경우 */
        if (isset($_POST['delete'])) {
            $this->deleteGeneral($_POST['delete_id']);
            header('location: general?' . $this->getURLQueryString());
        }
    }

    /**
     *  URL 뒤 '?'이후의 값을 반환한다. ('?'는 포함하지 않는다.)
     */
    private function getURLQueryString() {
        return explode('?', $_SERVER['REQUEST_URI'])[1];
    }

    /**
     *  카테고리 expel 기능을 수행한다.
     */
    private function categoryExpel($userid) {
        $category = $this->generalCategoryTable->findById($_GET['category']);
        if ($category) {
            if ($category['userid'] != $userid) {
                // 즐겨찾기 룩업 테이블에서 제거한다.
                $this->generalCategoryBookmarkTable->deleteLookup($userid,
                        'generalcategoryid', $_GET['category']);
                // generalcategoryexpel 룩업 테이블에 추가한다.
                $this->generalCategoryExpelTable->insert([
                    'userid' => $userid,
                    'generalcategoryid' => $_GET['category']
                ]);
                // generalcategory 테이블에서 해당 카테고리의 사용자 수를 1 감소한다.
                $this->DecreaseOne($this->generalCategoryTable, $_GET['category'], 'users');
            }
        }
    }

    /*================================================================================================*/ 

    /**
     *  general (글 본문) : '좋아요'를 클릭한 경우 
     */
    private function generalClickLikes($generalid, $userid) {
        // 작성자가 회원탈퇴를 한 경우 반응을 하지 않는다.
        if ($this->getWriter($generalid)) {
            // 해당 글에 좋아요를 한 이력을 확인한다.
            $isLikesCommitted = false;
            $likesList = $this->generalLikesTable->find('generalid', $generalid);
            foreach ($likesList as $likes) {
                if ($likes['userid'] == $userid) {
                    $isLikesCommitted = true;
                    break;
                }
            }
            // 좋아요는 한 번만 클릭할 수 있도록 제한한다.
            if (!$isLikesCommitted) {
                // generallikes 룩업 테이블에 추가한다.
                $this->generalLikesTable->insert([
                    'userid' => $userid,
                    'generalid' => $generalid
                ]);

                // 글의 likes 칼럼의 값 1 증가
                $this->IncreaseOne($this->generalTable, $generalid, 'likes');
            }
        }
    }

    /**
     *  general (글 본문) : '싫어요'를 클릭한 경우 
     */
    private function generalClickDislikes(array $category, $generalid, $userid) {
        /**
         *  게시판이 방출기능을 허용한 경우에만 동작한다.
         *  다수가 싫어요를 누른 경우에 해당 글은 삭제된다.
         */
        if ($category['expel'] == 'Y') {
            // 해당 글에 싫어요를 한 이력을 확인한다.
            $isDislikesCommitted = false;
            $dislikesList = $this->generalDislikesTable->find('generalid', $generalid);
            foreach ($dislikesList as $dislikes) {
                if ($dislikes['userid'] == $userid) {
                    $isDislikesCommitted = true;
                    break;
                }
            }

            // 싫어요는 한 번만 클릭할 수 있도록 제한한다.
            if (!$isDislikesCommitted) {

                // generaldislikes 룩업 테이블에 추가한다.
                $this->generalDislikesTable->insert([
                    'userid' => $userid,
                    'generalid' => $generalid
                ]);

                // 글의 likes 칼럼의 값 1 감소
                $this->DecreaseOne($this->generalTable, $generalid, 'likes');

                if ( $this->generalTable->findById($generalid)['likes'] < -(int)($category['users'] / 2) ) {

                    // 해당 글 작성자 방출 (회원탈퇴한 사용자가 아닌 경우) 
                    $writer = $this->getWriter($generalid);
                    if ($writer) {
                        $this->categoryExpel($writer);
                    } 

                    // 글 삭제
                    $this->deleteGeneral($generalid);
                }
            }
        }
    }

    /**
     *  general (글 본문) : ssexpel (stranger) 클릭한 경우
     */
    private function generalClickStranger($stranger_userid, $generalid, $userid) {
        // 작성자가 회원탈퇴를 한 경우 반응을 하지 않는다.
        if ($this->userTable->findById($stranger_userid)) {
            // 해당 글에 방출을 누른 이력을 확인한다.
            $isExpelCommitted = false;
            $expelList = $this->generalExpelTable->find('generalid', $generalid);
            foreach ($expelList as $expel) {
                if ($expel['userid'] == $userid) {
                    $isExpelCommitted = true;
                    break;
                }
            }
            // 방출 버튼을 한 번만 클릭할 수 있도록 제한한다.
            if (!$isExpelCommitted) {
                // generalexpel 룩업 테이블에 추가한다.
                $this->generalExpelTable->insert([
                    'userid' => $userid,
                    'generalid' => $generalid
                ]);

                // ssexpel 기능을 수행한다.
                $this->ssexpelUser($stranger_userid);
            }
        }
    }

    /*================================================================================================*/ 

    /**
     *  ssexpel 기능을 수행한다.
     */
    private function ssexpelUser($userid) {
        $expelUser = $this->userTable->findById($userid);
        if ($expelUser) {
            if ($expelUser['ssexpel'] + 1 == SS_EXPEL_MAX) {
                // 성신 인증이 취소된다. (ssexpel 값은 초기화)
                $this->userTable->update([
                    'primaryKey' => $userid,
                    'issungshin' => 'N',
                    'ssexpel' => 0
                ]); 

                $this->removeUserFromGeneral($userid); 
            } else {
                // ssexpel 값을 1 증가시킨다.
                $this->IncreaseOne($this->userTable, $userid, 'ssexpel');
            }
        }
    } 

    /**
     *  해당 사용자가 생성한 카테고리, 글, 댓글을 모두 삭제하고, 자유게시판에서 해당 사용자를 제거한다.
     */
    private function removeUserFromGeneral($userid) {
        // 해당 사용자가 생성한 카테고리를 모두 삭제한다.
        $this->deleteUserGeneralCategory($userid);
        // 해당 사용자가 작성한 글을 모두 삭제한다.
        $this->deleteUserGeneral($userid);
        // 해당 사용자가 작성한 댓글을 모두 삭제한다.
        $this->deleteUserComment($userid);
        // generalcategory 자유게시판(1)의 users 칼럼 1 감소
        $this->DecreaseOne($this->generalCategoryTable, 1, 'users');
        // generalcategorybookmark 수정
        $this->generalCategoryBookmarkTable->deleteLookup($userid, 'generalcategoryid', 1);
    }


    /**
     *  업로드한 이미지 파일을 서버에 저장하는 기능을 수행한다. 
     *
     *  매개변수 
     *              - $FILES    : $_FILES[] 값을 전달한다.
     *              - $fileDir  : /var/www/html/file/images/ 아래에 존재하는 폴더명을 전달한다.
     *              - $fileId   : 데이터베이스의 기본 키를 전달한다.
     *
     *              - $fixOrientation   : 이미지의 가로, 세로가 변경되어 등록되는 경우 처리 적용 여부
     *              - $changePxWidth    : (가로) 픽셀 크기 축소
     *              - $compress         : 압축 적용 여부
     *              - $compressQuality  : 압축률
     *
     *  반환값
     *              - upload_success    : 업로드 성공 여부
     *              - err_code          : 업로드 실패에 관한 에러 코드
     *              - save_success      : 업로드된 파일이 서버에 저장 성공 여부
     *              - path              : (날짜 형식 디렉토리)/(파일명) 형식
     *              - width             : 파일 width 정보
     *
     *  - err_code
     *                  [0] 파일 용량 초과
     *                  [1] UPLOAD_ERR_OK 실패 요소 중 파일 용량 초과를 제외한 나머지 경우
     *                  [2] 파일 타입 오류
     *
     */
    private function saveUploadImageFiles(array $FILES, $fileDir, $fileId, 
        bool $fixOrientation=false, int $changePxWidth=0, bool $compress=false, int $compressQuality=0) 
    {
        // 업로드 성공 여부 검사
        foreach ($FILES['error'] as $uploadErrorCheck) {
            if ($uploadErrorCheck != UPLOAD_ERR_OK) {
                // 파일 용량 초과로 인한 오류에 해당하는 경우
                // php.ini 설정 파일의 upload_max_filesize에서 설정 가능 (5M) 
                if ($uploadErrorCheck == UPLOAD_ERR_INI_SIZE) {
                    return [
                        'upload_success' => false,
                        'err_code' => 0
                    ];
                } else {
                    return [
                        'upload_success' => false,
                        'err_code' => 1
                    ];
                }
            }
        }
        // 파일 타입 검사
        foreach ($FILES['type'] as $fileType) {
            $fileType = explode('/', $fileType)[1];
            if ($fileType != 'jpg' && $fileType != 'gif' 
                    && $fileType != 'png' && $fileType != 'jpeg' && $fileType != 'bmp') {
                return [
                    'upload_success' => false,
                    'err_code' => 2
                ];
            }
        }


        // 파일 업로드에 성공한 경우 서버에 파일을 저장한다.

        /*
         *  이미지가 많아질 경우 이미지를 찾는 시간을 줄이기 위해 
         *  이미지를 업로드한 날짜에 해당하는 폴더에 저장한다.
         */
        $today = new DateTime();
        $todayDir = $today->format('Y-m-d');

        /* 
         * 파일이 서버에 저장될 이름를 지정한다.
         * (데이터베이스 기본 키를 이름에 추가해서 동일한 파일의 이름을 방지한다.)
         */
        $todayDirAndFileName = [];

        // 파일 width 정보
        $fileWidth = [];

        foreach ($FILES['name'] as $fileName) {
            $todayDirAndFileName[] = $todayDir . '/' . $fileId . '_' . basename($fileName);
        } 
        $savedFileCounter = 0;      // 서버에 저장 완료된 파일의 개수 저장
        foreach($FILES['tmp_name'] as $key => $value) {
            // 업로드된 파일 width 정보
            list($width, $height, $type) = getimagesize($value);

            /**
             *  (파일 확장자를 임의로 바꾼 경우에 대처하기 위해서)
             *  파일의 width 정보가 존재하지 않는 경우에는 저장하지 않는다.
             */
            if (!$width) {
                // 업로드된 파일이 서버에 저장을 실패한 경우
                $tmp_todayDirAndFileName = [];
                $tmp_fileWidth = [];
                for ($i = 0; $i < $savedFileCounter; $i++) {
                    $tmp_todayDirAndFileName[] = $todayDirAndFileName[$i];
                    $tmp_fileWidth[] = $fileWidth[$i];
                }
                // 성공한 결과까지만 반환한다.
                return [
                    'upload_success' => true,
                    'save_success' => false,
                    'path' => $tmp_todayDirAndFileName,
                    'width' => $tmp_fileWidth
                ];
            }


            /* 업로드 파일 수정 작업 (서버에 저장하기 전에 실행된다.) */

            $old_image = load_image($value, $type);

            // 이미지의 가로, 세로가 변경되어 등록되는 경우 처리
            if ($fixOrientation) {
                if (image_fix_orientation($old_image, $value)) {
                    $temp = $width;
                    $width = $height;
                    $height = $temp;
                }
            }

            // 픽셀 크기를 축소하는 경우
            if ($changePxWidth != 0 && ($width > $changePxWidth)) {
                $tempImg = resize_image_to_width($changePxWidth, $old_image, $width, $height);
                imagejpeg($tempImg, $value, 100);
                $width = $changePxWidth;
            }

            // 최종 파일 width 정보
            $fileWidth[] = $width;


            /* 파일을 서버에 저장한다. */

            // 파일을 저장할 (서버) 위치
            $imageUploadPath = __DIR__ . '/' . '../../file/images/' . $fileDir . '/' . $todayDirAndFileName[$key];

            if (!$compress && move_uploaded_file($value, $imageUploadPath)) {
                // 업로드된 파일이 서버에 저장이 완료된 경우
                $savedFileCounter++;
            } else if ($compress && compressImage($value, $imageUploadPath, $compressQuality)) {
                // 업로드된 파일이 압축되어 서버에 저장이 완료된 경우
                $savedFileCounter++;
            } else {
                // 업로드된 파일이 서버에 저장을 실패한 경우
                $tmp_todayDirAndFileName = [];
                $tmp_fileWidth = [];
                for ($i = 0; $i < $savedFileCounter; $i++) {
                    $tmp_todayDirAndFileName[] = $todayDirAndFileName[$i];
                    $tmp_fileWidth[] = $fileWidth[$i];
                }
                // 성공한 결과까지만 반환한다.
                return [
                    'upload_success' => true,
                    'save_success' => false,
                    'path' => $tmp_todayDirAndFileName,
                    'width' => $tmp_fileWidth
                ];
            }
        }

        // 파일 업로드와 저장 모두 정상적으로 진행된 경우
        return [
            'upload_success' => true,
            'save_success' => true,
            'path' => $todayDirAndFileName,
            'width' => $fileWidth
        ];
    }


    /**
     *  칼럼의 값 1 증가
     */
    private function IncreaseOne(DatabaseTable $table, $pkValue, $column) {
        $value = $table->findById($pkValue)[$column];
        $table->update([
            'primaryKey' => $pkValue,
            $column => $value + 1
        ]);
    }
    /**
     *  칼럼의 값 1 감소
     */
    private function DecreaseOne(DatabaseTable $table, $pkValue, $column) {
        $value = $table->findById($pkValue)[$column];
        $table->update([
            'primaryKey' => $pkValue,
            $column => $value - 1
        ]);
    }

    /**
     *  general 테이블에서 글 작성자를 찾아서 id값을 반환한다.
     *  회원탈퇴한 사용자의 경우 false를 반환한다.
     */
    private function getWriter($generalid) {
        $writer = $this->generalTable->findById($generalid)['userid'];

        if ($writer == false) { // 해당 글이 더 이상 존재하지 않는 경우
            return false;
        }

        if ($this->userTable->findById($writer)) {
            return $writer;
        } else {
            return false;
        }
    }

    /**
     *  generalcomments 테이블에서 글 작성자를 찾아서 id값을 반환한다.
     *  회원탈퇴한 사용자의 경우 false를 반환한다.
     */
    private function getCommentWriter($generalcommentsid) {
        $writer = $this->generalCommentsTable->findById($generalcommentsid)['userid'];

        if ($writer == false) { // 해당 글이 더 이상 존재하지 않는 경우
            return false;
        }

        if ($this->userTable->findById($writer)) {
            return $writer;
        } else {
            return false;
        }
    }
    
    /**
     *  성신 게시판 본문 (GET)
     */
    public function generalView() {
        $addStyle = <<<_END
            <link rel="stylesheet" href="{$this->CSS_LOCATION}/generalview.styles.css">
            <link rel="stylesheet" href="{$this->CSS_LOCATION}/aside.styles.css">
_END;
      
        $addScriptFile = <<<_END
            <script src="{$this->JS_LOCATION}/util.js"></script>
            <script src="{$this->JS_LOCATION}/generalview_scroll.js"></script>
            <script src="{$this->JS_LOCATION}/generalview_onload.js"></script>
_END;

        $title = '성신 게시판 본문';

        // 접근 제한 설정
        $isCameFromGeneral = strpos($_SERVER['HTTP_REFERER'], 'general?category=');
        $isCameFromGeneralView = strpos($_SERVER['HTTP_REFERER'], 
                                    ('generalview?category=' . $_GET['category'] . '&id=' . $_GET['id']));
        $isCameFromMyArticle = strpos($_SERVER['HTTP_REFERER'], 'myarticle?select=general');
        if (!($isCameFromGeneral || $isCameFromGeneralView || $isCameFromMyArticle)) {
            header('location: generalcategory');
        }

        $user = $this->getUser();
        $gView = $this->generalTable->findById($_GET['id']);        // 게시판 글 정보

        // 해당 글이 더 이상 존재하지 않는 경우
        if (!$gView) {
            if ($isCameFromGeneral) {
                $came_from = strstr($_SERVER['HTTP_REFERER'], 'general?category=');
                header('location: ' . $came_from);
            } else if ($isCameFromGeneralView) {
                header('location: general?category=' . $_GET['category']);
            } else if ($isCameFromMyArticle) {
                $came_from = strstr($_SERVER['HTTP_REFERER'], 'myarticle?select=');
                header('location: ' . $came_from);
            }
        }

        // 작성자 정보를 추가한다
        $this->addUserInfo($gView);
        // 이미지 파일 정보를 추가한다.
        if ($gView['img'] > 0) {
            $gView['imgpath'] = [];
            $gView['imgwidth'] = [];
            foreach ($this->generalImgTable->find('generalid', $gView['id']) as $img) {
                $gView['imgpath'][] = $img['path'];
                $gView['imgwidth'][] = $img['width'];
            }
        }
        // 카테고리 정보를 추가한다.
        $gView['categoryname'] = $this->generalCategoryTable->findById($_GET['category'])['name'];


        // 댓글+대댓글 목록
        $gComments = $this->generalCommentsTable->find('generalid', $_GET['id']);
        // 작성자 정보를 추가한다
        $this->addUsersInfo($gComments);

        // 댓글과 대댓글을 분리한다.
        $gBaseComments = [];
        $gChildComments = [];
        foreach ($gComments as $key => $comment) {
            if ($comment['class'] == 0) {
                // 댓글의 댓글 개수를 저장하기 위한 요소 comments 생성
                $gBaseComments[$comment['group']] = $comment;
                $gBaseComments[$comment['group']]['comments'] = 0;
            } else {
                // 댓글의 댓글 개수를 저장한다.
                $gBaseComments[$comment['group']]['comments']++;

                // 대댓글을 댓글 group 아래로 정리한다.
                $gChildComments[$comment['group']][] = $comment;
            }
        }

        // 비연속적인 배열을 연속적인 배열로 변환환다.
        changeToSequentialArray($gBaseComments);

        // id(auto_increment)순을 뒤집어서 최신순으로 정렬한다.
        $gBaseComments = array_reverse($gBaseComments);
        foreach ($gChildComments as $key => $value) {
            $gChildComments[$key] = array_reverse($value);
        }


        return [
            'addStyle' => $addStyle,
            'addScriptFile' => $addScriptFile,
            'title' => $title,
            'template' => 'generalview.html.php',
            'variables' => [
                'section' => [
                    'user' => $user,
                    'gView' => $gView,
                    'gBaseComments' => $gBaseComments,      // 댓글
                    'gChildComments' => $gChildComments     // 대댓글
                ],
                'aside' => [
                    'user' => $user
                ]
            ]
        ];
    }

    /**
     *  성신 게시판 본문 (POST)
     */
    public function generalViewPost() {
        $user = $this->getUser();
        
        /* 해당 글에 '좋아요'를 클릭한 경우 */
        if (isset($_POST['likes'])) {
            $this->generalClickLikes($_GET['id'], $user['id']);
            header('location: generalview?' . $this->getURLQueryString());
        }

        /* 해당 글에 '싫어요'를 클릭한 경우 */
        if (isset($_POST['dislikes'])) {
            /**
             *  게시판이 방출기능을 허용한 경우에만 동작한다.
             *  다수가 싫어요를 누른 경우에 해당 글은 삭제된다.
             */
            $category = $this->generalCategoryTable->findById($_GET['category']);
            if ($category) {
                $this->generalClickDislikes($category, $_GET['id'], $user['id']);
            } else {    // 해당 게시판이 삭제된 경우
                header('location: generalcategory');
            }

            header('location: generalview?' . $this->getURLQueryString());
        }

        /* 해당 글에 ssexpel (stranger) 클릭한 경우 */
        if (isset($_POST['stranger'])) {
            $this->generalClickStranger($_POST['stranger_userid'], $_GET['id'], $user['id']);
            header('location: generalview?' . $this->getURLQueryString());
        }


        /* 새 댓글 등록 */
        if (isset($_POST['generalcomment'])) {

            // 불필요한 공백이나 개행을 제거한다.
            $_POST['generalcomment'] = trim($_POST['generalcomment']);

            // 공백으로만 이루어진 경우에는 처리하지 않는다.
            if ($_POST['generalcomment'] !== '') { 
                $general = $this->generalTable->findById($_GET['id']);
                if ($general) {     // 해당 글이 존재하는 경우

                    $this->generalCommentsTable->insert([
                        'userid' => $user['id'],
                        'text' => $_POST['generalcomment'],
                        'date' => new DateTime(),
                        'likes' => 0,
                        'class' => 0,                       // 댓글 0, 대댓글 1
                        'group' => $general['groupid'],     // 0부터 시작
                        'categoryid' => $_GET['category'],
                        'generalid' => $_GET['id']
                    ]);

                    // comments 값을 1 증가시킨다.  
                    $this->IncreaseOne($this->generalTable, $_GET['id'], 'comments');
                    // groupid 값을 1 증가시킨다.
                    $this->IncreaseOne($this->generalTable, $_GET['id'], 'groupid');

                } else {    // 해당 글이 삭제되어 존재하지 않는 경우
                    header('location: general?category=' . $_GET['category']);

                    // '페이지가 제대로 리디렉션되지 않음'을 해결하기 위해 추가
                    exit();
                }
            }

            header('location: generalview?category=' . $_GET['category'] . '&id=' . $_GET['id']);
        }

        /* 대댓글 등록 */
        if (isset($_POST['child_comment'])) {
            // 불필요한 공백이나 개행을 제거한다.
            $_POST['child_comment'] = trim($_POST['child_comment']);
        
            // 공백으로만 이루어진 경우에는 처리하지 않는다.
            if ($_POST['child_comment'] !== '') { 
                $base_comment = $this->generalCommentsTable->findById($_POST['base_comment_id']);
                if ($base_comment) {    // 해당 댓글이 존재하는 경우
                    $this->generalCommentsTable->insert([
                        'userid' => $user['id'],
                        'text' => $_POST['child_comment'],
                        'date' => new DateTime(),
                        'likes' => 0,
                        'class' => 1,                       // 댓글 0, 대댓글 1
                        'group' => $base_comment['group'],
                        'categoryid' => $_GET['category'],
                        'generalid' => $_GET['id']
                    ]);
                } else {    // 댓글이 삭제되어 존재하지 않는 경우
                }
            }

            header('location: generalview?category=' . $_GET['category'] . '&id=' . $_GET['id'] 
                        . '&i=' . ($_GET['i'] ?? 0) . '&j_' . $_POST['base_comment_id'] . '=0');
        }

        /*================================================================================================*/

        /* '좋아요'를 클릭한 경우 */
        if (isset($_POST['comment_likes'])) {
            // 작성자가 회원탈퇴를 한 경우 반응을 하지 않는다.
            if ($this->getCommentWriter($_POST['comment_likes_id'])) {
                // 해당 댓글에 좋아요를 한 이력을 확인한다.
                $isLikesCommitted = false;
                $likesList = $this->generalCommentsLikesTable->find('generalcommentsid', $_POST['comment_likes_id']);
                foreach ($likesList as $likes) {
                    if ($likes['userid'] == $user['id']) {
                        $isLikesCommitted = true;
                        break;
                    }
                }
                // 좋아요는 한 번만 클릭할 수 있도록 제한한다.
                if (!$isLikesCommitted) {
                    // generalcommentslikes 룩업 테이블에 추가한다.
                    $this->generalCommentsLikesTable->insert([
                        'userid' => $user['id'],
                        'generalcommentsid' => $_POST['comment_likes_id']
                    ]);

                    // 댓글의 likes 칼럼의 값 1 증가
                    $this->IncreaseOne($this->generalCommentsTable, $_POST['comment_likes_id'], 'likes');
                }
            }
            
            header('location: generalview?' . $this->getURLQueryString());
        }

        /* '싫어요'를 클릭한 경우 */
        if (isset($_POST['comment_dislikes'])) {
            /**
             *  게시판이 방출기능을 허용한 경우에만 동작한다.
             *  다수가 싫어요를 누른 경우에 해당 글은 삭제된다.
             */
            $category = $this->generalCategoryTable->findById($_GET['category']);
            if ($category) {
                if ($category['expel'] == 'Y') {

                    // 해당 댓글에 싫어요를 한 이력을 확인한다.
                    $isDislikesCommitted = false;
                    $dislikesList = $this->generalCommentsDislikesTable->find('generalcommentsid', $_POST['comment_dislikes_id']);
                    foreach ($dislikesList as $dislikes) {
                        if ($dislikes['userid'] == $user['id']) {
                            $isDislikesCommitted = true;
                            break;
                        }
                    }

                    // 싫어요는 한 번만 클릭할 수 있도록 제한한다.
                    if (!$isDislikesCommitted) {

                        // generalcommentsdislikes 룩업 테이블에 추가한다.
                        $this->generalCommentsDislikesTable->insert([
                            'userid' => $user['id'],
                            'generalcommentsid' => $_POST['comment_dislikes_id']
                        ]);

                        // 댓글의 likes 칼럼의 값 1 감소
                        $this->DecreaseOne($this->generalCommentsTable, $_POST['comment_dislikes_id'], 'likes');

                        $commentInfo = $this->generalCommentsTable->findById($_POST['comment_dislikes_id']);
                        if ($commentInfo['likes'] < -(int)($category['users'] / 2)) {

                            // 해당 글 작성자 방출 (회원탈퇴한 사용자가 아닌 경우) 
                            $writer = $this->getCommentWriter($_POST['comment_dislikes_id']);
                            if ($writer) {
                                $this->categoryExpel($writer);
                            } 

                            // 해당 댓글 삭제
                            if ($commentInfo['class'] == 0) {
                                $this->deleteBaseComment($_POST['comment_dislikes_id']);
                            }
                            // 해당 대댓글 삭제 
                            else {
                                $this->deleteChildComment($_POST['comment_dislikes_id']);
                            }
                        }
                    }
                }
            } else {    // // 해당 게시판이 삭제된 경우
                header('location: generalcategory');
            }

            header('location: generalview?' . $this->getURLQueryString());
        }

        /* ssexpel (stranger) 클릭한 경우 */
        if (isset($_POST['comment_stranger'])) {
            // 작성자가 회원탈퇴를 한 경우 반응을 하지 않는다.
            if ($this->userTable->findById($_POST['comment_stranger_userid'])) {
                // 해당 댓글에 방출을 누른 이력을 확인한다.
                $isExpelCommitted = false;
                $expelList = $this->generalCommentsExpelTable->find('generalcommentsid', $_POST['comment_stranger_id']);
                foreach ($expelList as $expel) {
                    if ($expel['userid'] == $user['id']) {
                        $isExpelCommitted = true;
                        break;
                    }
                }
                // 방출 버튼을 한 번만 클릭할 수 있도록 제한한다.
                if (!$isExpelCommitted) {
                    // generalcommentsexpel 룩업 테이블에 추가한다.
                    $this->generalCommentsExpelTable->insert([
                        'userid' => $user['id'],
                        'generalcommentsid' => $_POST['comment_stranger_id']
                    ]);

                    // ssexpel 기능을 수행한다.
                    $this->ssexpelUser($_POST['comment_stranger_userid']);
                }
            }

            header('location: generalview?' . $this->getURLQueryString());
        }

        /*================================================================================================*/

        /* 내가 쓴 글을 삭제하는 경우 */
        if (isset($_POST['remove_general'])) {
            $this->deleteGeneral($_GET['id']);
            header('location: general?category=' . $_GET['category']);
        }

        /* 댓글을 삭제하는 경우 */
        if (isset($_POST['base_comment_delete'])) {
            $this->deleteBaseComment($_POST['base_comment_delete_id']);
            header('location: generalview?' . $this->getURLQueryString());
        }

        /* 대댓글을 삭제하는 경우 */
        if (isset($_POST['child_comment_delete'])) {
            $this->deleteChildComment($_POST['child_comment_delete_id']);
            header('location: generalview?' . $this->getURLQueryString());
        }
    }



    /*================================================================================================*/

    /**
     *  대댓글 삭제
     */
    private function deleteChildComment($id) {
        $this->generalCommentsLikesTable->deleteColumn('generalcommentsid', $id);
        $this->generalCommentsDislikesTable->deleteColumn('generalcommentsid', $id);
        $this->generalCommentsExpelTable->deleteColumn('generalcommentsid', $id);

        // 대댓글 삭제
        $this->generalCommentsTable->delete($id); 
    }

    /**
     *  댓글 삭제
     */
    private function deleteBaseComment($id) {
        $baseComment = $this->generalCommentsTable->findById($id);
        if ($baseComment) {
            // 함께 그룹(group)으로 묶여있는 대댓글들을 삭제한다.
            $deleteCommentList = $this->generalCommentsTable->findAndOptionDistinct([
                'group' => $baseComment['group'],
                'generalid' => $baseComment['generalid']
            ], [ 'id' ]);
            foreach ($deleteCommentList as $comment) {
                if ($comment['id'] != $id) {
                    $this->deleteChildComment($comment['id']);
                }
            }

            // 댓글 삭제
            $this->deleteChildComment($id);

            // 글의 comments 값을 1 감소시킨다.  
            $this->DecreaseOne($this->generalTable, $baseComment['generalid'], 'comments');
        }
    }

    /**
     *  글 삭제
     */
    private function deleteGeneral($id) {
        $generalCommentList = $this->generalCommentsTable->find('generalid', $id);
        if ($generalCommentList) {
            foreach ($generalCommentList as $generalComment) {
                if ($generalComment['class'] == 0) {    // 댓글 삭제
                    $this->deleteBaseComment($generalComment['id']);
                }
            }
        }

        $this->generalLikesTable->deleteColumn('generalid', $id);
        $this->generalDislikesTable->deleteColumn('generalid', $id);
        $this->generalExpelTable->deleteColumn('generalid', $id);

        // 이미지 삭제
        $imgList = $this->generalImgTable->find('generalid', $id);
        if ($imgList) {
            $dir = __DIR__ . '/' . '../../file/images/general/'; 
            foreach ($imgList as $img) {
                if (file_exists($dir . $img['path'])) {
                    unlink($dir . $img['path']);
                }
            } 
            $this->generalImgTable->delete($id);
        }

        // 글 삭제
        $this->generalTable->delete($id);
    }

    /**
     *  게시판 삭제
     */
    private function deleteGeneralCategory($id) {
        $generalList = $this->generalTable->find('categoryid', $id);
        if ($generalList) {
            foreach ($generalList as $general) {    // 글 삭제
                $this->deleteGeneral($general['id']);
            } 
        }
        
        $this->generalCategoryBookmarkTable->deleteColumn('generalcategoryid', $id);
        $this->generalCategoryExpelTable->deleteColumn('generalcategoryid', $id);

        // 게시판 삭제
        $this->generalCategoryTable->delete($id);
    }

    /*================================================================================================*/

    /**
     *  특정 사용자가 작성한 댓글 또는 대댓글을 모두 삭제한다.
     *
     *  $class
     *      - [0]   특정 사용자가 작성한 댓글을 모두 삭제한다. 
     *      - [1]   특정 사용자가 작성한 대댓글을 모두 삭제한다.
     *      - [2]   특정 사용자가 작성한 댓글 또는 대댓글을 모두 삭제한다.
     */
    private function deleteUserComment($userid, $class = 2) {
        $generalCommentList = $this->generalCommentsTable->find('userid', $userid);
        $generalClassCommentList = [];
        if ($generalCommentList) {
            foreach ($generalCommentList as $generalComment) {
                if ($generalComment['class'] == $class) {
                    $generalClassCommentList[] = $generalComment;
                } 
            }
        }

        if ($class != 1) {              // 댓글을 삭제하는 경우
            foreach ($generalClassCommentList as $generalClassComment) {
                $this->deleteBaseComment($generalClassComment['id']);
            }
        }
        
        if ($class != 0) {       // 대댓글을 삭제하는 경우
            foreach ($generalClassCommentList as $generalClassComment) {
                $this->deleteChildComment($generalClassComment['id']);
            }
        }
    }

    /**
     *  특정 사용자가 작성한 글을 모두 삭제한다.
     */
    private function deleteUserGeneral($userid) {
        $generalList = $this->generalTable->find('userid', $userid);
        if ($generalList) {
            foreach ($generalList as $general) {
                $this->deleteGeneral($general['id']);
            }
        }
    }

    /**
     *  특정 사용자가 생성한 게시판을 모두 삭제한다.
     */
    private function deleteUserGeneralCategory($userid) {
        $generalCategoryList = $this->generalCategoryTable->find('userid', $userid);
        if ($generalCategoryList) {
            foreach ($generalCategoryList as $generalCategory) {
                $this->deleteGeneralCategory($generalCategory['id']);
            }
        }
    }

    /*================================================================================================*/


    /**
     *  DatabaseTable 클래스의 find 함수로 구한 값에 작성자 정보를 추가한다
     *  참조 전달을 사용해서 값을 변경한다.
     */
    private function addUsersInfo(array &$findResult) {
        foreach ($findResult as $key => $value) {
            $writer = $this->userTable->findById($value['userid']);
            if ($writer) {
                $findResult[$key]['accountimg'] = $this->getAccountImgFilename($writer['accountimgid']);
                $findResult[$key]['nickname'] = $writer['nickname'];
            } else {    // 작성자가 회원탈퇴를 한 경우
                $findResult[$key]['accountimg'] = 'ghost';
                $findResult[$key]['nickname'] = '(알 수 없음)';
            }
        }
    }

    /**
     *  DatabaseTable 클래스의 findById 함수로 구한 값에 작성자 정보를 추가한다
     *  참조 전달을 사용해서 값을 변경한다.
     */
    private function addUserInfo(array &$findByIdResult) {
        $writer = $this->userTable->findById($findByIdResult['userid']);
        if ($writer) {
            $findByIdResult['accountimg'] = $this->getAccountImgFilename($writer['accountimgid']);
            $findByIdResult['nickname'] = $writer['nickname'];
        } else {    // 작성자가 회원탈퇴를 한 경우
            $findByIdResult['accountimg'] = 'ghost';
            $findByIdResult['nickname'] = '(알 수 없음)';
        }
    }

    /**
     *  내 정보 (GET)
     */
    public function myInfo() {
        $addStyle = <<<_END
            <link rel="stylesheet" href="{$this->CSS_LOCATION}/myinfo.styles.css">
            <link rel="stylesheet" href="{$this->CSS_LOCATION}/aside.styles.css">
_END;
            
        $addScriptFile = <<<_END
            <script src="{$this->JS_LOCATION}/myinfo_onload.js"></script>
_END;
            
        $title = '내 정보';  

        $user = $this->getUser();

        return [
            'addStyle' => $addStyle,
            'addScriptFile' => $addScriptFile,
            'title' => $title,
            'template' => 'myinfo.html.php',
            'variables' => [
                'section' => [
                    'user' => $user
                ],
                'aside' => [
                    'user' => $user
                ]
            ]
        ];
    }

    /**
     *  내 정보 (POST)
     */
    public function myInfoPost() {
        $user = $this->getUser();
        $mode = $_GET['mode'];
        $valid = true;
        $errors = [];

        switch ($mode) {
            /* 비밀번호 변경 */
            case 'viewChangePw':
                if (isset($_POST['pw_submit'])) {

                    /* 유효성 검증, 오류 메시지 생성 */
                    $errors['cur_pw'] = [];
                    if (empty($_POST['cur_pw'])) {
                        $valid = false;
                        $errors['cur_pw'][] = '현재 비밀번호를 입력해야 합니다.';
                    } else {
                        if (!password_verify($_POST['cur_pw'], $_SESSION['pw'])) {
                            $valid = false;
                            $errors['cur_pw'][] = '현재 비밀번호를 정확하게 입력해 주세요.';
                        }
                    }
                    $errors['new_pw'] = [];
                    if (empty($_POST['new_pw'])) {
                        $valid = false;
                        $errors['new_pw'][] = '새 비밀번호를 입력해야 합니다.';
                    } else {
                        $pw_pattern = '/^[\w!@#$%^&*]{8,16}$/u';
                        if (preg_match($pw_pattern, $_POST['new_pw']) !== 1) {
                            $valid = false;
                            $errors['new_pw'][] = '8~16자 영문 대 소문자, 숫자, 특수문자(!@#$%^&*)를 사용하세요.'; 
                        }
                    }
                    $errors['new_pw_confirm'] = [];
                    if (empty($_POST['new_pw_confirm'])) {
                        $valid = false;
                        $errors['new_pw_confirm'][] = '새 비밀번호를 재입력해야 합니다.';
                    } else {
                        if ($_POST['new_pw_confirm'] !== $_POST['new_pw']) {
                            $valid = false;
                            $errors['new_pw_confirm'][] = '새 비밀번호가 일치하지 않습니다.';
                        }
                    }


                    if ($valid) {   
                        $new_pw = password_hash($_POST['new_pw'], PASSWORD_BCRYPT);
                        $this->userTable->update([
                            'primaryKey' => $user['id'],
                            'pw' => $new_pw
                        ]);
                        
                        if (strpos($_SERVER['REQUEST_URI'], 'Mobile')) {
                            header('location: login');
                        } else {
                            header('location: ../login');
                        }
                    } else {
                        $ret = $this->myInfo();
                        $ret['variables']['section']['errors'] = $errors;
                        return $ret;
                    }
                }
                break;

            /* 닉네임 변경 */
            case 'viewChangeNickname':
                if (isset($_POST['nickname_submit'])) {

                    /* 유효성 검증, 오류 메시지 생성 */
                    $errors['new_nickname'] = [];
                    if (empty($_POST['new_nickname'])) {
                        $valid = false;
                        $errors['new_nickname'][] = '닉네임을 입력해야 합니다.';
                    } else {
                        if (utf8_strlen($_POST['new_nickname']) > 8) {   // 8자를 초과하는 경우
                            $valid = false;
                            $errors['new_nickname'][] = '최대 8자까지만 가능합니다.';
                        }
                    }

                    if ($valid) {   
                        if ($user['nickname'] != $_POST['new_nickname']) {
                            $this->userTable->update([
                                'primaryKey' => $user['id'],
                                'nickname' => $_POST['new_nickname']
                            ]);
                        }
                        
                        header('location: myinfo');

                    } else {
                        $ret = $this->myInfo();
                        $ret['variables']['section']['errors'] = $errors;
                        return $ret;
                    }
                }
                break;

            /* 프로필 이미지 변경 */
            case 'viewChangeAccountimg':
                if (isset($_POST['accountimg_submit'])) {
                    if ($user['accountimgid'] != $_POST['accountimg_id']) {
                        $this->userTable->update([
                            'primaryKey' => $user['id'],
                            'accountimgid' => $_POST['accountimg_id']
                        ]);
                    }
                        
                    header('location: myinfo');
                }
                break;

            /* 회원탈퇴 */
            case 'viewWithdrawal':
                if (isset($_POST['withdrawal_submit'])) {

                    /* 유효성 검증, 오류 메시지 생성 */
                    $errors['withdrawal_cur_pw'] = [];
                    if (empty($_POST['withdrawal_cur_pw'])) {
                        $valid = false;
                        $errors['withdrawal_cur_pw'][] = '현재 비밀번호를 입력해야 합니다.';
                    } else {
                        if (!password_verify($_POST['withdrawal_cur_pw'], $_SESSION['pw'])) {
                            $valid = false;
                            $errors['withdrawal_cur_pw'][] = '현재 비밀번호를 정확하게 입력해 주세요.';
                        }
                    }

                    if ($valid) {
                        /* 성신 인증을 한 사용자인 경우 */
                        if ($user['issungshin'] == 'Y') {
                            // 다른 이용자가 동일한 아이디로 가입할 수 없도록 제한한다.
                            $this->withdrawalTable->insert([
                                'userid' => $user['id']
                            ]);

                            // 즐겨찾기 카테고리 users 칼럼 1 감소
                            $userBookmarks = $this->getUserBookmarks($user);
                            foreach ($userBookmarks as $userBookmark) {
                                $this->DecreaseOne($this->generalCategoryTable, $userBookmark['id'], 'users');
                            }
                        }

                        /* 모든 사용자 공통 처리 */
                        // generalcategorybookmark 데이터 삭제
                        $this->generalCategoryBookmarkTable->delete($user['id']);

                        // 해당 사용자의 게시판 룩업 테이블을 모두 삭제한다.
                        $this->removeUserFromGeneralLookup($user['id']);

                        // 해당 사용자의 시간표 관련 테이블을 모두 삭제한다.
                        $this->removeUserFromSchedule($user['id']);

                        // 해당 사용자의 중고거래 관련 테이블을 모두 삭제한다.
                        $this->removeUserFromTrade($user['id']);

                        $this->userTable->delete($user['id']);
                        $this->logout();
                    } else {
                        $ret = $this->myInfo();
                        $ret['variables']['section']['errors'] = $errors;
                        return $ret;
                    }
                }
                break;
        }
    }

    /**
     *  해당 사용자의 게시판 룩업 테이블을 모두 삭제한다.
     */
    private function removeUserFromGeneralLookup($userid) {
        // 게시판 글 룩업 테이블 데이터 삭제
        $this->generalLikesTable->delete($userid);
        $this->generalDislikesTable->delete($userid);
        $this->generalExpelTable->delete($userid);

        // 게시판 댓글 룩업 테이블 데이터 삭제
        $this->generalCommentsLikesTable->delete($userid);
        $this->generalCommentsDislikesTable->delete($userid);
        $this->generalCommentsExpelTable->delete($userid);
    }

    /**
     *  해당 사용자의 시간표 관련 테이블을 모두 삭제한다.
     */
    private function removeUserFromSchedule($userid) {
        $this->scheduleLookupTable->delete($userid);
        $this->userScheduleTable->delete($userid);
    }

    /**
     *  해당 사용자의 중고거래 관련 테이블을 모두 삭제한다.
     */
    private function removeUserFromTrade($userid) {
        // 작성된 중고거래 글 삭제
        $tradeList = $this->tradeTable->find('userid', $userid);
        if ($tradeList) {
            foreach ($tradeList as $trade) {
                $this->deleteTrade($trade['id']);
            }
        }

        // 중고거래 'expel' 룩업 테이블 데이터 삭제
        $this->tradeExpelTable->delete($userid);
    }


    /**
     *  성신 시간표
     */
    public function schedule() {
        $addStyle = <<<_END
            <link rel="stylesheet" href="{$this->CSS_LOCATION}/schedule.styles.css">
            <link rel="stylesheet" href="{$this->CSS_LOCATION}/aside.styles.css">
_END;
            
        $addScriptFile = <<<_END
            <script src="{$this->JS_LOCATION}/schedule_onload.js"></script>
            <script src="{$this->JS_LOCATION}/schedule_ajax.js"></script>
            <script src="{$this->JS_LOCATION}/xhr.js"></script>
            <script src="{$this->JS_LOCATION}/loading.js"></script>
            <script src="{$this->JS_LOCATION}/util.js"></script>
            <script src="{$this->JS_LOCATION}/schedule_create.js"></script>
_END;
            
        $title = '성신 시간표';  

        $user = $this->getUser();

        return [
            'addStyle' => $addStyle,
            'addScriptFile' => $addScriptFile,
            'title' => $title,
            'template' => 'schedule.html.php',
            'variables' => [
                'section' => [
                    'user' => $user
                ],
                'aside' => [
                    'user' => $user
                ]
            ]
        ];
    }


    /**
     *  성신 중고거래
     */
    public function trade() {
        $addStyle = <<<_END
            <link rel="stylesheet" href="{$this->CSS_LOCATION}/trade.styles.css">
            <link rel="stylesheet" href="{$this->CSS_LOCATION}/aside.styles.css">
_END;
            
        $addScriptFile = <<<_END
            <script src="{$this->JS_LOCATION}/jquery.js"></script>
            <script src="{$this->JS_LOCATION}/util.js"></script>
            <script src="{$this->JS_LOCATION}/loading.js"></script>
            <script src="{$this->JS_LOCATION}/trade_jquery.js"></script>
            <script src="{$this->JS_LOCATION}/trade_onload.js"></script>
_END;
            
        $title = '성신 중고거래';  

        $user = $this->getUser();
        
        // 카테고리 목록
        $tradeCategoryList = $this->tradeCategoryTable->findAll();

        /**
         *  URL 조작을 금지한다.
         */ 
        $tradeCampusIndexList = [0, 1, 2];
        if (isset($_GET['campus'])) {
            if (!is_numeric($_GET['campus']) 
                    || !in_array($_GET['campus'], $tradeCampusIndexList)) {
                header('location: trade'); 
            }
        }
        $tradeCategoryIndexList = [];
        for ($i = 0; $i <= count($tradeCategoryList); $i++) { 
            $tradeCategoryIndexList[] = $i; 
        }
        if (isset($_GET['category'])) {
            if (!is_numeric($_GET['category']) 
                    || !in_array($_GET['category'], $tradeCategoryIndexList)) {
                header('location: trade'); 
            }
        }

        // 검색창의 불필요한 공백은 제거한다.
        if (isset($_GET['search'])) {
            $_GET['search'] = trim($_GET['search']);
        }

        /* 중고거래 글 목록 */
        $tradeContentList = $this->tradeTable->getTradeList($_GET['campus'], $_GET['category'], $_GET['search']); 

        // 카테고리 정보 가져오기
        $CategoryDetailInfoList = $this->tradeCategoryTable->findAll();
        $tradeCategoryNameList = [];
        foreach ($CategoryDetailInfoList as $info) {
            $tradeCategoryNameList[$info['id']] = $info['category'];
        }
        foreach ($tradeContentList as $key => $content) {
            $tradeContentList[$key]['category'] = $tradeCategoryNameList[$content['categoryid']];
        }

        /* 중고거래 글에 대응하는 이미지 목록 */
        $tradeImgFileList = $this->tradeTable->getTradeImgList($_GET['campus'], $_GET['category'], $_GET['search']);

        return [
            'addStyle' => $addStyle,
            'addScriptFile' => $addScriptFile,
            'title' => $title,
            'template' => 'trade.html.php',
            'variables' => [
                'section' => [
                    'user' => $user,
                    'tradeCategoryList' => $tradeCategoryList,
                    'tradeContentList' => $tradeContentList,
                    'tradeImgFileList' => $tradeImgFileList
                ],
                'aside' => [
                    'user' => $user
                ]
            ]
        ];
    }


    /**
     *  성신 중고거래 (POST)
     */
    public function tradePost() {
        $user = $this->getUser();

        if (isset($_POST['trade_title'])) {

            // 이미지 구분 아이디
            $findResult = $this->tradeImgTable->findAndOptionDistinct([
                'userid' => $user['id'],
                'no' => 0
            ], [ 'id' ]);
            if ($findResult) {
                $imgId = $findResult[count($findResult)-1]['id'] + 1;
            } else {
                $imgId = 0;
            }

            // 선택한 캠퍼스 구하기
            if ($_POST['campus_1'] && $_POST['campus_2']) {
                $campus = 'B';
            } else if ($_POST['campus_1']) {
                $campus = 'S';
            } else {
                $campus = 'U';
            }

            // 업로드한 이미지 파일을 서버에 저장하고 실행 결과를 반환한다.
            $saveUploadImageFilesResults = $this->saveUploadImageFiles($_FILES['add_file'],      
                                                'trade', ($user['id'] . '_' . $imgId), true, 500);

            if ($saveUploadImageFilesResults['upload_success']) {
                // 중간에 서버 저장에 실패해서 파일의 일부가 누락된 경우, 누락된 파일은 무시하고 진행한다.
                if (count($saveUploadImageFilesResults['path']) > 0) {

                    // 이미지 파일 경로와 넓이 정보를 테이블에 저장한다.
                    foreach ($saveUploadImageFilesResults['path'] as $key => $path) {
                        $this->tradeImgTable->insert([
                            'userid' => $user['id'],
                            'id' => $imgId,
                            'no' => $key,
                            'path' => $path,
                            'width' => $saveUploadImageFilesResults['width'][$key]
                        ]);
                    }
                }
            }
            
            // 적어도 하나의 파일이 존재하는 경우에만 글을 등록한다.
            if ($this->tradeImgTable->findAndOptionDistinct([
                'userid' => $user['id'],
                'id' => $imgId
            ], [ 'no' ])) {
                $this->tradeTable->insert([
                    'userid' => $user['id'],
                    'categoryid' => $_POST['trade_category'],
                    'title' => trim($_POST['trade_title']),
                    'price' => $_POST['trade_price'],
                    'info' => trim($_POST['trade_info']),
                    'imgid' => $imgId,
                    'campus' => $campus,
                    'date' => new DateTime(),
                    'expel' => 0
                ]);
            }

            // 폼 처리를 완료하고 페이지를 이동시켜야지 폼 재전송이 발생하지 않는다.    
            header('location: trade');
        }
    }

    /*================================================================================================*/

    /**
     *  중고거래 글 삭제
     */
    private function deleteTrade($id) {
        $deleteTradeInfo = $this->tradeTable->findById($id);
        
        // 이미지 삭제
        $imgList = $this->tradeImgTable->findAndOptionDistinct([
            'userid' => $deleteTradeInfo['userid'],
            'id' => $deleteTradeInfo['imgid']
        ], [ 'path' ]);
        if ($imgList) {
            $dir = __DIR__ . '/' . '../../file/images/trade/'; 
            foreach ($imgList as $img) {
                if (file_exists($dir . $img['path'])) {
                    unlink($dir . $img['path']);
                }
            } 
            $this->tradeImgTable->deleteLookup($deleteTradeInfo['imgid'], 'userid', $deleteTradeInfo['userid']);
        }

        // 글 삭제
        $this->tradeTable->delete($id);

        // 'expel' 룩업 테이블 삭제
        $this->tradeExpelTable->deleteColumn('tradeid', $id);
    }

    /*================================================================================================*/


    /**
     *  글 관리
     */
    public function myArticle() {
        $addStyle = <<<_END
            <link rel="stylesheet" href="{$this->CSS_LOCATION}/myarticle.styles.css">
            <link rel="stylesheet" href="{$this->CSS_LOCATION}/myarticle_{$_GET['select']}.styles.css">
            <link rel="stylesheet" href="{$this->CSS_LOCATION}/aside.styles.css">
_END;
            
        $addScriptFile = <<<_END
            <script src="{$this->JS_LOCATION}/util.js"></script>
            <script src="{$this->JS_LOCATION}/jquery.js"></script>
            <script src="{$this->JS_LOCATION}/myarticle_jquery.js"></script>
_END;
            
        $title = '글 관리';  

        $user = $this->getUser();

        // 글 목록 이동 위치 초기화
        setcookie('generalScrollTop', '0', ['samesite' => 'None', 'secure' => true]);

        // 글 정보
        $contentList = false;
        switch($_GET['select']) {
            case 'general':
                $contentList = $this->generalTable->find('userid', $user['id']);

                // 카테고리 정보 가져오기
                $CategoryDetailInfoList = $this->generalCategoryTable->findAll();
                $generalCategoryNameList = [];

                foreach ($CategoryDetailInfoList as $info) {
                    $generalCategoryNameList[$info['id']] = $info['name'];
                }
                foreach ($contentList as $key => $content) {
                    $contentList[$key]['category'] = $generalCategoryNameList[$content['categoryid']];
                }

                break;
            case 'generalcomment':
                $contentList = $this->generalCommentsTable->find('userid', $user['id']);

                $isUnsetActivate = false;   // unset 함수 사용 여부

                // 카테고리 정보 가져오기
                $CategoryDetailInfoList = $this->generalCategoryTable->findAll();
                $generalCategoryNameList = [];

                foreach ($CategoryDetailInfoList as $info) {
                    $generalCategoryNameList[$info['id']] = $info['name'];
                }

                foreach ($contentList as $key => $content) {
                    /**
                     *  해당 댓글 페이지 위치 정보 추출 
                     *
                     *      - baseid    : 댓글 id
                     *      - page      : 페이지 정보
                     */
                    /* 해당 글의 댓글 목록 */
                    $totalComment = $this->generalCommentsTable->findAndOptionDistinct([
                        'class' => 0,
                        'generalid' => $content['generalid'] 
                    ], [ 'id' ]);
                    $totalComment = array_reverse($totalComment);
                    /* 댓글의 위치 순서 */
                    if ($content['class'] == 0) {   // 댓글
                        $contentList[$key]['baseid'] = $content['id'];
                    } else {    // 대댓글 (상위 댓글 정보 추출)
                        $result = $this->generalCommentsTable->findAndOptionDistinct([
                            'class' => 0,
                            'group' => $content['group'],
                            'generalid' => $content['generalid']
                        ], [ 'id' ]);
                        if ($result) {
                            $contentList[$key]['baseid'] = $result[0]['id'];
                        }
                        else {  // 대댓글의 상위 댓글이 삭제되어 존재하지 않는 경우
                            unset($contentList[$key]);
                            $isUnsetActivate = true;
                        }
                    }
                    $order = 0;
                    foreach ($totalComment as $comment) {
                        if ($comment['id'] == $contentList[$key]['baseid']) { break; }
                        $order++;
                    }
                    $contentList[$key]['page'] = (int)($order / 10);


                    /* 카테고리 정보 가져오기 */
                    $contentList[$key]['category'] = $generalCategoryNameList[$content['categoryid']];

                }   // end-of-foreach

                if ($isUnsetActivate) { 
                    changeToSequentialArray($contentList);
                }
                break;
            case 'trade':
                $contentList = $this->tradeTable->find('userid', $user['id']);

                /* 카테고리 정보 가져오기 */
                $CategoryDetailInfoList = $this->tradeCategoryTable->findAll();
                $tradeCategoryNameList = [];

                foreach ($CategoryDetailInfoList as $info) {
                    $tradeCategoryNameList[$info['id']] = $info['category'];
                }

                foreach ($contentList as $key => $content) {

                    /* 첫 번째 이미지 정보 가져오기 */
                    $imgPath = $this->tradeImgTable->findAndOptionDistinct([
                        'userid' => $content['userid'],
                        'id' => $content['imgid'],
                        'no' => 0
                    ], [ 'path' ])[0]['path'];
                    if (!$imgPath) {    // 이미지가 존재하지 않는 경우
                        $imgPath = 'image.png';
                    }
                    
                    $contentList[$key]['path'] = $imgPath;

                    /* 카테고리 정보 가져오기 */
                    $contentList[$key]['category'] = $tradeCategoryNameList[$content['categoryid']];

                    /* 캠퍼스 정보 가져오기 */
                    if ($content['campus'] == 'S') { 
                        $contentList[$key]['campus'] = '수정 캠퍼스'; 
                    } else if ($content['campus'] == 'U') { 
                        $contentList[$key]['campus'] = '운정 캠퍼스'; 
                    } else {
                        $contentList[$key]['campus'] = '수정 / 운정 캠퍼스';
                    }
                }
                break;
        }
        // 배열을 뒤집어서 최신 글이 위에 있도록 설정한다.
        $contentList = array_reverse($contentList);

        return [
            'addStyle' => $addStyle,
            'addScriptFile' => $addScriptFile,
            'title' => $title,
            'template' => 'myarticle.html.php',
            'variables' => [
                'section' => [
                    'user' => $user,
                    'contentList' => $contentList
                ],
                'aside' => [
                    'user' => $user
                ]
            ]
        ];
    }


    /**
     *  글 관리 (POST)
     */
    public function myArticlePost() {

        /* Redirect Location 설정 */
        if ($_GET['select'] == 'trade') {
            $location = 'myarticle?select=trade';
            if (isset($_GET['i'])) {
                $location .= '&i=' . $_GET['i'];
            }
        }

        /* 거래완료 (글 삭제) */
        if ($_POST['tradeOpt'] == 'delete') {
            $this->deleteTrade($_POST['tradeId']);
        } 
        /* 끌어올리기 */
        else if ($_POST['tradeOpt'] == 'update') {
            $prevTrade = $this->tradeTable->findById($_POST['tradeId']);
            if ($prevTrade) {
                // 기존 데이터 삭제
                $this->tradeTable->delete($_POST['tradeId']);

                // 새로운 데이터 생성
                $this->tradeTable->insert([
                    'userid' => $prevTrade['userid'],
                    'categoryid' => $prevTrade['categoryid'],
                    'title' => $prevTrade['title'],
                    'price' => $prevTrade['price'],
                    'info' => $prevTrade['price'],
                    'imgid' => $prevTrade['imgid'],
                    'campus' => $prevTrade['campus'],
                    'date' => new DateTime(),
                    'expel' => $prevTrade['expel']
                ]);
            }
        }
        /* 가격 변경 */
        else if ($_POST['tradeOpt'] == 'changePrice') {
            $this->tradeTable->update([
                'primaryKey' => $_POST['tradeId'],
                'price' => $_POST['tradePrice']
            ]);
        }

        /* Redirect */
        if ($_GET['select'] == 'trade') {
            header('location: ' . $location);
        }
    }


    /**
     *  로그인 상태를 로그아웃 상태로 바꾼다. 
     */
    public function logout() {
        // 세션 변수의 값을 비운다.
        $_SESSION = [];
        // 세션 쿠키를 파기한다.
        if (isset($_COOKIE[session_name()])) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time()-36000, $params['path']);
        }
        // 세션을 파기한다.
        session_destroy();
        header('location: about');
    }

    public function sungshinError() {
        $addStyle = <<<_END
            <link rel="stylesheet" href="{$this->CSS_LOCATION}/error.styles.css">
            <link rel="stylesheet" href="{$this->CSS_LOCATION}/aside.styles.css">
_END;

        $user = $this->getUser();

        // 성신 인증 후 뒤로가기로 해당 페이지 이동 시
        if ($user['issungshin'] == 'Y') {
            header('location: about');
            exit();
        }

        return [
            'addStyle' => $addStyle,
            'title' => '성신 인증 오류',
            'template' => 'error/sungshinerror.html.php',
            'variables' => [
                'aside' => [
                    'user' => $user
                ]
            ]
        ];
    }

    public function loginError() {
        $addStyle = <<<_END
            <link rel="stylesheet" href="{$this->CSS_LOCATION}/error.styles.css">
            <link rel="stylesheet" href="{$this->CSS_LOCATION}/aside.styles.css">
_END;

        // 로그인 후 뒤로가기로 해당 페이지 이동 시
        $user = $this->getUser();
        if ($user) {
            header('location: about');
            exit();
        }

        return [
            'addStyle' => $addStyle,
            'title' => '로그인 오류',
            'template' => 'error/loginerror.html.php'
        ];
    }

} // END OF 'SsHomeController' CLASS


/**
 *  반환값 형식 (예시)
 *
return [
    'addStyle' => $addStyle,
    'addScriptFile' => $addScriptFile,
    'addScriptCode' => $addScriptCode,
    'title' => $title,
    'template' => 'general.html.php',
    'variables' => [
        'aside' => [
        ],
        'section' => [
        ]
    ]
];
 */

?>
