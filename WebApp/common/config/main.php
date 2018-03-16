<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
            //'class' => 'yii\redis\Cache',
        ],
//         'cache' => [
//        // 'class' => 'yii\caching\FileCache',
//             'class' => 'yii\redis\Cache',
//         ],
//         'session' => [
//         // 'class' => 'yii\caching\FileCache',
//             'class' => 'yii\redis\Session',
//         ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => 'localhost',
            'port' => 6379,
            'database' => 0,
        ],
    ],
];
