<?php

namespace app\assets;

use yii\web\AssetBundle;

class DealerFormAssets extends AssetBundle {
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/dealerForm.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}