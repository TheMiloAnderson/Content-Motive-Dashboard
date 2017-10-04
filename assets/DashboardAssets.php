<?php

namespace app\assets;

use yii\web\AssetBundle;

class DashboardAssets extends AssetBundle {
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        //'js/d3.min.js',
        //'js/d3-interpolate-path.js',
        //'js/jquery-ui.min.js',
        //'js/dashboard.js',
        //'js/vue.js',
        //'js/chart.js',
        //'js/dash.js',
        //'js/simplify.js',
        'js/build/dashboard.bundle.min.js',
    ];
    public $css = [
        '//fonts.googleapis.com/css?family=Roboto:400,500,700',
        '//fonts.googleapis.com/css?family=Open+Sans',
        '//fonts.googleapis.com/css?family=Open+Sans+Condensed:700',
        'css/jquery-ui.min.css',
        'css/jquery-ui.structure.min.css',
        'css/jquery-ui.theme.min.css',
        'css/dashboard.css',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}