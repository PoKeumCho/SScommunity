<?php

// Generate Random Strings
// [참고] https://code.tutsplus.com/tutorials/generate-random-alphanumeric-strings-in-php--cms-32132
function generate_string(int $strength = 16, 
    string $input = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') 
{
    $input_length = strlen($input);
    $random_string = '';
    for ($i = 0; $i < $strength; $i++) {
        $random_character = $input[mt_rand(0, $input_length - 1)];
        $random_string .= $random_character;
    }

    return $random_string;
}

?>
