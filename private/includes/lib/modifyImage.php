<?php

/** 
 *  [참고] https://www.devopsschool.com/blog/how-to-upload-and-compress-an-image-using-php/ 
 *
 *  Custom function to compress image size and 
 *  upload to the server using PHP 
 */ 
function compressImage($source, $destination, $quality) { 
    // Get image info 
    $imgInfo = getimagesize($source); 
    $mime = $imgInfo['mime']; 
     
    // Create a new image from file 
    switch($mime){ 
        case 'image/jpeg': 
            $image = imagecreatefromjpeg($source); 
            break; 
        case 'image/png': 
            $image = imagecreatefrompng($source); 
            break; 
        case 'image/gif': 
            $image = imagecreatefromgif($source); 
            break; 
        default: 
            $image = imagecreatefromjpeg($source); 
    } 
     
    // Save image 
    imagejpeg($image, $destination, $quality); 
     
    // Return compressed image 
    return $destination; 
}



/**
 *  [참고] https://tutorialio.com/resize-an-image-programmatically-in-php/
 */

function load_image($filename, $type) {
    if( $type == IMAGETYPE_JPEG ) {
        $image = imagecreatefromjpeg($filename);
    }
    elseif( $type == IMAGETYPE_PNG ) {
        $image = imagecreatefrompng($filename);
    }
    elseif( $type == IMAGETYPE_GIF ) {
        $image = imagecreatefromgif($filename);
    }
    return $image;
}

function resize_image($new_width, $new_height, $image, $width, $height) {
    $new_image = imagecreatetruecolor($new_width, $new_height);
    imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
    return $new_image;
}

function resize_image_to_width($new_width, $image, $width, $height) {
    $resize_ratio = $new_width / $width;
    $new_height = $height * $resize_ratio;
    return resize_image($new_width, $new_height, $image, $width, $height);
}



/**
 *  [참고]
 *  https://stackoverflow.com/questions/42033887/php-upload-with-exif-orientation-based-rotation/42034701
 *  https://stackoverflow.com/questions/43318816/gd-php-rotate-image-black-border
 */
function image_fix_orientation(&$image, $filename) {
    $rotated = false;
    $exif = exif_read_data($filename);
    
    if (!empty($exif['Orientation'])) {
        switch ($exif['Orientation']) {
            case 3:
                $image = imagerotate($image, 180, 0);
                break;
            case 6:
                $image = imagerotate($image, -90, 0);
                $rotated = true;
                break;
            case 8:
                $image = imagerotate($image, 90, 0);
                $rotated = true;
                break;
        }
    }

    return $rotated;
}

?>
