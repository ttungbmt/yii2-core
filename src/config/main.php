<?php

return [
    'language'       => 'vi-VN',
    'sourceLanguage' => 'en-US',
    'timezone'       => 'Asia/Ho_Chi_Minh',
    'components' => [
        'urlManager'   => [
            'class'           => 'yii\web\UrlManager',
            'showScriptName'  => false, // Disable index.php
            'enablePrettyUrl' => true, // Disable r= routes
            'rules'           => [
            ]
        ],
    ],
    'container' => [
        'definitions' => [
            'yii\web\Request' => [
                'class' => 'ttungbmt\web\Request'
            ],
            'yii\web\Session'                 => [
                'class' => 'common\supports\Session'
            ],
        ],
    ],
];
