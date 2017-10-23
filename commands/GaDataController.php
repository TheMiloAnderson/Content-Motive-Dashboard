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
        $analytics->updateDetails();
    }
    // In case the aggregates ever need to be rebuilt manually
    // e.g. php ./yii ga-data/update-aggregates 2013-07-09 2016-09-10 35
    public function actionUpdateAggregates($startDate, $endDate, $propID) {
        $analytics = new GoogleAnalyticsDB();
        $analytics->property = GoogleAnalyticsProperties::find()->where(['id' => $propID])->one();
        $output = $analytics->updateAggregates($startDate, $endDate);
        $this->stdOut($output);
    }
}