<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\GaAnalyticsReports;
use app\models\Dealers;

class DashboardController extends Controller {
    
    public function actionIndex(array $pids = []) {
        if (!$pids) {
            // fix this so it defaults to the user's dealer-properties (defaults to Larson now)
            $pids = [34, 35, 36];
        }
        $report = new GaAnalyticsReports();
        $report = $report->chartData($pids);
        $dealers = Dealers::find()
            ->all();
        return $this->render('index', [
            'report' => $report,
            'dealers' => $dealers,
        ]);
    }
    
    public function actionAggregate(array $pids) {
        $report = new GaAnalyticsReports();
        $data = $report->chartData($pids);
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        return $data;
    }
}
