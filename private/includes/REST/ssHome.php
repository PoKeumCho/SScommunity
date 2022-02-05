<?php

$ROUTES = [
    'about' => [
        'GET' => [ 'action' => 'about' ]
    ],

    'generalcategory' => [
        'GET' => [ 'action' => 'generalCategory' ],
        'POST' => [ 'action' => 'generalCategoryPost' ],

        // 접근 제한 페이지
        'login' => true,
        'sungshin' => true
    ],
    'general' => [
        'GET' => [ 'action' => 'general' ],
        'POST' => [ 'action' => 'generalPost' ],
        
        // 접근 제한 페이지
        'login' => true,
        'sungshin' => true
    ],
    'generalview' => [
        'GET' => [ 'action' => 'generalView' ],
        'POST' => [ 'action' => 'generalViewPost' ],
    
        // 접근 제한 페이지
        'login' => true,
        'sungshin' => true
    ],

    'schedule' => [
        'GET' => [ 'action' => 'schedule' ],

        // 접근 제한 페이지
        'login' => true
    ],

    'trade' => [
        'GET' => [ 'action' => 'trade' ],
        'POST' => [ 'action' => 'tradePost' ],

        // 접근 제한 페이지
        'login' => true,
        'sungshin' => true
    ],

    'myinfo' => [
        'GET' => [ 'action' => 'myInfo' ],
        'POST' => [ 'action' => 'myInfoPost' ],

        // 접근 제한 페이지
        'login' => true
    ],

    'myarticle' => [
        'GET' => [ 'action' => 'myArticle' ],
        'POST' => [ 'action' => 'myArticlePost' ],

        // 접근 제한 페이지
        'login' => true
    ],

    'logout' => [
        'GET' => [ 'action' => 'logout' ]
    ],

    'sungshinerror' => [
        'GET' => [ 'action' => 'sungshinError' ]
    ],
    'loginerror' => [
        'GET' => [ 'action' => 'loginError' ]
    ]
];

?>
