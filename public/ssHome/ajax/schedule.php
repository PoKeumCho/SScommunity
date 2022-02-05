<?php
session_start();    // 세션 변수를 사용하여 사용자 아이디를 가져온다.

header('Content-Type: application/json');

try {
    /* 필수 파일 불러오기 */
    include_once __DIR__ . '/' . '../../../private/includes/lib/schedule.php';
    include_once __DIR__ . '/' . '../../../private/includes/db/DatabaseConnection.php';
    include_once __DIR__ . '/' . '../../../private/classes/DatabaseTable.php';

    /* 데이터베이스 테이블 인스턴스 생성 */
    $userTable = new DatabaseTable($pdo, 'user', 'id');
    $scheduleTable = new DatabaseTable($pdo, 'scheduletbl', 'no');
    $scheduleLookupTable = new DatabaseTable($pdo, 'schedulelookuptbl', 'userid');
    $userScheduleTable = new DatabaseTable($pdo, 'userscheduletbl', 'userid');

    $response = [];

    /* AJAX을 사용한 시간표 검색 */
    if ($_GET['mode'] == 'getClass') {
        if ($_GET['opt'] == 0) {    // '교과목명'으로 검색한 시간표 검색 자동 완성 기능
            $result = $scheduleTable->search('className', $_GET['value'], true);
        } else {    // '학수번호'로 검색한 시간표 검색 자동 완성 기능
            $result = $scheduleTable->search('classNumber', trim($_GET['value']), true);
        }

        if ($result) {
            $response = array( 
                'schedule' => array()
            );
            foreach ($result as $key => $value) {
                /**
                 *  '교과목명'으로 검색한 경우
                 *  className : 교과목명 (분반: ) 
                 */
                if ($_GET['opt'] == 0) {
                    $response['schedule'][$key] = array(
                        'no' => $value['no'],
                        'className' => $value['className'] . ' (분반: ' . (int)$value['bunban'] . ')',
                        'classTime' => $value['classTime']
                    );
                } 
                /**
                 *  '학수번호'로 검색한 경우
                 *  className : 학수번호 (분반: ) // 교과목명
                 */
                else {
                    $response['schedule'][$key] = array(
                        'no' => $value['no'],
                        'className' => $value['classNumber'] . ' (분반: ' . (int)$value['bunban'] . ') // ' . $value['className'],
                        'classTime' => $value['classTime']
                    );
                }

                /**
                 *  [공통] 'info'에 campus, roomAndProf 정보를 추가한다. (존재하는 경우에만)
                 */
                $response['schedule'][$key]['info'] = $value['campus']; // campus는 항상 존재한다.
                if (isset($value['roomAndProf'])) {
                    $response['schedule'][$key]['info'] .= '<br/>' . $value['roomAndProf'];
                }
            }
        }
    }
    /* AJAX을 사용한 시간표 저장 */
    else if ($_GET['mode'] == 'saveClass') {

        /* 사용자의 시간표 시간 목록 (중복 방지) */
        $userClassTime = [];
        $result = $userScheduleTable->find('userid', $_SESSION['id']);
        if ($result) {
            foreach($result as $key => $value) {
                $userClassTime[] = $value['classTime'];
            }
        }
        $result = $scheduleLookupTable->find('userid', $_SESSION['id']);
        if ($result) {
            foreach($result as $key => $value) {
                $userClassTime[] = $scheduleTable->findById($value['scheduleno'])['classTime'];
            }
        }

        /* 시간표를 '추가'하는 경우 */
        if ($_GET['opt'] == 0) {
            $schedule = $scheduleTable->findById($_GET['no']);
            if ($schedule) {
                $savePossibility = checkSavePossibility($schedule['classTime'], $userClassTime);

                if ($savePossibility) {
                    $scheduleLookupTable->insert([
                        'userid' => $_SESSION['id'],
                        'scheduleno' => $_GET['no']
                    ]);
                    $response['result'] = true;
                } else {    // 해당 시간에 이미 다른 수업이 존재하는 경우
                    $response['result'] = false;
                    $response['code'] = 1;
                }
            } else {    // 해당 'no'를 기본 키로 갖는 데이터가 존재하지 않는 경우
                $response['result'] = false;
                $response['code'] = 2;
            }
        }
        /* 시간표를 '직접 추가'하는 경우 */
        else if ($_GET['opt'] == 1) {
            // 현재 시간표에 시간이 중복되지 않는지 확인한다.
            $savePossibility = checkSavePossibility($_GET['time'], $userClassTime);
            if ($savePossibility) {
                $classToSave = [
                    'userid' => $_SESSION['id'],
                    'className' => $_GET['name'],
                    'classTime' => $_GET['time'],
                ];
                if (isset($_GET['info'])) {
                    $classToSave['classInfo'] = $_GET['info'];
                } 
                $userScheduleTable->insert($classToSave);
                $response['result'] = true;
            } else {    // 해당 시간에 이미 다른 수업이 존재하는 경우
                $response['result'] = false;
                $response['code'] = 1;
            }
        }
    }
    /* AJAX을 사용한 사용자의 저장된 시간표 불러오기 */
    else if ($_GET['mode'] == 'loadClass') {

        /* 사용자의 시간표 시간 목록 */
        $response['schedule'] = [];
        $result = $userScheduleTable->find('userid', $_SESSION['id']);
        if ($result) {
            foreach($result as $key => $value) {
                $response['schedule'][] = array(
                    'contentHTML' => '<p class="className_bold">' . 
                                        htmlspecialchars($value['className'], ENT_QUOTES, 'UTF-8') . '</p><br/>' .
                                        htmlspecialchars($value['classInfo'], ENT_QUOTES, 'UTF-8'),
                    'classTime' => $value['classTime'],
                    'opt' => 1  // [0] 추가 / [1] 직접 추가
                );
            }
        }
        $result = $scheduleLookupTable->find('userid', $_SESSION['id']);
        if ($result) {
            foreach($result as $key => $value) {
                $schedule = $scheduleTable->findById($value['scheduleno']);
                if ($schedule) {
                    $response['schedule'][] = array(
                        'contentHTML' => '<p class="className_bold">' . $schedule['className'] . '</p><br/>' . $schedule['campus'] . 
                                            (isset($schedule['roomAndProf']) ? '<br/>' . $schedule['roomAndProf'] : ''),
                        'classTime' => $schedule['classTime'],
                        'opt' => 0,
                        'no' => $value['scheduleno']
                    );
                }
            }
        }
    }
    /* AJAX을 사용한 시간표 데이터베이스에서 제거 */
    else if ($_GET['mode'] == 'removeClass') {
        if ($_GET['opt'] == 0) {
            $scheduleLookupTable->deleteLookup($_SESSION['id'], 'scheduleno', $_GET['value']);
        } else if ($_GET['opt'] == 1) {
            $userScheduleTable->deleteLookup($_SESSION['id'], 'classTime', $_GET['value']);
        }
    }

    echo json_encode($response);
}
catch (PDOException $e) {
    $now = new DateTime('NOW');
    $errMsg = $now->format('[Y-n-j g:i:s A] ') 
        . '데이터베이스 오류: ' . $e->getMessage() . ', 위치: ' . $e->getFile() . ' : ' . $e->getLine();  
    error_log ($errMsg, 3, "/var/log/apache2/sscommu/ssHome_schedule_error.log");
}
