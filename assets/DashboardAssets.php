<?php

namespace app\assets;

use yii\web\AssetBundle;

class DashboardAssets extends AssetBundle {
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/d3.min.js',
        'js/d3-interpolate-path.js',
        'js/jquery-ui.min.js',
        'js/dashboard.js',
        //'js/vue.js',
        //'js/dash.js',
        'js/simplify.js',
    ];
    public $css = [
        'css/jquery-ui.min.css',
        'css/jquery-ui.structure.min.css',
        'css/jquery-ui.theme.min.css',
        'css/basics.css',
        'css/dash.css',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}