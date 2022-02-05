<?php

class DatabaseTable 
{
    // 날짜 형식 처리 함수명
    static $DATE_FUNC_DATE = 'processDate';
    static $DATE_FUNC_DATETIME = 'processDateTime';
    
    private $pdo;
    private $table;
    private $primaryKey;
    private $dateFunc;

    public function __construct(PDO $pdo, $table, $primaryKey, $dateFunc = 'none') {
        $this->pdo = $pdo;
        $this->table = $table;
        $this->primaryKey = $primaryKey;

        // 날짜를 처리하는 경우, $dateFunc에 DatabaseTable::$DATE_FUNC_... 값을 전달한다.
        $this->dateFunc = $dateFunc;
    }

    /**
     *  쿼리 실행
     */
    private function query($sql, array $parameters = []) {
        $query = $this->pdo->prepare($sql);
        $query->execute($parameters);
        return $query;
    }

    /**
     *  다음 auto_increment 값 받아오기
     */
    public function getAutoIncrement() {
        // 해당 테이블에 auto_increment가 존재하지 않으면 false를 반환한다.
        $auto_increment = false;

        $query = 'show table status';

        $query = $this->query($query);

        foreach($query as $row) {   // while ($row = $query->fetch())과 동일
            if ($row['Name'] == $this->table) {
                $auto_increment = $row['Auto_increment'];
                break;
            }
        }

        return $auto_increment;
    }

    /**
     *  테이블 전체 로우 개수 구하기
     */
    public function total() {
        $query = 'SELECT COUNT(*) FROM `' . $this->table . '`';
        
        $query = $this->query($query);
        $row = $query->fetch();
        return $row[0];
    }

    /**
     *  AND 조건을 추가한 로우 개수 구하기
     *  
     *  fields = [ columnName => value, ... ]
     */
    public function countAndOption(array $fields) {
        $query = 'SELECT COUNT(*) FROM `' . $this->table . '` WHERE `';
        foreach ($fields as $key => $value) {
            $query .= $key . '` = :' . $key . ' AND `'; 
        }
        $query = rtrim($query, 'AND `');

        $query = $this->query($query, $fields);
        $row = $query->fetch();
        return $row[0];
    }

    /**
     *  기본 키로 테이블 데이터 가져오기
     */
    public function findById($value) {
        $query = 'SELECT * FROM `' . $this->table . '` 
            WHERE `' . $this->primaryKey . '` = :value';
        
        $parameters = [
            'value' => $value
        ];

        $query = $this->query($query, $parameters);  

        return $query->fetch();
    }

    /**
     *  전달받은 칼럼을 검색하며, 모든 결과 레코드를 반환한다.
     */
    public function find($column, $value) {
        $query = 'SELECT * FROM `' . $this->table . '` 
            WHERE `' . $column . '` = :value'; 

        $parameters = [
            'value' => $value
        ];

        $query = $this->query($query, $parameters);

        return $query->fetchAll();
    }

    /**
     *  OR 조건을 추가한 검색 결과에서 중복된 결과를 제거하여 반환한다.
     *  
     *  fields = [ columnName => value, ... ]
     */
    public function findOrOptionDistinct(array $fields) {
        $query = 'SELECT DISTINCT `';
        foreach ($fields as $key => $value) {
            $query .= $key . '`, `';
        }
        $query = rtrim($query, ', `');

        $query .= '` FROM `' . $this->table . '` WHERE `';
        foreach ($fields as $key => $value) {
            $query .= $key . '` = :' . $key . ' OR `'; 
        }
        $query = rtrim($query, 'OR `');

        $query = $this->query($query, $fields);

        return $query->fetchAll();
    }

