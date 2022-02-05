<?php

$ROUTES = [
    'login' => [
        'GET' => [ 'action' => 'login' ],
        'POST' => [ 'action' => 'processLogin' ]
    ],
    'terms' => [
        'GET' => [ 'action' => 'terms' ]
    ],
    'join' => [
        'GET' => [ 'action' => 'join' ],
        'POST' => [ 'action' => 'join' ]
    ],
    'verifyemail' => [
        'GET' => [ 'action' => 'verifyemail' ],
        'POST' => [ 'action' => 'verifyemail' ]
    ],
    'findid' => [
        'GET' => [ 'action' => 'findId' ],
        'POST' => [ 'action' => 'findId' ]
    ],
    'findpw' => [
        'GET' => [ 'action' => 'findPw' ],
        'POST' => [ 'action' => 'findPw' ]
    ],
    'findmsg' => [
        'GET' => [ 'action' => 'findMsg' ]
    ]

];

?>
