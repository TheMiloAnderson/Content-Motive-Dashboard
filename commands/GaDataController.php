<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\commands\models\GoogleAnalyticsDB;
use app\models\gii\GoogleAnalyticsProperties;

class GaDataController extends Controller {
    
    public function actionUpdate() {
        set_time_limit(86400);
        $properties = GoogleAnalyticsProperties::find()->all();
        $analytics = new GoogleAnalyticsDB();
        foreach ($properties as $property) {
            $analytics->property = $property;
            $analytics->updateDB();
        }
        $result = $analytics->updateDetails();
        echo 'Updated ga_analytics_details; ' . $result . ' records\n\n';
    }
    
    public function actionEvents() {
        set_time_limit(86400);
        $properties = GoogleAnalyticsProperties::find()->where(['id' => 64])->all();
        $analytics = new GoogleAnalyticsDB();
        foreach ($properties as $property) {
            $analytics->property = $property;
            $analytics->updateDB();
        }
        $result = $analytics->updateDetails();
        echo 'Updated ga_analytics_details; ' . $result . ' records\n\n';
    }
}