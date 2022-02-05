<?php

//=============================================================================//
// XSS 대응을 위한 HTML 이스케이프
//=============================================================================//

    function es($data, $charset='UTF-8') {
        // $data가 배열인 경우
        if (is_array($data)) {
            // 재귀호출
            // __METHOD__은 현재 실행 중인 메서드를 가리키는 상수이다.
            return array_map(__METHOD__, $data);
        } 
        else {
            return htmlspecialchars($data, ENT_QUOTES, $charset);
        }
    }


//=============================================================================//
// UTF-8 문자 길이 구하기
//=============================================================================//

    function utf8_strlen($str) {
        $length = strlen(utf8_decode($str));
        return $length;
    }


//=============================================================================//
// 비연속적인 배열을 연속적인 배열로 변환환다.
//=============================================================================//

    function changeToSequentialArray(array &$arr) {
        $tmpArr = [];
        foreach ($arr as $key => $value) {
            $tmpArr[] = $value;
        }
        $arr = $tmpArr;
    }


//=============================================================================//
// 숫자 간략한 형태(K, M)로 변환하기
//=============================================================================//

    function numToStr(int $num):string {
        $unit = 1000;   // 단위

        if ($num < $unit) {                                                               
            return $num;                                                                       
        } else if ($num < ($unit ** 2)) {   // K

            // 두 자리 정수까지는 소수점 아래 첫째 자리까지도 표현할 것이기 때문에 
            // 10을 먼저 곱한 후 1000으로 나누어서 불필요한 반올림을 방지한다. 
            if ($num < (($unit ** 2) / 10)) {
                $num = $num * 10;
                $num = (int)($num / $unit);
                return sprintf('%.1f', $num / 10) . 'K';                                 
            }                                                                       
            // 세 자리 정수는 소수점 아래 부분을 표현하지 않는다.
            else {                                              
                return (int)($num / $unit) . 'K';            
            }                                           

        } else if ($num < ($unit ** 3)) {    // M
            if ($num < (($unit ** 3) / 10)) {
                $num = $num * 10;
                $num = (int)($num / ($unit ** 2));
                return sprintf('%.1f', $num / 10) . 'M';
            } else {
                return (int)($num / ($unit ** 2)) . 'M';     
            }
        } else {    // 999M 넘는 수는 표현하지 않는다.
            return '999M';
        }
    }


//=============================================================================//
// mariadb DATETIME을 YYYY년 M월 D일 AM/PM H:M 형식으로 변환하기
//=============================================================================//

    function datetimeFormatted($datetime) {
        $dt = DateTime::createFromFormat('Y-m-d H:i:s', $datetime);
        return $dt->format("Y년 n월 j일 A g:i");
    }


//=============================================================================//
// 이메일의 아이디 부분을 일부만 출력한다.
//=============================================================================//
    
    function emailSecureString(string $email): string {
        $email = explode('@', $email);  // 아이디와 주소 부분을 배열로 나눈다.

        /* 아이디를 앞 3자리만 남기고 '*'로 바꾼다. */
        $idLen = mb_strlen($email[0]);  // 아이디 길이
        $pattern = "/^(\w{3})\w*$/";
        $replacement = "$1";
        for ($i = 0; $i < ($idLen - 3); $i++) {
            $replacement .= "*";
        }
        $email[0] = preg_replace($pattern, $replacement, $email[0]);

        return implode("@", $email);
    }


//=============================================================================//
// 가격 3자리 콤마(,) 구분해서 표시한다.
//=============================================================================//

    function priceCommaFormatted(string $price): string {
        $ret = [];
        $len = mb_strlen($price);
        for ($i = 0; $i < $len; $i++) {
            if ($i % 3 == 0 && $i != 0) {
                $ret[] = ',';
            }
            $ret[] = $price[$len - (1 + $i)];
        }
        $ret = array_reverse($ret);
        $ret = implode('', $ret);
        return $ret;
    }


?>
