<?php

$ROUTES = [

    /* ========================================================= */
    /*  ssHomeController                                         */
    /* ========================================================= */

    'about' => [
        'GET' => [ 
            'controller' => 'ssHomeController',
            'action' => 'about' 
        ]
    ],

    'generalcategory' => [
        'GET' => [ 
            'controller' => 'ssHomeController',
            'action' => 'generalCategory' 
        ],
        'POST' => [ 
            'controller' => 'ssHomeController',
            'action' => 'generalCategoryPost' 
        ],

        // 접근 제한 페이지
        'login' => true,
        'sungshin' => true
    ],
    'general' => [
        'GET' => [ 
            'controller' => 'ssHomeController',
            'action' => 'general' 
        ],
        'POST' => [ 
            'controller' => 'ssHomeController',
            'action' => 'generalPost' 
        ],
        
        // 접근 제한 페이지
        'login' => true,
        'sungshin' => true
    ],
    'generalview' => [
        'GET' => [ 
            'controller' => 'ssHomeController',
            'action' => 'generalView' 
        ],
        'POST' => [ 
            'controller' => 'ssHomeController',
            'action' => 'generalViewPost' 
        ],
    
        // 접근 제한 페이지
        'login' => true,
        'sungshin' => true
    ],

    'schedule' => [
        'GET' => [ 
            'controller' => 'ssHomeController',
            'action' => 'schedule' 
        ],

        // 접근 제한 페이지
        'login' => true
    ],

    'trade' => [
        'GET' => [ 
            'controller' => 'ssHomeController',
            'action' => 'trade' 
        ],
        'POST' => [ 
            'controller' => 'ssHomeController',
            'action' => 'tradePost' 
        ],

        // 접근 제한 페이지
        'login' => true,
        'sungshin' => true
    ],

    'myinfo' => [
        'GET' => [ 
            'controller' => 'ssHomeController',
            'action' => 'myInfo' 
        ],
        'POST' => [ 
            'controller' => 'ssHomeController',
            'action' => 'myInfoPost' 
        ],

        // 접근 제한 페이지
        'login' => true,
    ],

    'myarticle' => [
        'GET' => [ 
            'controller' => 'ssHomeController',
            'action' => 'myArticle' 
        ],
        'POST' => [ 
            'controller' => 'ssHomeController',
            'action' => 'myArticlePost' 
        ],

        // 접근 제한 페이지
        'login' => true
    ],

    'logout' => [
        'GET' => [ 
            'controller' => 'ssHomeController',
            'action' => 'logout' 
        ]
    ],

    'sungshinerror' => [
        'GET' => [ 
            'controller' => 'ssHomeController',
            'action' => 'sungshinError' 
        ]
    ],
    'loginerror' => [
        'GET' => [ 
            'controller' => 'ssHomeController',
            'action' => 'loginError' 
        ]
    ],


    /* ========================================================= */
    /*  loginController                                          */
    /* ========================================================= */

    'login' => [
        'GET' => [ 
            'controller' => 'loginController',
            'action' => 'login' 
        ],
        'POST' => [ 
            'controller' => 'loginController',
            'action' => 'processLogin' 
        ]
    ],
    'terms' => [
        'GET' => [ 
            'controller' => 'loginController',
            'action' => 'terms' 
        ]
    ],
    'join' => [
        'GET' => [ 
            'controller' => 'loginController',
            'action' => 'join' 
        ],
        'POST' => [ 
            'controller' => 'loginController',
            'action' => 'join' 
        ]
    ],
    'verifyemail' => [
        'GET' => [
            'controller' => 'loginController',
            'action' => 'verifyemail' 
        ],
        'POST' => [ 
            'controller' => 'loginController',
            'action' => 'verifyemail' 
        ]
    ],
    'findid' => [
        'GET' => [ 
            'controller' => 'loginController',
            'action' => 'findId' 
        ],
        'POST' => [ 
            'controller' => 'loginController',
            'action' => 'findId' 
        ]
    ],
    'findpw' => [
        'GET' => [
            'controller' => 'loginController',
            'action' => 'findPw'
        ],
        'POST' => [
            'controller' => 'loginController',
            'action' => 'findPw'
        ]
    ],
    'findmsg' => [
        'GET' => [ 
            'controller' => 'loginController',
            'action' => 'findMsg' 
        ]
    ]
];

?>
