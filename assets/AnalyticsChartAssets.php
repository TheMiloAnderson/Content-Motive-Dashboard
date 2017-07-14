<?php

namespace app\assets;

use yii\web\AssetBundle;

class AnalyticsChartAssets extends AssetBundle {
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/jquery-ui.min.js',
        'js/mainChart.js',
    ];
    public $css = [
        'css/jquery-ui.min.css',
        'css/jquery-ui.structure.min.css',
        'css/jquery-ui.theme.min.css',
        'css/dashboard.css',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}