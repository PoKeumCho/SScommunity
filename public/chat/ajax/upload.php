<?php

header('Content-Type: application/json');

require_once __DIR__ . '/' . '../../../private/includes/lib/modifyImage.php';
require_once __DIR__ . '/' . '../../../private/includes/lib/genRandStr.php';

$response = [];

/* Getting file name */
$filename = [];
// 파일은 최대 3개 업로드 가능한다.
for ($i = 0; $i < 3; $i++) {
    if (isset($_FILES['file_' . $i])) {
        $filename[] = $_FILES['file_' . $i]['name']; 
    } else {
        break;
    }
}

/* Location */
$date = new DateTime();
$location = __DIR__ . '/' . '../../../file/images/chat/';


/**
 *  Upload file 
 */

$response['path'] = [];     // 파일이 저장된 경로명을 저장한다.
$response['width'] = [];    // 파일의 width를 저장한다.

for ($i = 0; $i < count($filename); $i++) {

    // 파일 확장자 구하기
    $fileType = $_FILES['file_'. $i]['type'];
    $fileType = explode('/', $fileType)[1];
    if ($fileType != 'jpg' && $fileType != 'gif' 
            && $fileType != 'png' && $fileType != 'jpeg' && $fileType != 'bmp') {
        $fileType = false;
    }

    if ($fileType) {
        $path = $date->format('Y-m-d') . '/' . generate_string(100) . '.' . $fileType; 
        $fileTmpName = $_FILES['file_'. $i]['tmp_name'];
         
        list($width, $height, $type) = getimagesize($fileTmpName);
        $old_image = load_image($fileTmpName, $type);

        // 이미지의 가로, 세로가 변경되어 등록되는 경우 처리
        if (image_fix_orientation($old_image, $fileTmpName)) {
            $temp = $width;
            $width = $height;
            $height = $temp;
        }

        // 픽셀 크기를 축소하는 경우
        $changePxWidth = 512;
        if ($width > $changePxWidth) {
            $tempImg = resize_image_to_width($changePxWidth, $old_image, $width, $height);
            imagejpeg($tempImg, $fileTmpName, 100);
            $width = $changePxWidth;
        }

        $response['width'][] = $width;

        // $response['path'] 배열의 개수가 저장에 성공한 파일의 개수가 된다.
        if ($width && move_uploaded_file($fileTmpName, $location . $path)) {
            $response['result'] = 1;
            $response['path'][] = $path;
        } else {
            $response['result'] = 0;
            break;
        }
    } else {    // 파일 확장자 올바르지 않은 경우
        $response['result'] = 0;
        break;
    }
    
}

echo json_encode($response);

?>
