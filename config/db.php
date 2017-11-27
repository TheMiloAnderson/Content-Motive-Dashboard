<?php

return [
    'dev' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=127.0.0.1;dbname=cmdash_demo',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
    ],
    'prod' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=10.137.151.228;dbname=cmdash',
        'username' => 'contentmotive',
        'password' => 'C0nt3ntM0t1v3@1',
        'charset' => 'utf8',
    ]
];
