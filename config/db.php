<?php

return [
    'prod' => [
        'class' => 'yii\db\Connection',
        'dsn' => '',
        'username' => '',
        'password' => '',
        'charset' => 'utf8',
    ],
    'dev' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=127.0.0.1;dbname=cmdash_demo',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
    ]
];
