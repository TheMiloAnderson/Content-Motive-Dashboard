<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Users;
use app\models\DashboardData;
//use app\commands\models\GoogleAnalytics;
use yii\grid\GridView;

class DashboardController extends Controller {
    
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
    
    public function actionDetails(array $pids, $start='', $end='') {
        $model = new DashboardData();
        $dataProvider = $model->details($pids, $start, $end);
        $dataProvider->setSort([
                    'attributes' => [
                        'page',
                        'pageviews' => ['default' => SORT_DESC],
                        'visitors' => ['default' => SORT_DESC],
                        'entrances' => ['default' => SORT_DESC],
                        'avg_time' => ['default' => SORT_DESC],
                        'bounce_rate',
                    ],
                    'defaultOrder' => [
                        'entrances' => SORT_DESC,
                    ],
                ]);
        $html = GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                'page',
                ['attribute' => 'entrances',
                'format' => 'integer'],
                ['attribute' => 'visitors',
                'format' => 'integer'],
                ['attribute' => 'pageviews',
                'format' => 'integer'],
                ['attribute' => 'avg_time',
                'value' => function($data) {return $this->formatTime($data);}],
                ['attribute' => 'bounce_rate',
                'format' => 'percent'],
            ]
        ]);
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        return $html;
    }
    
    public function actionKeywords($start, $end, $view) {
//        $model = new GoogleAnalytics();
//        $data = $model->fetchKeywords($start, $end, $view);
//        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
//        ini_set('xdebug.var_display_max_depth', '100');  
//        return var_dump($data);
    }
    
    public function actionContentAll() {
        $currentUser = $this->getCurrentUser();
        $dealers = $currentUser->getDealers()->with('contentProperties')->asArray()->all();
        $this->simplifyArray($dealers, 'contentProperties');
        foreach($dealers as &$dealer) {
            foreach($dealer['properties'] as &$property) {
                $model = new DashboardData();
                $data = $model->aggregates($property['id']);
                $property['aggregates'] = $data;
            }
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        return json_encode($dealers);
    }
    
    //*** utility functions ***//
    private function getCurrentUser() {
        $currentUserId = Yii::$app->user->id;
        return Users::find()->where(['id' => $currentUserId])->one();
    }
    private function simplifyArray(&$array, $key) {
        // this makes it easier to handle Content, Blogs, Micro in the same view template
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
    private function formatTime($data) {
        if (is_object($data)) {
            $seconds = '0' . ($data->avg_time % 60);
            return (int)($data->avg_time / 60) . ':' . substr($seconds, -2);
        } else {
            $seconds = '0' . ($data['avg_time'] % 60);
            return (int)($data['avg_time'] / 60) . ':' . substr($seconds, -2);
        }
    }
}
