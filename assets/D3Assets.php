<?php

namespace app\assets;

use yii\web\AssetBundle;

class D3Assets extends AssetBundle {
    public $sourcePath = '@npm';
    public $baseUrl = '@web';
    public $js = [
        'd3/build/d3.min.js',
        'd3-interpolate-path/build/d3-interpolate-path.min.js',
        
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}