<?php

require_once __DIR__ . '/' . '../includes/lib/defineMaxExpel.php';
require_once __DIR__ . '/' . '../includes/lib/util.php';
require_once __DIR__ . '/' . '../includes/lib/modifyImage.php';
require_once __DIR__ . '/' . '../includes/lib/schedule.php';
require_once __DIR__ . '/' . '../includes/lib/genRandStr.php';

// 보안 코드
define("SECURE_CODE", "********");

class AndroidController {
    private $userTable;

    private $generalCategoryTable;
    private $generalCategoryBookmarkTable;
    private $generalCategoryExpelTable;

    private $generalTable;
    private $generalLikesTable;
    private $generalDislikesTable;
    private $generalExpelTable; 
    private $generalImgTable; 

    private $generalCommentsTable;
    private $generalCommentsLikesTable;
    private $generalCommentsDislikesTable; 
    private $generalCommentsExpelTable;

    private $scheduleTable;
    private $scheduleLookupTable;
    private $userScheduleTable;

    private $tradeCategoryTable;
    private $tradeTable;
    private $tradeImgTable; 
    private $tradeExpelTable;

    private $chatTable; 
    private $chatBlockTable;
    private $chatTextTable;
    private $chatFileTable;

    public function __construct(DatabaseTable $userTable, 
        DatabaseTable $generalCategoryTable, 
        DatabaseTable $generalCategoryBookmarkTable,
        DatabaseTable $generalCategoryExpelTable,
        DatabaseTable $generalTable,
        DatabaseTable $generalLikesTable,
        DatabaseTable $generalDislikesTable,
        DatabaseTable $generalExpelTable,
        DatabaseTable $generalImgTable,
        DatabaseTable $generalCommentsTable,
        DatabaseTable $generalCommentsLikesTable,
        DatabaseTable $generalCommentsDislikesTable,
        DatabaseTable $generalCommentsExpelTable,
        DatabaseTable $scheduleTable,
        DatabaseTable $scheduleLookupTable, 
        DatabaseTable $userScheduleTable,
        DatabaseTable $tradeCategoryTable, 
        DatabaseTable $tradeTable, 
        DatabaseTable $tradeImgTable, 
        DatabaseTable $tradeExpelTable,
        DatabaseTable $chatTable,
        DatabaseTable $chatBlockTable,
        DatabaseTable $chatTextTable,
        DatabaseTable $chatFileTable) {
        
        $this->userTable = $userTable;
        $this->generalCategoryTable = $generalCategoryTable;
        $this->generalCategoryBookmarkTable = $generalCategoryBookmarkTable;
        $this->generalCategoryExpelTable = $generalCategoryExpelTable;
        $this->generalTable = $generalTable;
        $this->generalLikesTable = $generalLikesTable;
        $this->generalDislikesTable = $generalDislikesTable;
        $this->generalExpelTable = $generalExpelTable; 
        $this->generalImgTable = $generalImgTable; 
        $this->generalCommentsTable = $generalCommentsTable;
        $this->generalCommentsLikesTable = $generalCommentsLikesTable;
        $this->generalCommentsDislikesTable = $generalCommentsDislikesTable; 
        $this->generalCommentsExpelTable = $generalCommentsExpelTable;
        $this->scheduleTable = $scheduleTable;
        $this->scheduleLookupTable = $scheduleLookupTable;
        $this->userScheduleTable = $userScheduleTable;
        $this->tradeCategoryTable = $tradeCategoryTable;
        $this->tradeTable = $tradeTable;
        $this->tradeImgTable = $tradeImgTable; 
        $this->tradeExpelTable = $tradeExpelTable;
        $this->chatTable = $chatTable; 
        $this->chatBlockTable = $chatBlockTable;
        $this->chatTextTable = $chatTextTable;
        $this->chatFileTable = $chatFileTable;
    }

    /** 보안 코드 **/
    private function secure(string $code): bool {

        if ($code != SECURE_CODE) {
            return false;
        } else {
            return true; 
        }
    }

    /** 사용자가 존재 여부 반환 */
    private function isUserExist(string $id): bool {
        
        $user = $this->userTable->findById(strtolower($id));

        if ($user)
            return true;
        else
            return false;
    }


    /** 성신 인증 여부 반환 */
    private function isSungshin(string $id): bool {

        $user = $this->userTable->find('id', strtolower($id));

        if ($user[0]['issungshin'] === 'Y')
            return true;
        else
            return false;
    }

    /**
     *  접속한 사용자의 정보 출력
     */
    public function user() {

        /* 보안 코드 */
        if (!$this->secure($_POST['secure'])) {
            return [
                'result' => false,
                'msg' => "SECURE ERROR"
            ];
        } 

        $user = $this->userTable->find('id', strtolower($_POST['id']));
        
        // MainActivity User Check
        if ($_POST['pw'] == $user[0]['pw'] && $user[0]['issungshin'] == 'Y') {
            return [
                'result' => true,
                'user' => [
                    'nickname' => $user[0]['nickname'],
                    'accountimgid' => $user[0]['accountimgid']
                ]
            ];
        }

        // LoginActivity User Check
        if (empty($user)) {
            return [
                'result' => false,
                'msg' => "ID ERROR"
            ];
        } else if (!password_verify($_POST['pw'], $user[0]['pw'])){
            return [
                'result' => false,
                'msg' => "PW ERROR"
            ];
        } else if ($user[0]['issungshin'] != 'Y') {
            return [
                'result' => false,
                'msg' => "SUNGSHIN ERROR"
            ];
        } else {
            return [
                'result' => true,
                'user' => [
                    'id' => $user[0]['id'],
                    'pw' => $user[0]['pw'],
                    'issungshin' => ($user[0]['issungshin'] == 'Y') ? true : false,
                    'nickname' => $user[0]['nickname'],
                    'accountimgid' => $user[0]['accountimgid'],
                ]
            ];
        }
    }

    /**
     *  접속한 사용자의 즐겨찾기 게시판 카테고리 출력
     */
    public function userCategory() {

        /* 보안 코드 */
        if (!$this->secure($_GET['secure'])) {
            return [
                'result' => false,
                'msg' => "SECURE ERROR"
            ];
        } 

        $bookmarks = [];
        // 사용자가 만든 게시판 목록
        $bookmarks = $this->generalCategoryTable->find('userid', $_GET['id']);
        // 즐겨찾기에 추가한 게시판 목록
        foreach ($this->generalCategoryBookmarkTable->find('userid', $_GET['id']) as $result) {
            if ($result['generalcategoryid'] == 1) {    // 자유게시판 제외

                // 자유게시판 이용자 수
                $defaultUsers = 
                    ($this->generalCategoryTable->findById($result['generalcategoryid']))['users'];

                continue;
            }

            $bookmarks[] = $this->generalCategoryTable->findById($result['generalcategoryid']);
        }

        $response = [];

        // 자유게시판 이용자 수
        $response['defaultUsers'] = (int)$defaultUsers;

        // 접속한 사용자의 즐겨찾기 게시판 카테고리
        foreach ($bookmarks as $index => $bookmark) {
            // 카테고리 데이터를 JSON 포맷으로 변환한다.
            $response['bookmarks'][$index] = $this->categoryJsonFormat($bookmark);
        }

        return $response;
    }

