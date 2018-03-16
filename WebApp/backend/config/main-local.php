<?php

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'P-GKqewsxG9mQizG-YoXbJvZmkItxZi1',
        ],
        'db' => [
//             'class' => 'yii\db\Connection',
//             'dsn' => 'mysql:host=127.0.0.1;port=3307;dbname=testjson',
//             'username' => 'root',
//             'password' => '',
//             'charset' => 'utf8',
        ],
    ],
];

if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs'=>['*'],
        'generators' => [
            'mongoDbModel' => [
                'class' => 'yii\mongodb\gii\model\Generator'
            ],
        ]
    ];
//    $config['bootstrap'][] = 'mongogii';
//    $config['modules']['mongogii'] ['class']= 'yii\mongodb\gii\model\Generator';
//    $config['modules']['mongogii']['allowedIPs'] =['127.0.0.1','201.104.104.104',"::1"];
}

return $config;
