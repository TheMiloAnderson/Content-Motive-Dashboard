<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\commands\models\GoogleAnalyticsDB;
use app\models\GaProperties;

class GaDataController extends Controller {
    
    public function actionUpdate() {
        set_time_limit(86400);
        $properties = GaProperties::find()->all();
        $analytics = new GoogleAnalyticsDB();
        foreach ($properties as $property) {
            $analytics->property = $property;
            $analytics->updateDB();
        }
    }
}