    /**
     *  사용자가 검색한 게시판 카테고리 목록 출력
     */
    public function searchGeneralCategory() {

        /* 보안 코드 */
        if (!$this->secure($_GET['secure'])) {
            return [
                'result' => false,
                'msg' => "SECURE ERROR"
            ];
        } 

        // 성신 인증을 받지 않은 사용자는 검색을 허용하지 않는다.
        if (!$this->isSungshin($_GET['id'])) {
            return [];
        }

        // 문자열의 앞뒤의 불필요한 공백이나 개행을 제거한다.
        $search = trim($_GET['search']);

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
        $excludes = $this->getUserBookmarks($_GET['id']);
        foreach ($this->generalCategoryExpelTable->find('userid', $_GET['id']) as $result) {
            $excludes[] = $this->generalCategoryTable->findById($result['generalcategoryid']);
        }

        foreach ($excludes as $exclude) {
            $index = array_search($exclude, $results);
            if ($index !== false) {
                unset($results[$index]);
            }
        }

        // 인덱스를 다시 0부터 1씩 증가하는 순서로 맞추어준다.
        changeToSequentialArray($results);

        // 카테고리 데이터를 JSON 포맷으로 변환한다.
        foreach ($results as $index => $category) {
            $results[$index] = $this->categoryJsonFormat($category); 
        }

        return $results;
    }

    private function categoryJsonFormat(array $category): array {
        $result = [];

        $result['id'] = (int)$category['id'];
        $result['userid'] = $category['userid'];
        $result['name'] = $category['name'];
        $result['info'] = $category['info'];
        $result['expel'] = ($category['expel'] == 'Y') ? true : false;
        $result['users'] = (int)$category['users'];

        return $result;
    }

    private function getUserBookmarks($userid) {
        $bookmarks = [];
        // 사용자가 만든 게시판 목록
        $bookmarks = $this->generalCategoryTable->find('userid', $userid);
        // 즐겨찾기에 추가한 게시판 목록
        foreach ($this->generalCategoryBookmarkTable->find('userid', $userid) as $result) {
            $bookmarks[] = $this->generalCategoryTable->findById($result['generalcategoryid']);
        }
        return $bookmarks;
    }


    /**
     *  즐겨찾기 추가
     */
    public function addCategoryBookmark() {

        /* 보안 코드 */
        if (!$this->secure($_POST['secure'])) {
            return [
                'result' => false,
                'msg' => "SECURE ERROR"
            ];
        } 

        // 사용자가 유효하고, 해당 게시판이 삭제되지 않은 경우에만 실행한다.
        if ($this->isSungshin($_POST['id']) &&
            $this->generalCategoryTable->findById($_POST['categoryid'])) {

                // 즐겨찾기 룩업 테이블에 추가한다.
                $this->generalCategoryBookmarkTable->insert([
                    'userid' => $_POST['id'],
                    'generalcategoryid' => $_POST['categoryid']
                ]);

                // 게시판의 users 칼럼의 값 1 증가
                $this->IncreaseOne($this->generalCategoryTable, $_POST['categoryid'], 'users');

            return [
                'result' => true        // 성공 시
            ];
        } else {
            return [
                'result' => false       // 실패 시
            ];
        }
    }

    /**
     *  게시판 카테고리 추가
     */
    public function createGeneralCategory() {

        /* 보안 코드 */
        if (!$this->secure($_POST['secure'])) {
            return [
                'result' => false,
                'msg' => "SECURE ERROR"
            ];
        } 

        if (!$this->isSungshin($_POST['id'])) {
            return [
                'result' => false,
                'msg' => "SUNGSHIN ERROR"
            ];
        } else if (count($this->generalCategoryTable->find('name', $_POST['name'])) > 0) {
            return [
                'result' => false,
                'msg' => "NAME ERROR"   // 중복된 게시판 이름이 존재하는 경우
            ];
        } else {
            $user = $this->userTable->findById($_POST['id']);
            if ($user) {
                // 새 게시판의 임시 id 생성
                $tmpId = $user['id'] . '_' . ($user['generalcategorycount'] + 1);

                /* 데이터베이스에 저장하는 코드 구현 */
                $this->generalCategoryTable->insert([
                    'tmpid' => $tmpId,
                    'userid' => $user['id'],
                    'name' => $_POST['name'],
                    'info' => $_POST['info'],
                    'hashtag' => '',
                    'expel' => $_POST['expel'], // (Y/N)
                    'users' => 1
                ]);

                // 새 게시판의 임시 id를 이용해서 id 값을 구한다.
                $findCategoryResult = $this->generalCategoryTable->find('tmpid', $tmpId); 
                if ($findCategoryResult) {  // 게시판 생성 성공 시
                    // userTable 의 'generalcategorycount' 칼럼의 값 1 증가
                    $this->IncreaseOne($this->userTable, $user['id'], 'generalcategorycount');

                    return [
                        'result' => true,
                        'category' => $this->categoryJsonFormat($findCategoryResult[0]) 
                    ];
                } else {
                    return [
                        'result' => false,
                        'msg' => "CATEGORY CREATE ERROR"
                    ];
                }
            } else {
                return [
                    'result' => false,
                    'msg' => "NO USER ERROR"
                ];
            }
        }
    }
    
