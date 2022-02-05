<?php

//=============================================================================//
//  현재 시간표에 시간이 중복되지 않는지 확인한다.
//=============================================================================//

function checkSavePossibility(string $classTime, array $userClassTime): bool {
    $classTimeArray = explode(',', $classTime);

    foreach($userClassTime as $time) {
        $timeArray = explode(',', $time);
        for ($i = 0; $i < count($classTimeArray); $i++) {
            for ($j = 0; $j < count($timeArray); $j++) {
                $i_array = explode('/', $classTimeArray[$i]);
                $j_array = explode('/', $timeArray[$j]);
                if ($i_array[0] == $j_array[0]) {
                    $i_time = $i_array[1];
                    $j_time = $j_array[1]; 

                    $i_time_array = explode('-', $i_time);
                    $j_time_array = explode('-', $j_time);

                    if (count($i_time_array) == 1) {
                        $i_time_min = $i_time_max = $i_time_array[0];
                    } else {
                        $i_time_min = $i_time_array[0];
                        $i_time_max = $i_time_array[1];
                    }
                    if (count($j_time_array) == 1) {
                        $j_time_min = $j_time_max = $j_time_array[0];
                    } else {
                        $j_time_min = $j_time_array[0];
                        $j_time_max = $j_time_array[1];
                    }

                    // 겹치는 경우
                    if (max($i_time_min, $j_time_min) <= min($i_time_max, $j_time_max)) {
                        return false;
                    }
                }
            } 
        }
    }
    return true;
}

?>
