<?php

namespace app\assets;

use yii\web\AssetBundle;

class DashboardAssets extends AssetBundle {
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
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
        'css/site.css',
        '//fonts.googleapis.com/css?family=Bree+Serif',
        'css/global.css',
    ];
    public $depends = [
    ];
}