    /**
     *  즐겨찾기 제거
     */
    public function removeCategoryBookmark() {

        /* 보안 코드 */
        if (!$this->secure($_POST['secure'])) {
            return [
                'result' => false,
                'msg' => "SECURE ERROR"
            ];
        } 

        // 즐겨찾기 제거 가능 여부
        $isRemovable = false;

        $bookmarkUserList = $this->generalCategoryBookmarkTable->find('generalcategoryid', $_POST['categoryid']);
        foreach ($bookmarkUserList as $bookmarkUser) {
            // 즐겨찾기 목록(generalcategorybookmark)에 추가되어 있고 자유게시판이 아닌 경우
            if ($_POST['categoryid'] != 1 && $_POST['id'] == $bookmarkUser['userid']) {
                $isRemovable = true;
                break;
            }
        }

        // 즐겨찾기를 제거한다.
        if ($isRemovable) {

            // 즐겨찾기 룩업 테이블에서 제거한다.
            $this->generalCategoryBookmarkTable->deleteLookup($_POST['id'],
                    'generalcategoryid', $_POST['categoryid']);

            // 해당 게시판이 존재하는 경우에만 실행한다.
            if ($this->generalCategoryTable->findById($_POST['categoryid'])) {

                // 게시판의 users 칼럼의 값 1 감소
                $this->DecreaseOne($this->generalCategoryTable, $_POST['categoryid'], 'users');

                // 게시판에 방출 기능이 설정되어있는 경우, 좋아요 수가 음수면 +1을 한다.
                $this->removeBookmarkLikesUpdate($_POST['categoryid']);
            }
            return [ 'result' => true ];
        } else { return [ 'result' => false ]; }
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
     *  내 정보 설정
     */
    public function myInfo() {

        /* 보안 코드 */
        if (!$this->secure($_POST['secure'])) {
            return [
                'result' => false,
                'msg' => "SECURE ERROR"
            ];
        } 

        $user = $this->userTable->findById(strtolower($_POST['id']));
        if ($user) {
            if (($_POST['nickname'] == $user['nickname']) 
                && ($_POST['accountimgid'] == $user['accountimgid'])) {

                // 값의 변경이 없는 경우
            } else {
                $this->userTable->update([
                    'primaryKey' => $_POST['id'],
                    'nickname' => $_POST['nickname'],
                    'accountimgid' => $_POST['accountimgid']
                ]);
            }
        }

        return [ 'result' => true ];
    }


    /**
     *  게시판 접근 가능 여부를 반환한다.
     */
    private function checkGeneralCategoryAccess($userid, $categoryid): bool {

        // 사용자가 만든 게시판과 즐겨찾기에 추가한 게시판 이외의 게시판은 접근을 허용하지 않는다.
        $isCategoryAccessible = false;
        $bookmarks = $this->getUserBookmarks($userid);
        foreach ($bookmarks as $bookmark) {
            if ($bookmark['id'] === $categoryid) {
                $isCategoryAccessible = true;
                break;
            } 
        }

        return $isCategoryAccessible;
    }

    /**
     *  성신 게시판
     *
     *  id=&categoryid=&option=default
     *  id=&categoryid=&option=search&search=
     */
    public function general() {

        /* 보안 코드 */
        if (!$this->secure($_POST['secure'])) {
            return [
                'result' => false,
                'msg' => "SECURE ERROR"
            ];
        } 

        if (!$this->checkGeneralCategoryAccess($_POST['id'], $_POST['categoryid'])) {
            return [
                'result' => false,
                'msg' => "ACCESS ERROR"     // 접근이 불가능한 게시판
            ];
        } else {
            // 접속한 페이지의 카테고리 정보
            $gCategory = $this->generalCategoryTable->findById($_POST['categoryid']);

            /* 모든 게시글 가져오기 */ 
            if ($_POST['option'] == 'default') {

                // 접속한 페이지의 카테고리의 전체 글 목록
                $generalList = $this->generalTable->find('categoryid', $_POST['categoryid']);

                // 접속한 페이지의 카테고리 글 목록에 작성자 정보 추가
                $this->addUsersInfo($generalList);

                // 배열을 뒤집어서 최신 글이 위에 있도록 설정한다.
                $generalList = array_reverse($generalList);
            } 
            /* 검색 결과에 해당하는 게시글 가져오기 */
            else if ($_POST['option'] == 'search') {

                // 검색 단어를 포함한 글 목록
                $generalList = $this->generalTable->searchAndOption([
                    'categoryid' => $_POST['categoryid']
                ], 'text', trim($_POST['search']));

                // 작성자 정보 추가
                $this->addUsersInfo($generalList);

                // 배열을 뒤집어서 최신 글이 위에 있도록 설정한다.
                $generalList = array_reverse($generalList);
            }

            // 게시글 데이터를 JSON 포맷으로 변환한다.
            foreach ($generalList as $index => $general) {
                $generalList[$index] = $this->generalJsonFormat($general);
            }

            return [
                'result' => true,
                'category' => $this->categoryJsonFormat($gCategory), 
                'general' => $generalList
            ];
        }
    }

    private function generalJsonFormat(array $general): array {
        $result = [];

        $result['id'] = (int)$general['id'];
        $result['userid'] = $general['userid'];
        $result['user_accountimg'] = (int)$general['accountimg'];
        $result['user_nickname'] = $general['nickname'];
        $result['categoryid'] = (int)$general['categoryid'];
        $result['category'] = $general['category'] ?? ''; 
        $result['text'] = $general['text'];
        $result['img'] = (int)$general['img'];
        $result['date'] = $general['date'];
        $result['likes'] = (int)$general['likes'];
        $result['comments'] = (int)$general['comments'];
        $result['groupid'] = (int)$general['groupid'];

        return $result;
    }

    /**
     *  DatabaseTable 클래스의 find 함수로 구한 값에 작성자 정보를 추가한다
     *  참조 전달을 사용해서 값을 변경한다.
     */
    private function addUsersInfo(array &$findResult) {
        foreach ($findResult as $key => $value) {
            $writer = $this->userTable->findById($value['userid']);
            if ($writer) {
                $findResult[$key]['accountimg'] = $writer['accountimgid'];
                $findResult[$key]['nickname'] = $writer['nickname'];
            } else {    // 작성자가 회원탈퇴를 한 경우
                $findResult[$key]['accountimg'] = -1;
                $findResult[$key]['nickname'] = '(알 수 없음)';
            }
        }
    }


    /**
     *  게시판 삭제하기
     */
    public function removeCategory() {

        /* 보안 코드 */
        if (!$this->secure($_POST['secure'])) {
            return [
                'result' => false,
                'msg' => "SECURE ERROR"
            ];
        } 

        $this->deleteGeneralCategory($_POST['categoryid']);
        return [ 'result' => true ];
    }


    /*================================================================================================*/ 

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
     *  대댓글 삭제
     */
    private function deleteChildComment($id) {
        $this->generalCommentsLikesTable->deleteColumn('generalcommentsid', $id);
        $this->generalCommentsDislikesTable->deleteColumn('generalcommentsid', $id);
        $this->generalCommentsExpelTable->deleteColumn('generalcommentsid', $id);

        // 대댓글 삭제
        $this->generalCommentsTable->delete($id); 
    }

    /*================================================================================================*/ 


    /**
     *  게시글 작성
     */
    public function editGeneral() {

        $MAX_IMAGES = 5;

        /* 보안 코드 */
        if (!$this->secure($_POST['secure'])) {
            return [
                'result' => false,
                'msg' => "SECURE ERROR"
            ];
        } 

        if (!$this->checkGeneralCategoryAccess($_POST['id'], $_POST['categoryid'])) {
            return [
                'result' => false,
                'msg' => "ACCESS ERROR"     // 접근이 불가능한 게시판
            ];
        }

        $imageArray = [
            'path' => [],
            'name' => [],
            'width' => [],
            'height' => []
        ];

        for ($i = 0; $i < $MAX_IMAGES; $i++) {

            if (isset($_POST['image_name_' . $i]) 
                && isset($_POST['image_path_' . $i]) 
                && isset($_POST['image_width_' . $i])
                && isset($_POST['image_height_' . $i])) {
                
                $imageArray['path'][] = $_POST['image_path_' . $i]; // data
                $imageArray['name'][] = $_POST['image_name_' . $i];
                $imageArray['width'][] = $_POST['image_width_' . $i];
                $imageArray['height'][] = $_POST['image_height_' . $i];
            } else {
                break;
            }
        }

        $user = $this->userTable->findById($_POST['id']);

        // 새 게시판의 임시 id 생성
        $tmpId = $user['id'] . '_' . ($user['generalcount'] + 1);

        $this->generalTable->insert([    
            'tmpid' => $tmpId, 
            'userid' => $user['id'], 
            'categoryid' => $_POST['categoryid'],    
            'text' => $_POST['text'],    
            'img' => 0,   
            'date' => new DateTime(),    
            'likes' => 0,
            'comments' => 0,
            'groupid' => 0
        ]);     

        // 파일을 업로드한 경우에만 실행한다. 
        if (!empty($imageArray['path'][0])) {
            // 새 게시판의 임시 id를 이용해서 id 값을 구한다.
            $findGeneralResult = $this->generalTable->find('tmpid', $tmpId); 
            if ($findGeneralResult) {
                // 업로드한 이미지 파일을 서버에 저장하고 실행 결과를 반환한다.
                $uploaded = $this->saveUploadImageFiles($imageArray, 'general', $findGeneralResult[0]['id'], 500);

                // 이미지 파일 개수를 설정한다.
                $this->generalTable->update([
                    'primaryKey' => $findGeneralResult[0]['id'],
                    'img' => count($uploaded['path'])
                ]);

                // 이미지 파일 경로와 넓이 정보를 테이블에 저장한다.
                foreach ($uploaded['path'] as $key => $path) {
                    $this->generalImgTable->insert([
                        'generalid' => $findGeneralResult[0]['id'],
                        'path' => $path,
                        'width' => $uploaded['width'][$key]
                    ]);
                }
            }
        }

        // userTable 의 'generalcount' 칼럼의 값 1 증가
        $this->IncreaseOne($this->userTable, $user['id'], 'generalcount');

        return [ 'result' => true ];
    }

    /*================================================================================================*/ 

    /**
     *  업로드한 이미지 파일을 서버에 저장하는 기능을 수행한다. 
     *
     *  매개변수 
     *              - $imageArray   : [ 
     *                                  'path' => [],
     *                                  'name' => [],
     *                                  'width' => [],
     *                                  'height' => []
     *                                ]
     *              - $fileDir      : /var/www/html/file/images/ 아래에 존재하는 폴더명을 전달한다.
     *              - $fileId       : 데이터베이스의 기본 키를 전달한다.
     *
     *              - $changePxWidth    : (가로) 픽셀 크기 축소 ([0]: 픽셀 크기 변경 없음)
     *
     *  반환값
     *              - uploaded      : [
     *                                  'path' => [],
     *                                  'width' => []
     *                                ]
     */
    private function saveUploadImageFiles(array $imageArray, $fileDir, $fileId, int $changePxWidth=0): array {

        $uploaded = [];
        $uploaded['path'] = [];
        $uploaded['width'] = [];

        $today = new DateTime();
        $todayDir = $today->format('Y-m-d');

        // 파일을 저장할 (서버) 위치
        $imageUploadPath_1 = __DIR__ . '/' . '../../file/images/' . $fileDir . '/';
        $imageUploadPath_2 = $todayDir . '/' . $fileId . '_';

        $COUNT = count($imageArray['path']);
        for ($i = 0; $i < $COUNT; $i++) {

            $data = base64_decode($imageArray['path'][$i]);

            // 픽셀 크기를 축소하는 경우
            if ($changePxWidth != 0 && ($imageArray['width'][$i] > $changePxWidth)) {

                $image = imagecreatefromstring($data);

                $resizedImage = resize_image_to_width($changePxWidth, $image, 
                    $imageArray['width'][$i], $imageArray['height'][$i]);

                if ($resizedImage) {
                    ob_start();
                    imagejpeg($resizedImage);
                    $data = ob_get_clean();

                    $imageArray['width'][$i] = $changePxWidth;
                }
            }

            if (file_put_contents($imageUploadPath_1 . $imageUploadPath_2 . $imageArray['name'][$i], $data)) {

                $uploaded['path'][] = $imageUploadPath_2 . $imageArray['name'][$i];
                $uploaded['width'][] = $imageArray['width'][$i];
            }
        }

        return $uploaded;
    }

    /*================================================================================================*/ 

    /**
     *  게시판 글 관리
     */
    public function myGeneralArticle() {

        /* 보안 코드 */
        if (!$this->secure($_POST['secure'])) {
            return [
                'result' => false,
                'msg' => "SECURE ERROR"
            ];
        }

        /* 내가 쓴 글 */
        if ($_POST['option'] == 'general') {

            $contentList = $this->generalTable->find('userid', $_POST['id']);

            // 배열을 뒤집어서 최신 글이 위에 있도록 설정한다.
            $contentList = array_reverse($contentList);
        } 
        /* 댓글 단 글 */
        else if ($_POST['option'] == 'comment') {

            // 쓴 댓글 목록
            $commentList = $this->generalCommentsTable->find('userid', $_POST['id']);

            // 배열을 뒤집어서 최신 글이 위에 있도록 설정한다.
            $commentList = array_reverse($commentList);
            
            $generalIdList = [];
            foreach ($commentList as $index => $content) {
                $generalIdList[] = $content['generalid'];
            }

            // 중복되는 값을 모두 제거한다.
            $generalIdList = array_unique($generalIdList);
            changeToSequentialArray($generalIdList);

            $contentList = [];
            foreach ($generalIdList as $id) {
                $contentList[] = $this->generalTable->findById($id);
            }
        } else {
            return [
                'result' => false,
                'msg' => "OPTION ERROR"
            ];
        }

        // 카테고리 정보 가져오기
        $this->getGeneralCategoryName($contentList);

        // 작성자 정보 추가
        $this->addUsersInfo($contentList);

        // 게시글 데이터를 JSON 포맷으로 변환한다.
        foreach ($contentList as $index => $content) {
            $contentList[$index] = $this->generalJsonFormat($content);
        }

        return [
            'result' => true,
            'general' => $contentList
        ];
    }

    /**
     *  [General] 배열을 변경한다 : 카테고리명 추가 (category)
     */
    private function getGeneralCategoryName(array &$list) {

        $CategoryDetailInfoList = $this->generalCategoryTable->findAll();
        $generalCategoryNameList = [];

        foreach ($CategoryDetailInfoList as $info) {
            $generalCategoryNameList[$info['id']] = $info['name'];
        }
        foreach ($list as $key => $value) {
            $list[$key]['category'] = $generalCategoryNameList[$value['categoryid']];
        }
    }


    /**
     *  게시판 이미지 가져오기
     */
    public function generalImage() {

        /* 보안 코드 */
        if (!$this->secure($_GET['secure'])) {
            return [
                'result' => false,
                'msg' => "SECURE ERROR"
            ];
        }

        $result = $this->generalImgTable->find('generalid', $_GET['generalid']);
        $pathJO = [];
        foreach ($result as $key => $value) {
            $pathJO['_' . $key] = $value['path'];
        }
        return [
            'result' => true,
            'path' => $pathJO
        ];
    }


    /**
     *  댓글과 대댓글을 가져온다.
     */
    public function generalComment() {

        /* 보안 코드 */
        if (!$this->secure($_POST['secure'])) {
            return [
                'result' => false,
                'msg' => "SECURE ERROR"
            ];
        }

        $gView = $this->generalTable->findById($_POST['generalid']);        // 게시판 글 정보

        // 해당 글이 더 이상 존재하지 않는 경우
        if (!$gView) {
            return [
                'result' => false,
                'msg' => "DELETED ERROR"
            ];
        }

        // 댓글+대댓글 목록
        $gComments = $this->generalCommentsTable->find('generalid', $_POST['generalid']);
        // 작성자 정보를 추가한다
        $this->addUsersInfo($gComments);

        // 댓글+대댓글 데이터를 JSON 포맷으로 변환한다.
        foreach ($gComments as $index => $comment) {
            $gComments[$index] = $this->generalCommentJsonFormat($comment);
        }

        // 댓글과 대댓글을 분리한다.
        $gBaseComments = [];
        foreach ($gComments as $key => $comment) {
            if ($comment['class'] == 0) {
                $gBaseComments[$comment['group']] = $comment;
                $gBaseComments[$comment['group']]['comment'] = [];
            } else {
                // 대댓글을 댓글 'comment' 아래로 정리한다.
                $gBaseComments[$comment['group']]['comment'][] = $comment;
            }
        }

        // 비연속적인 배열을 연속적인 배열로 변환환다.
        changeToSequentialArray($gBaseComments);

        // id(auto_increment)순을 뒤집어서 최신순으로 정렬한다.
        $gBaseComments = array_reverse($gBaseComments);
        foreach ($gBaseComments as $key => $value) {
            $gBaseComments[$key]['comment'] = array_reverse($value['comment']);
        }

        return [
            'result' => true,
            'base_comment' => $gBaseComments
        ];
    }

    private function generalCommentJsonFormat(array $comment): array {
        $result = [];

        $result['id'] = (int)$comment['id'];
        $result['userid'] = $comment['userid'];
        $result['user_accountimg'] = (int)$comment['accountimg'];
        $result['user_nickname'] = $comment['nickname'];
        $result['text'] = $comment['text'];
        $result['date'] = $comment['date'];
        $result['likes'] = (int)$comment['likes'];
        $result['class'] = (int)$comment['class'];
        $result['group'] = (int)$comment['group'];
        $result['categoryid'] = (int)$comment['categoryid'];
        $result['generalid'] = (int)$comment['generalid'];

        return $result;
    }


    /**
     *  게시판 글, 댓글, 대댓글 처리 관련
     */
    public function generalView() {

        /* 보안 코드 */
        if (!$this->secure($_POST['secure'])) {
            return [
                'result' => false,
                'msg' => "SECURE ERROR"
            ];
        }

        /* 게시글 정보 새로고침 */
        if ($_POST['option'] == 'refresh') {

            $gView = $this->generalTable->findById($_POST['generalid']);
            if (!$gView) {  // 해당 글이 더 이상 존재하지 않는 경우
                return [
                    'result' => false,
                    'msg' => "DELETED ERROR"
                ];
            } else {        // 좋아요 수, 댓글 수
                return [
                    'result' => true,
                    'likes' => (int)$gView['likes'],
                    'comments' => (int)$gView['comments']
                ];
            }
        }

        switch($_POST['option']) {
            /* 해당 글에 '공감'을 클릭한 경우 */
            case 'likes':
                $this->generalClickLikes($_POST['generalid'], $_POST['id']);
                break;
            /* 해당 글에 '비추'를 클릭한 경우 */
            case 'dislikes':
                $category = $this->generalCategoryTable->findById($_POST['categoryid']);
                if ($category)
                    $this->generalClickDislikes($category, $_POST['generalid'], $_POST['id']);
                break;
            /* 해당 글에 '신고'를 클릭한 경우 */
            case 'stranger':
                $this->generalClickStranger($_POST['stranger_userid'], $_POST['generalid'], $_POST['id']);
                break;
            /* 해당 글을 삭제하는 경우 */
            case 'delete':
                $this->deleteGeneral($_POST['generalid']);
            break;

            /* 해당 댓글에 '공감'을 클릭한 경우 */
            case 'comment_likes':
                // 작성자가 회원탈퇴를 한 경우 반응을 하지 않는다.
                if ($this->getCommentWriter($_POST['commentid'])) {
                    // 해당 댓글에 좋아요를 한 이력을 확인한다.
                    $isLikesCommitted = false;
                    $likesList = $this->generalCommentsLikesTable->find('generalcommentsid', $_POST['commentid']);
                    foreach ($likesList as $likes) {
                        if ($likes['userid'] == $_POST['id']) {
                            $isLikesCommitted = true;
                            break;
                        }
                    }
                    // 좋아요는 한 번만 클릭할 수 있도록 제한한다.
                    if (!$isLikesCommitted) {
                        // generalcommentslikes 룩업 테이블에 추가한다.
                        $this->generalCommentsLikesTable->insert([
                            'userid' => $_POST['id'],
                            'generalcommentsid' => $_POST['commentid']
                        ]);

                        // 댓글의 likes 칼럼의 값 1 증가
                        $this->IncreaseOne($this->generalCommentsTable, $_POST['commentid'], 'likes');
                    }
                }
                break;
            /* 해당 댓글에 '비추'를 클릭한 경우 */
            case 'comment_dislikes':
                /**
                 *  게시판이 방출기능을 허용한 경우에만 동작한다.
                 *  다수가 싫어요를 누른 경우에 해당 글은 삭제된다.
                 */
                $category = $this->generalCategoryTable->findById($_POST['categoryid']);
                if ($category) {
                    if ($category['expel'] == 'Y') {

                        // 해당 댓글에 싫어요를 한 이력을 확인한다.
                        $isDislikesCommitted = false;
                        $dislikesList = $this->generalCommentsDislikesTable
                                             ->find('generalcommentsid', $_POST['commentid']);
                        foreach ($dislikesList as $dislikes) {
                            if ($dislikes['userid'] == $_POST['id']) {
                                $isDislikesCommitted = true;
                                break;
                            }
                        }

                        // 싫어요는 한 번만 클릭할 수 있도록 제한한다.
                        if (!$isDislikesCommitted) {

                            // generalcommentsdislikes 룩업 테이블에 추가한다.
                            $this->generalCommentsDislikesTable->insert([
                                'userid' => $_POST['id'],
                                'generalcommentsid' => $_POST['commentid']
                            ]);

                            // 댓글의 likes 칼럼의 값 1 감소
                            $this->DecreaseOne($this->generalCommentsTable, $_POST['commentid'], 'likes');

                            $commentInfo = $this->generalCommentsTable->findById($_POST['commentid']);
                            if ($commentInfo['likes'] < -(int)($category['users'] / 2)) {

                                // 해당 글 작성자 방출 (회원탈퇴한 사용자가 아닌 경우) 
                                $writer = $this->getCommentWriter($_POST['commentid']);
                                if ($writer) 
                                    $this->categoryExpel($writer);

                                if ($commentInfo['class'] == 0) // 해당 댓글 삭제
                                    $this->deleteBaseComment($_POST['commentid']);
                                else    // 해당 대댓글 삭제
                                    $this->deleteChildComment($_POST['commentid']);
                            }
                        }
                    }
                }
                break;
            /* 해당 댓글에 '신고'를 클릭한 경우 */
            case 'comment_stranger':
                // 작성자가 회원탈퇴를 한 경우 반응을 하지 않는다.
                if ($this->userTable->findById($_POST['stranger_userid'])) {
                    // 해당 댓글에 방출을 누른 이력을 확인한다.
                    $isExpelCommitted = false;
                    $expelList = $this->generalCommentsExpelTable->find('generalcommentsid', $_POST['commentid']);
                    foreach ($expelList as $expel) {
                        if ($expel['userid'] == $_POST['id']) {
                            $isExpelCommitted = true;
                            break;
                        }
                    }
                    // 방출 버튼을 한 번만 클릭할 수 있도록 제한한다.
                    if (!$isExpelCommitted) {
                        // generalcommentsexpel 룩업 테이블에 추가한다.
                        $this->generalCommentsExpelTable->insert([
                            'userid' => $_POST['id'],
                            'generalcommentsid' => $_POST['commentid']
                        ]);

                        // ssexpel 기능을 수행한다.
                        $this->ssexpelUser($_POST['stranger_userid']);
                    }
                }
                break;
            /* 해당 댓글을 삭제하는 경우 */
            case 'comment_delete':
                $commentInfo = $this->generalCommentsTable->findById($_POST['commentid']);
                if ($commentInfo) {
                    if ($commentInfo['class'] == 0) // 댓글 삭제
                        $this->deleteBaseComment($_POST['commentid']);
                    else    // 대댓글 삭제
                        $this->deleteChildComment($_POST['commentid']);
                }
            break;


            /* 댓글 등록 */
            case 'write_comment':
                $general = $this->generalTable->findById($_POST['generalid']);
                if ($general) {     // 해당 글이 존재하는 경우

                    $this->generalCommentsTable->insert([
                        'userid' => $_POST['id'],
                        'text' => $_POST['text'],
                        'date' => new DateTime(),
                        'likes' => 0,
                        'class' => 0,                       // 댓글 0, 대댓글 1
                        'group' => $general['groupid'],     // 0부터 시작
                        'categoryid' => $_POST['categoryid'],
                        'generalid' => $_POST['generalid']
                    ]);

                    // comments 값을 1 증가시킨다.  
                    $this->IncreaseOne($this->generalTable, $_POST['generalid'], 'comments');
                    // groupid 값을 1 증가시킨다.
                    $this->IncreaseOne($this->generalTable, $_POST['generalid'], 'groupid');
                }
                break;
                /* 대댓글 등록 */
                case 'write_ccomment':
                    $baseComment = $this->generalCommentsTable->findById($_POST['commentid']);
                    if ($baseComment) {    // 해당 댓글이 존재하는 경우
                        $this->generalCommentsTable->insert([
                            'userid' => $_POST['id'],
                            'text' => $_POST['text'],
                            'date' => new DateTime(),
                            'likes' => 0,
                            'class' => 1,                       // 댓글 0, 대댓글 1
                            'group' => $baseComment['group'],
                            'categoryid' => $_POST['categoryid'],
                            'generalid' => $_POST['generalid']
                        ]);
                    }
                    break;
        }

        return [ 'result' => true ];
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
     *  중고거래 카테고리 목록을 가져온다.
     */
    public function tradeCategory() {

        /* 보안 코드 */
        if (!$this->secure($_GET['secure'])) {
            return [
                'result' => false,
                'msg' => "SECURE ERROR"
            ];
        }

        $CategoryList = $this->tradeCategoryTable->findAll();
        $CategoryJsonList = [];
        foreach ($CategoryList as $key => $value)
            $CategoryJsonList["_" . $key] = $value['category'];
        return [
            'result' => true,
            'category' => $CategoryJsonList
        ];
    }

    /**
     *  전체 중고거래 목록을 가져온다.
     */
    public function trade() {

        /* 보안 코드 */
        if (!$this->secure($_POST['secure'])) {
            return [
                'result' => false,
                'msg' => "SECURE ERROR"
            ];
        }

        /* 중고거래 글 목록 */
        $tradeContentList = $this->tradeTable->getTradeList($_POST['campus'], $_POST['category'], $_POST['search']); 
        foreach ($tradeContentList as $key => $content) {
            $tradeContentList[$key] = $this->tradeJsonFormat($content);
        }

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
        $tradeImgList = $this->tradeTable->getTradeImgList($_POST['campus'], $_POST['category'], $_POST['search']);
        $tradeImgTempList = [];
        foreach ($tradeContentList as $content) {
            $tradeImgTempList[$content['id']] = [];
        }
        foreach ($tradeImgList as $content) {
            $tradeImgTempList[$content['id']][] = $content['path'];
        }
        foreach ($tradeContentList as $key => $content) {
            $tradeContentList[$key]['img_path'] = $tradeImgTempList[$content['id']];
        }

        return [
            'result' => true,
            'trade' => $tradeContentList
        ];
    }

    /**
     *  사용자가 작성한 중고거래 목록을 가져온다.
     */
    public function myTradeArticle() {

        /* 보안 코드 */
        if (!$this->secure($_POST['secure'])) {
            return [
                'result' => false,
                'msg' => "SECURE ERROR"
            ];
        }

        $userid = $_POST['id'];

        /* 중고거래 글 목록 */
        $tradeContentList = $this->tradeTable->find('userid', $userid); 
        foreach ($tradeContentList as $key => $content) {
            $tradeContentList[$key] = $this->tradeJsonFormat($content);
        }

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
        $tradeImgList = $this->tradeTable->getTradeImgList($_POST['campus'], $_POST['category'], $_POST['search']);
        $tradeImgTempList = [];
        foreach ($tradeContentList as $content) {
            $tradeImgTempList[$content['id']] = [];
        }
        foreach ($tradeImgList as $content) {
            $tradeImgTempList[$content['id']][] = $content['path'];
        }
        foreach ($tradeContentList as $key => $content) {
            $tradeContentList[$key]['img_path'] = $tradeImgTempList[$content['id']];
        }

        // 배열을 뒤집어서 최신 글이 위에 있도록 설정한다.
        $tradeContentList = array_reverse($tradeContentList);

        return [
            'result' => true,
            'trade' => $tradeContentList
        ];
    }

    /**
     *  개별 중고거래 목록을 가져온다.
     */
    public function viewTrade() {

        /* 보안 코드 */
        if (!$this->secure($_POST['secure'])) {
            return [
                'result' => false,
                'msg' => "SECURE ERROR"
            ];
        }

        $tradeContentItem = $this->tradeTable->findById($_POST['tradeid']);
        if (!$tradeContentItem) {
            return [
                'result' => false,
                'msg' => "NO TRADE ERROR"
            ];
        }
        $tradeContentItem = $this->tradeJsonFormat($tradeContentItem);

        // 카테고리 정보 가져오기
        $CategoryDetailInfoList = $this->tradeCategoryTable->findAll();
        $tradeCategoryNameList = [];
        foreach ($CategoryDetailInfoList as $info) {
            $tradeCategoryNameList[$info['id']] = $info['category'];
        }
        $tradeContentItem['category'] = $tradeCategoryNameList[$tradeContentItem['categoryid']];

        /* 중고거래 글에 대응하는 이미지 목록 */
        $tradeContentItem['img_path'] = [];
        $tradeImgList = $this->tradeImgTable->findAndOptionDistinct([ 
            'id' => $tradeContentItem['imgid'],
            'userid' => $tradeContentItem['userid']
        ], [ 'path' ]);
        foreach ($tradeImgList as $tradeImg) {
            $tradeContentItem['img_path'][] = $tradeImg['path'];
        }

        return [
            'result' => true,
            'trade' => $tradeContentItem
        ];
    }

    private function tradeJsonFormat(array $trade): array {
        $result = [];

        $result['id'] = (int)$trade['id'];
        $result['userid'] = $trade['userid'];
        $result['categoryid'] = (int)$trade['categoryid'];
        $result['title'] = $trade['title'];
        $result['price'] = (int)$trade['price'];
        $result['info'] = $trade['info'];
        $result['imgid'] = (int)$trade['imgid'];
        $result['campus'] = $trade['campus'];
        $result['date'] = $trade['date'];
        $result['expel'] = (int)$trade['expel'];

        return $result;
    }

    /**
     *  중고거래 글 작성
     */
    public function editTrade() {

        $MAX_IMAGES = 3;

        /* 보안 코드 */
        if (!$this->secure($_POST['secure'])) {
            return [
                'result' => false,
                'msg' => "SECURE ERROR"
            ];
        } 

        $imageArray = [
            'path' => [],
            'name' => [],
            'width' => [],
            'height' => []
        ];

        for ($i = 0; $i < $MAX_IMAGES; $i++) {

            if (isset($_POST['image_name_' . $i]) 
                && isset($_POST['image_path_' . $i]) 
                && isset($_POST['image_width_' . $i])
                && isset($_POST['image_height_' . $i])) {
                
                $imageArray['path'][] = $_POST['image_path_' . $i]; // data
                $imageArray['name'][] = $_POST['image_name_' . $i];
                $imageArray['width'][] = $_POST['image_width_' . $i];
                $imageArray['height'][] = $_POST['image_height_' . $i];
            } else {
                break;
            }
        }

        $userid = $_POST['id'];

        // 이미지 구분 아이디
        $findResult = $this->tradeImgTable->findAndOptionDistinct([
            'userid' => $userid,
            'no' => 0
        ], [ 'id' ]);
        if ($findResult) {
            $imgId = $findResult[count($findResult)-1]['id'] + 1;
        } else {
            $imgId = 0;
        }

        // 업로드한 이미지 파일을 서버에 저장하고 실행 결과를 반환한다.
        $uploaded = $this->saveUploadImageFiles($imageArray, 'trade', ($userid . '_' . $imgId), 500);

        // 이미지 파일 경로와 넓이 정보를 테이블에 저장한다.
        foreach ($uploaded['path'] as $key => $path) {
            $this->tradeImgTable->insert([
                'userid' => $userid,
                'id' => $imgId,
                'no' => $key,
                'path' => $path,
                'width' => $uploaded['width'][$key]
            ]);
        }

        // 적어도 하나의 파일이 존재하는 경우에만 글을 등록한다.
        if ($this->tradeImgTable->findAndOptionDistinct([
            'userid' => $userid,
            'id' => $imgId
        ], [ 'no' ])) {
            $this->tradeTable->insert([
                'userid' => $userid,
                'categoryid' => $_POST['trade_category'],
                'title' => $_POST['trade_title'],
                'price' => $_POST['trade_price'],
                'info' => $_POST['trade_info'],
                'imgid' => $imgId,
                'campus' => $_POST['trade_campus'],
                'date' => new DateTime(),
                'expel' => 0
            ]);
        } else {
            return [
                'result' => false,
                'msg' => "IMAGE NOT FOUND ERROR"
            ];
        }

        $saveTrade = $this->tradeTable->findAndOptionDistinct([
            'userid' => $userid,
            'imgid' => $imgId
        ], [ 'id', 'date' ]);
        if (!$saveTrade) {
            return [
                'result' => false,
                'msg' => "TRADE ID NOT FOUND ERROR"
            ];
        }
        return [ 
            'result' => true,
            'id' => (int)$saveTrade[0]['id'],
            'first_img_path' => $uploaded['path'][0],
            'date' => $saveTrade[0]['date']
        ];
    }

    /**
     *  판매내역 변경
     */
    public function myTradeArticleAction() {

        /* 보안 코드 */
        if (!$this->secure($_POST['secure'])) {
            return [
                'result' => false,
                'msg' => "SECURE ERROR"
            ];
        }

        /* 거래완료 (글 삭제) */
        if ($_POST['action'] == 'delete') {
            $this->deleteTrade($_POST['tradeid']);
        } 
        /* 끌어올리기 */
        else if ($_POST['action'] == 'pull_up') {
            $prevTrade = $this->tradeTable->findById($_POST['tradeid']);
            if ($prevTrade) {
                // 기존 데이터 삭제
                $this->tradeTable->delete($_POST['tradeid']);

                $newDateTime = new DateTime();

                // 새로운 데이터 생성
                $this->tradeTable->insert([
                    'userid' => $prevTrade['userid'],
                    'categoryid' => $prevTrade['categoryid'],
                    'title' => $prevTrade['title'],
                    'price' => $prevTrade['price'],
                    'info' => $prevTrade['price'],
                    'imgid' => $prevTrade['imgid'],
                    'campus' => $prevTrade['campus'],
                    'date' => $newDateTime,
                    'expel' => $prevTrade['expel']
                ]);

                return [
                    'result' => true,
                    'date' => $newDateTime->format('Y-m-d H:i:s')
                ];
            } else {
                return [
                    'result' => false,
                    'msg' => "DATA NOT FOUND ERROR"
                ];
            }
        }
        /* 가격 변경 */
        else if ($_POST['action'] == 'change_price') {
            $this->tradeTable->update([
                'primaryKey' => $_POST['tradeid'],
                'price' => $_POST['price']
            ]);
        }

        return [ 'result' => true ];
    }

    /**
     *  중고거래 글 신고 처리
     */
    public function tradeExpel() {

        /* 보안 코드 */
        if (!$this->secure($_POST['secure'])) {
            return [
                'result' => false,
                'msg' => "SECURE ERROR"
            ];
        }

        $expelTradeInfo = $this->tradeTable->findById($_POST['tradeid']);

        if ($expelTradeInfo) {
            
            $expel = $expelTradeInfo['expel'] + 1;

            if ($this->tradeExpelTable->countAndOption([
                'userid' => $_POST['id'],
                'tradeid' => $expelTradeInfo['id']
            ]) == 0) {  // 신고는 한 번만 가능하다.
            
                /* 해당 글을 삭제하는 경우 */
                if ($expel >= TRADE_EXPEL_MAX)
                    $this->deleteTrade($expelTradeInfo['id']);
                else {
                    $this->tradeExpelTable->insert([
                        'userid' => $_POST['id'],
                        'tradeid' => $expelTradeInfo['id']
                    ]);

                    $this->tradeTable->update([
                        'primaryKey' => $expelTradeInfo['id'],
                        'expel' => $expel
                    ]);
                } 

                return [ 
                    'result' => true,
                    'proceed' => true
                ];
            } else {        // 이미 신고를 한 글에 해당하는 경우
                return [ 
                    'result' => true,
                    'proceed' => false
                ];
            }
        }

        return [
            'result' => false,
            'msg' => "DATA NOT FOUND ERROR"
        ];
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
     *  모든 시간표 가져오기
     */
    public function allSchedule() {

        $schedules = $this->scheduleTable->findAll();
        foreach ($schedules as $index => $schedule)
            $schedules[$index] = $this->scheduleJsonFormat($schedule);

        return [
            'result' => true,
            'schedules' => $schedules
        ];
    }

    /**
     *  사용자 시간표 정보 가져오기
     */
    public function mySchedule() {

        /* 보안 코드 */
        if (!$this->secure($_POST['secure'])) {
            return [
                'result' => false,
                'msg' => "SECURE ERROR"
            ];
        }
         
        return [
            'result' => true,
            'user_schedule' => $this->getJsonFormattedUserSchedule($_POST['id']),
            'schedule' => $this->getJsonFormattedSchedule($_POST['id'])
        ];
    }

    /* 사용자가 직접 추가한 시간표 정보 가져오기 */
    private function getJsonFormattedUserSchedule($userid): array {

        $userScheduleList = $this->userScheduleTable->find('userid', $userid);
        foreach ($userScheduleList as $key => $value) 
            $userScheduleList[$key] = $this->userScheduleJsonFormat($value);

        return $userScheduleList;
    }

    /* 사용자가 추가한 시간표 정보 가져오기 */
    private function getJsonFormattedSchedule($userid): array {

        $scheduleLookupList = $this->scheduleLookupTable->find('userid', $userid);
        $scheduleList = [];
        if ($scheduleLookupList) {
            foreach ($scheduleLookupList as $key => $value) {
                $scheduleList[] = $this->scheduleJsonFormat(
                    $this->scheduleTable->findById($value['scheduleno']));
            }
        }

        return $scheduleList;
    }

    private function userScheduleJsonFormat(array $schedule): array {

        $result = [];

        $result['className'] = $schedule['className'];
        $result['classTime'] = $schedule['classTime'];
        $result['classInfo'] = $schedule['classInfo'] ?? "";

        return $result;
    }

    private function scheduleJsonFormat(array $schedule): array {

        $result = [];

        $result['no'] = (int)$schedule['no'];
        $result['subjects'] = $schedule['subjects'] ?? "";
        $result['department'] = $schedule['department'] ?? "";
        $result['classNumber'] = $schedule['classNumber'] ?? "";
        $result['className'] = $schedule['className'];
        $result['bunban'] = (int)$schedule['bunban'];
        $result['isugubun'] = $schedule['isugubun'] ?? "";
        $result['classTime'] = $schedule['classTime'];
        $result['campus'] = $schedule['campus'];
        $result['roomAndProf'] = $schedule['roomAndProf'] ?? "";

        return $result;
    }

    /**
     *  사용자 시간표 삭제하기
     */
    public function deleteSchedule() {

        /* 보안 코드 */
        if (!$this->secure($_POST['secure'])) {
            return [
                'result' => false,
                'msg' => "SECURE ERROR"
            ];
        }

        $userid = $_POST['id'];

        if ($_POST['option'] == 'user') {
            $this->userScheduleTable->deleteLookup($userid, 'classTime', $_POST['key']);
            return [ 'result' => true ];
        } else if ($_POST['option'] == 'college') {
            $this->scheduleLookupTable->deleteLookup($userid, 'scheduleno', $_POST['key']);
            return [ 'result' => true ];
        }

        return [
            'result' => false,
            'msg' => "OPTION ERROR"
        ];
    }
         
    /**
     *  사용자 시간표 추가하기
     */
    public function addSchedule() {

        define("ID_GENERATOR", 10000);

        /* 보안 코드 */
        if (!$this->secure($_POST['secure'])) {
            return [
                'result' => false,
                'msg' => "SECURE ERROR"
            ];
        }
        
        $userid = $_POST['id'];

        /* 사용자의 시간표 시간 목록 (중복 방지) */
        $userClassTime = [];
        $result = $this->userScheduleTable->find('userid', $userid);
        if ($result) {
            foreach($result as $key => $value) {
                $userClassTime[] = $value['classTime'];
            }
        }
        $result = $this->scheduleLookupTable->find('userid', $userid);
        if ($result) {
            foreach($result as $key => $value) {
                $userClassTime[] = ($this->scheduleTable->findById($value['scheduleno']))['classTime'];
            }
        }

        $isSuccess = false;

        if ($_POST['class_id'] < ID_GENERATOR) {    // 시간표를 '추가'하는 경우
            $schedule = $this->scheduleTable->findById($_POST['class_id']);
            if ($schedule) {
                $savePossibility = checkSavePossibility($schedule['classTime'], $userClassTime);
                if ($savePossibility) {
                    $this->scheduleLookupTable->insert([
                        'userid' => $userid,
                        'scheduleno' => $_POST['class_id']
                    ]);
                    $isSuccess = true;
                }
            }
        } else {  // 시간표를 '직접 추가'하는 경우
            $savePossibility = checkSavePossibility($_POST['class_time'], $userClassTime);
            if ($savePossibility) {
                $classToSave = [
                    'userid' => $userid,
                    'className' => $_POST['class_name'],
                    'classTime' => $_POST['class_time'],
                ];
                if (!empty($_POST['class_info']))
                    $classToSave['classInfo'] = $_POST['class_info'];
                $this->userScheduleTable->insert($classToSave);
                $isSuccess = true;
            }
        }

        return [
            'result' => $isSuccess,
            'user_schedule' => $this->getJsonFormattedUserSchedule($userid),
            'schedule' => $this->getJsonFormattedSchedule($userid)
        ];
    }

    /**
     *  채팅 상대방 가져오기
     */
    public function loadChatOpponentList() {

        /* 보안 코드 */
        if (!$this->secure($_POST['secure'])) {
            return [
                'result' => false,
                'msg' => "SECURE ERROR"
            ];
        }

        $userid = $_POST['id'];
        
        // 기존의 채팅 상대방 배열 생성
        $receiverList = [];
        $chatList = $this->chatTable->findOrOptionDistinct([
            'senderid' => $userid,
            'receiverid' => $userid
        ]);
        // 최근에 채팅한 상대가 먼저 오도록 배열을 역순으로 나열한다.
        $chatList = array_reverse($chatList);
        foreach ($chatList as $connection) {
            if ($connection['senderid'] == $userid)
                $receiverList[] = $connection['receiverid'];
            else
                $receiverList[] = $connection['senderid'];
        }
        // 채팅 상대방을 지정한 경우 해당 아이디를 가장 앞에 위치시킨다.
        if (!empty($_POST['opponent_id']))
            array_unshift($receiverList , $_POST['opponent_id']); 
        // 중복되는 채팅 상대방을 배열에서 제거한다.
        $receiverList = array_unique($receiverList);
        // 차단한 상대방을 배열에서 제거한다.
        $blockList = [];
        $chatBlockList = $this->chatBlockTable->find('userid', $userid); 
        foreach ($chatBlockList as $key => $value)
            $blockList[] = $value['blockid'];
        $receiverList = array_diff($receiverList, $blockList);

        // 새로 도착한 메시지가 있는지 확인한다.
        foreach ($receiverList as $index => $receiver) {
            if ($this->chatTable->findAndOptionDistinct([
                'senderid' => $receiver,
                'receiverid' => $userid,
                'readstatus' => 'N'
            ]) ) {
                $receiverList[$index] = [
                    'id' => $receiver,
                    'hasNewMessage' => true
                ];
            } else {
                $receiverList[$index] = [
                    'id' => $receiver,
                    'hasNewMessage' => false
                ];
            }
        }
        // 비연속적인 배열을 연속적인 배열로 변환환다.
        changeToSequentialArray($receiverList);

        // 채팅 상대방을 지정한 경우, 현재 채팅 상대방이 차단한 상대방인지 확인한다.
        $isBlock = false;
        if (!empty($_POST['opponent_id']))
            $isBlock = in_array($_POST['opponent_id'], $blockList);

        return [ 
            'result' => true,
            'block' => $isBlock,
            'opponents' => $receiverList
        ];
    }

    /**
     *  채팅 대화 목록 가져오기
     */
    public function loadChatDataList() {

        /* 보안 코드 */
        if (!$this->secure($_POST['secure'])) {
            return [
                'result' => false,
                'msg' => "SECURE ERROR"
            ];
        }
        
        // 채팅 데이터 목록
        $chatDataList = $this->chatTable->getChatList($_POST['id'], $_POST['opponent_id']);
        foreach ($chatDataList as $key => $value)
            $chatDataList[$key] = $this->chatDataJsonFormat($value);

        return [ 
            'result' => true,
            'chat' => $chatDataList
        ];
    }

    private function chatDataJsonFormat(array $chatData): array {

        $result = [];

        $result['no'] = (int)$chatData['no'];
        $result['senderid'] = $chatData['senderid'];
        //$result['receiverid'] = $chatData['receiverid'];
        $result['datetime'] = $chatData['datetime'];
        $result['contenttype'] = $chatData['contenttype'];
        $result['readstatus'] = ($chatData['readstatus'] == 'Y') ? true : false;
        $result['content'] = $chatData['text'] ?? $chatData['path'];

        return $result;
    }

    /**
     *  채팅 관련 기타 처리
     */
    public function chat() {

        /* 보안 코드 */
        if (!$this->secure($_POST['secure'])) {
            return [
                'result' => false,
                'msg' => "SECURE ERROR"
            ];
        }

        if ($_POST['option'] == 'updateReadStatus') {

            $this->chatTable->updateChatReadStatus(
                $_POST['opponent_id'], $_POST['id'], $_POST['no']);

            return [ 'result' => true ];

        } else if ($_POST['option'] == 'sendText') {

            // 발신자와 수신자 간의 채팅 구분 번호 (contentNo)
            $contentNo = $this->chatTable->countAndOption([
                'senderid' => $_POST['id'],
                'receiverid' => $_POST['opponent_id']
            ]); 

            $dt = new DateTime();

            $this->chatTextTable->insert([
                'no' => $contentNo,
                'senderid' => $_POST['id'],
                'receiverid' => $_POST['opponent_id'],
                'text' => $_POST['text']
            ]);

            $this->chatTable->insert([
                'senderid' => $_POST['id'],
                'receiverid' => $_POST['opponent_id'],
                'datetime' => $dt,
                'contenttype' => 'T',
                'contentno' => $contentNo,
                'readstatus' => 'N'
            ]);

            $resultChat = $this->chatTable->getResultChat($_POST['id'], $_POST['opponent_id'], $contentNo);
            foreach ($resultChat as $key => $value)
                $resultChat[$key] = $this->chatDataJsonFormat($value);
            return [ 
                'result' => true,
                'chat' => $resultChat
            ];

        } else if ($_POST['option'] == 'sendFile') {

            $imageArray = [
                'path' => [ $_POST['image_path'] ],
                'name' => [ $_POST['image_name'] ],
                'width' => [ $_POST['image_width'] ],
                'height' => [ $_POST['image_height'] ]
            ];

            $uploaded = $this->saveUploadImageFiles($imageArray, 'chat', generate_string(10), 512);
            if (empty($uploaded['path'])) {
                return [
                    'result' => false,
                    'msg' => "IMAGE UPLOAD FAIL ERROR"
                ];
            }

            // 발신자와 수신자 간의 채팅 구분 번호 (contentNo)
            $contentNo = $this->chatTable->countAndOption([
                'senderid' => $_POST['id'],
                'receiverid' => $_POST['opponent_id']
            ]); 

            $dt = new DateTime();

            $this->chatFileTable->insert([
                'no' => $contentNo,
                'senderid' => $_POST['id'],
                'receiverid' => $_POST['opponent_id'],
                'path' => $uploaded['path'][0],
                'width' => $uploaded['width'][0]
            ]);

            $this->chatTable->insert([
                'senderid' => $_POST['id'],
                'receiverid' => $_POST['opponent_id'],
                'datetime' => $dt,
                'contenttype' => 'F',
                'contentno' => $contentNo,
                'readstatus' => 'N'
            ]);

            $resultChat = $this->chatTable->getResultChat($_POST['id'], $_POST['opponent_id'], $contentNo);
            foreach ($resultChat as $key => $value)
                $resultChat[$key] = $this->chatDataJsonFormat($value);
            return [ 
                'result' => true,
                'chat' => $resultChat
            ];

        } else if ($_POST['option'] == 'block') {

            $this->chatBlockTable->insert([
                'userid' => $_POST['id'],
                'blockid' => $_POST['opponent_id']
            ]);

            return [ 'result' => true ];

        } else if ($_POST['option'] == 'unblock') {
            
            $this->chatBlockTable->deleteLookup($_POST['id'], 'blockid', $_POST['opponent_id']);
            return [ 'result' => true ];
        }

        return [ 'result' => false ];
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
}
?>
