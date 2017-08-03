<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Users;
use app\models\DashboardData;
use yii\grid\GridView;

class DashboardController extends Controller {
    
    private function getCurrentUser() {
        $currentUserId = Yii::$app->user->id;
        return Users::find()->where(['id' => $currentUserId])->one();
    }
    
    private function simplifyArray(&$array, $key) {
        foreach ($array as &$item) {
            $item['properties'] = $item[$key];
            unset($item[$key]);
        }
        $count = count($array) - 1;
        for ($i=$count; $i>=0; $i--) {
            if (empty($array[$i]['properties'])) {
                unset($array[$i]);
            }
        }
        $array = array_values($array);
    }
    
    public function actionContent() {
        $currentUser = $this->getCurrentUser();
        $dealers = $currentUser->getDealers()->with('contentProperties')->asArray()->all();
        $this->simplifyArray($dealers, 'contentProperties');
        return $this->render('index', [
            'dealers' => $dealers,
        ]);
    }
    
    public function actionBlogs() {
        $currentUser = $this->getCurrentUser();
        $dealers = $currentUser->getDealers()->with('blogProperties')->asArray()->all();
        $this->simplifyArray($dealers, 'blogProperties');
        return $this->render('index', [
            'dealers' => $dealers,
        ]);
    }   
    
    public function actionMicrosites() {
        $currentUser = $this->getCurrentUser();
        $dealers = $currentUser->getDealers()->with('microProperties')->asArray()->all();
        $this->simplifyArray($dealers, 'microProperties');
        return $this->render('index', [
            'dealers' => $dealers,
        ]);
    }  
    
    public function actionIndex() {
        $currentUser = $this->getCurrentUser();
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
