<?php

// comment out the following two lines when deployed to production
$server_name = filter_input(INPUT_SERVER, 'SERVER_NAME', FILTER_SANITIZE_STRING);
if ($server_name === 'localhost' || $server_name === 'cmdash.local') {
    defined('YII_DEBUG') or define('YII_DEBUG', true);
    defined('YII_ENV') or define('YII_ENV', 'dev');
} else {
    defined('YII_DEBUG') or define('YII_DEBUG', true);
    defined('YII_ENV') or define('YII_ENV', 'prod');
}

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../config/web.php');

(new yii\web\Application($config))->run();