    /**
     *  AND 조건을 추가한 검색 결과에서 중복된 결과를 제거하여 반환한다.
     *  
     *  fields = [ columnName => value, ... ]
     *  columns = [ columnName, columnName, ... ]
     */
    public function findAndOptionDistinct(array $fields, array $columns=[]) {
        $query = 'SELECT DISTINCT ';
        if ($columns) {
            foreach ($columns as $column) {
                $query .= '`' . $column . '`, ';
            }
        } else {
            foreach ($fields as $key => $value) {
                $query .= '`' . $key . '`, ';
            }
        }
        $query = rtrim($query, ', ');

        $query .= ' FROM `' . $this->table . '` WHERE `';
        foreach ($fields as $key => $value) {
            $query .= $key . '` = :' . $key . ' AND `'; 
        }
        $query = rtrim($query, 'AND `');

        $query = $this->query($query, $fields);

        return $query->fetchAll();
    }

    /**
     *  문자열 검색 결과에 해당하는 모든 레코드를 반환한다.
     *  $enhance : true 전달 시, 검색할 문자열의 제일 앞에 들어가는 '%'를 제거하여 성능을 향상시킨다.
     */
    public function search($column, $search, $enhance=false) {
        $query = 'SELECT * FROM `' . $this->table . '` 
            WHERE `' . $column . '` LIKE  :search'; 

        if ($enhance) {
            $parameters = [
                'search' => $search . '%'
            ];
        } else {
            $parameters = [
                'search' => '%' . $search . '%'
            ];
        }

        $query = $this->query($query, $parameters);

        return $query->fetchAll();
    }

    /**
     *  AND 조건을 추가한 문자열 검색 결과에 해당하는 모든 레코드를 반환한다.
     *
     *  fields = [ columnName => value, ... ]
     */
    public function searchAndOption(array $fields, $column, $search) {
        $query = 'SELECT * FROM `' . $this->table . '` WHERE `';
        foreach ($fields as $key => $value) {
            $query .= $key . '` = :' . $key . ' AND `'; 
        }
        $query .= $column . '` LIKE  :search';

        $fields['search'] = '%' . $search . '%';

        $query = $this->query($query, $fields);

        return $query->fetchAll();
    }

    /**
     *  테이블 데이터 삽입
     *
     *  fields = [ columnName => value, ... ]
     */
    public function insert(array $fields) {
        $query = 'INSERT INTO `' . $this->table . '` (';

        foreach ($fields as $key => $value) {
            $query .= '`' . $key . '`, ';
        }
        $query = rtrim($query, ', ');
        $query .= ') VALUES (';

        foreach ($fields as $key => $value) {
            $query .= ':' . $key . ', ';
        }
        $query = rtrim($query, ', ');
        $query .= ')';

        $dateFunc = $this->dateFunc;
        if ($dateFunc !== 'none') {
            $fields = $this->$dateFunc($fields);
        }

        $this->query($query, $fields);
    }

    /**
     *  테이블 데이터 수정
     *
     *  fields = [
     *      'primaryKey' => (기본 키 값),
     *
     *      columnName => value, ...
     *  ]
     */
    public function update(array $fields) {
        $query = 'UPDATE `' . $this->table . '` SET ';

        foreach ($fields as $key => $value) {
            if ($key === 'primaryKey') continue;
            $query .= '`' . $key . '` = :' . $key . ', ';
        }
        $query = rtrim($query, ', ');

        $query .= ' WHERE `' . $this->primaryKey . '` = :primaryKey';

        $dateFunc = $this->dateFunc;
        if ($dateFunc !== 'none') {
            $fields = $this->$dateFunc($fields);
        }

        $this->query($query, $fields);
    }

    /**
     *  AND 조건을 추가한 테이블 데이터 수정
     *
     *  options / fields = [
     *      columnName => value, ...
     *  ]
     */
    public function updateAndOption(array $options, array $fields) {
        $query = 'UPDATE `' . $this->table . '` SET ';

        foreach ($fields as $key => $value) {
            $query .= '`' . $key . '` = :' . $key . ', ';
        }
        $query = rtrim($query, ', ');

        $query .= ' WHERE ';
        foreach ($options as $key => $value) {
            $query .= '`' . $key . '` = :' . $key . ' AND ';
        }
        $query = rtrim($query, 'AND ');

        $dateFunc = $this->dateFunc;
        if ($dateFunc !== 'none') {
            $fields = $this->$dateFunc($fields);
        }

        $this->query($query, array_merge($fields, $options));
    }

