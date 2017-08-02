<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Users;
use app\models\DashboardData;
use yii\grid\GridView;

class DashboardController extends Controller {
    
    public function actionContent() {
        $currentUserId = Yii::$app->user->id;
        $currentUser = Users::find()->where(['id' => $currentUserId])->one();
        $dealers = $currentUser->getDealers()->with('contentProperties')->all();
        return $this->render('index', [
            'dealers' => $dealers,
        ]);
    }
    
    public function actionIndex() {
        $currentUserId = Yii::$app->user->id;
        $currentUser = Users::find()->where(['id' => $currentUserId])->one();
        $dealers = $currentUser->getDealers()->with('gaProperties')->all();
        return $this->render('index', [
            'dealers' => $dealers,
        ]);
    }
    
    public function actionAggregate(array $pids) {
        $model = new DashboardData();
        $data = $model->aggregates($pids);
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        return json_encode($data);
    }
    
    public function actionDetails($pid) {
        $model = new DashboardData();
        $dataProvider = $model->details($pid);
        $html = GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                'page',
                'pageviews',
                'visitors',
                'entrances',
                'avg_time',
                'bounce_rate',
            ]
        ]);
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        return $html;        
    }
}