    /**
     *  테이블 데이터 삭제 (기본 키 사용)
     */
    public function delete($pkValue) {
        $query = 'DELETE FROM `' . $this->table . '` 
            WHERE `' . $this->primaryKey . '` = :value';
        
        $parameters = [ ':value' => $pkValue ];

        $this->query($query, $parameters);
    }

    /**
     *  테이블 데이터 삭제 (칼럼 지정)
     */
    public function deleteColumn($column, $value) {
        $query = 'DELETE FROM `' . $this->table . '` 
            WHERE `' . $column . '` = :value';
        
        $parameters = [ ':value' => $value ];

        $this->query($query, $parameters);
    }

    /**
     *  룩업(lookup) 테이블 데이터 삭제
     *
     *  - $pkValue  : 기본 키 값
     *  - $column   : 기본 키 외의 칼럼
     *  - $colValue : 기본 키 외의 칼럼의 값
     */
    public function deleteLookup($pkValue, $column, $colValue) {
        $query = 'DELETE FROM `' . $this->table . '` 
            WHERE `' . $this->primaryKey . '` = :pkValue AND `' . $column . '` = :colValue';
        
        $parameters = [ 
            ':pkValue' => $pkValue, 
            ':colValue' => $colValue
        ];

        $this->query($query, $parameters);
    }

    /**
     *  테이블의 모든 데이터 가져오기
     */
    public function findAll() {
        $query = 'SELECT * FROM `' . $this->table . '`';
        $result = $this->query($query);
        return $result->fetchAll();
    }

    /**
     *  [chat] 채팅 목록을 가져온다.
     */
    public function getChatList($id, $opponentId, $checkBothSides=true) {
        $query = 'WITH `temptbl`
                    AS
                    (SELECT C.`no`, C.`senderid`, C.`receiverid`, C.`datetime`, C.`contenttype`, 
                                C.`contentno`, C.`readstatus`, T.`text`
                        FROM `chat` C
                        LEFT JOIN `chattext` T
                        ON C.`contentno` = T.`no`
                            AND C.`senderid` = T.`senderid`
                            AND C.`receiverid` = T.`receiverid`)
                    SELECT T.`no`, T.`senderid`, T.`receiverid`, T.`datetime`, T.`contenttype`, 
                            T.`readstatus`, T.`text`, F.`path`, F.`width`
                        FROM `temptbl` T
                        LEFT JOIN `chatfile` F
                        ON T.`contentno` = F.`no`
                            AND T.`senderid` = F.`senderid`
                            AND T.`receiverid` = F.`receiverid`
                        WHERE (T.`senderid` = :opponentId AND T.`receiverid` = :id) ';
    
        if ($checkBothSides) {
            $query .= 'OR (T.`senderid` = :id AND T.`receiverid` = :opponentId)';
        } else {
            $query .= "AND T.`readstatus`='N'";
        }

        $parameters = [ 
            ':id' => $id, 
            ':opponentId' => $opponentId
        ];

        $result = $this->query($query, $parameters);
        return $result->fetchAll();
    }

    /**
     *  [chat] Android 읽은 메시지 처리용
     */
    public function updateChatReadStatus(string $senderid, string $receiverid, int $no) {
        $query = "UPDATE `chat` SET `readstatus` = 'Y' " .
                    'WHERE `senderid` = :senderid AND `receiverid` = :receiverid ' .
                    'AND `no` < :no';

        $parameters = [ 
            ':senderid' => $senderid, 
            ':receiverid' => $receiverid,
            ':no' => $no
        ];

        $this->query($query, $parameters);
    }

    /**
     *  [chat] Android 채팅 목록을 가져온다.
     */
    public function getResultChat($senderid, $receiverid, $contentNo) {
        $query = 'WITH `temptbl`
                    AS
                    (SELECT C.`no`, C.`senderid`, C.`receiverid`, C.`datetime`, C.`contenttype`, 
                                C.`contentno`, C.`readstatus`, T.`text`
                        FROM `chat` C
                        LEFT JOIN `chattext` T
                        ON C.`contentno` = T.`no`
                            AND C.`senderid` = T.`senderid`
                            AND C.`receiverid` = T.`receiverid`)
                    SELECT T.`no`, T.`senderid`, T.`receiverid`, T.`datetime`, T.`contenttype`, T.`contentno`, 
                            T.`readstatus`, T.`text`, F.`path`, F.`width`
                        FROM `temptbl` T
                        LEFT JOIN `chatfile` F
                        ON T.`contentno` = F.`no`
                            AND T.`senderid` = F.`senderid`
                            AND T.`receiverid` = F.`receiverid`
                        WHERE (T.`senderid` = :senderid AND T.`receiverid` = :receiverid AND T.`contentno` = :contentno)';
    
        $parameters = [ 
            ':senderid' => $senderid, 
            ':receiverid' => $receiverid,
            ':contentno' => $contentNo
        ];

        $result = $this->query($query, $parameters);
        return $result->fetchAll();
    }

    /**
     *  [trade] 조건에 만족하는 글 목록을 가져온다.
     */
    public function getTradeList($campus, $category, $search) {
        $query = 'SELECT * FROM `' . $this->table . '`';
        $query .= $this->tradeHelper($campus, $category, $search);
        $query .= ' ORDER BY `id` DESC';

        if ($search) {
            $parameters = [
                'search' => '%' . $search . '%'
            ];
        
            $query = $this->query($query, $parameters);
        } else {
           $query = $this->query($query); 
        }

        return $query->fetchAll();
    }

    /**
     *  [trade] 조건에 만족하는 글에 대응하는 이미지 목록을 가져온다.
     */
    public function getTradeImgList($campus, $category, $search) {
        $query = 'WITH `temp`(`id`, `userid`, `imgid`) 
                    AS 
                    (SELECT `id`, `userid`, `imgid`
                        FROM `' . $this->table . '`';
        $query .= $this->tradeHelper($campus, $category, $search);
        $query .= ') ';
        $query .= 'SELECT T.`id`, TI.`path`, TI.`width`
                    FROM  `tradeimg` TI
                        INNER JOIN `temp` T
                        ON  TI.`userid`=T.`userid` AND TI.`id`=T.`imgid`
                    ORDER BY `id` DESC';

        if ($search) {
            $parameters = [
                'search' => '%' . $search . '%'
            ];
        
            $query = $this->query($query, $parameters);
        } else {
           $query = $this->query($query); 
        }

        return $query->fetchAll();
    }

    // trade helper function
    private function tradeHelper($campus, $category, $search):string {
        $printAnd = false;
    
        $query = '';
        if ($campus) {
            if (!$printAnd) { $query .= ' WHERE '; }
            $query .= "(`campus` = '" . ($campus == 1 ? 'S' : 'U') . "' OR `campus` = 'B' )";
            $printAnd = true;
        }
        if ($category) {
            if ($printAnd) { $query .= ' AND '; }
            else { $query .= ' WHERE '; }
            $query .= '`categoryid` = ' . $category;
            $printAnd = true;
        }
        if ($search) {
            if ($printAnd) { $query .= ' AND '; }
            else { $query .= ' WHERE '; }
            $query .= "((`title` LIKE :search) OR (`info` LIKE :search))";
        }

        return $query;
    }

    /**
     *  날짜 형식 처리
     */
    private function processDate(array $fields) {
        foreach ($fields as $key => $value) {
            if ($value instanceof DateTime) {
                $fields[$key] = $value->format('Y-m-d');
            }
        }

        return $fields;
    }
    private function processDateTime(array $fields) {
        foreach ($fields as $key => $value) {
            if ($value instanceof DateTime) {
                $fields[$key] = $value->format('Y-m-d H:i:s');
            }
        }

        return $fields;
    }

} // END OF 'DatabaseTable' CLASS

?>
