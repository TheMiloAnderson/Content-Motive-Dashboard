<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Users;
use app\models\DashboardData;
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
                        'pageviews' => ['default' => SORT_DESC],
                        'visitors' => ['default' => SORT_DESC],
                        'entrances' => ['default' => SORT_DESC],
                        'avg_time' => ['default' => SORT_DESC],
                        'bounce_rate' => ['default' => SORT_DESC],
                    ],
                    'defaultOrder' => [
                        'entrances' => SORT_DESC,
                    ],
                ]);
        $html = GridView::widget([
            'dataProvider' => $dataProvider,
            'layout'=>"{items}{pager}",
            'tableOptions' => ['class' => 'table table-bordered'],
            'columns' => [
                ['attribute' => 'page',
                    'label' => function() {return $this->summary;},
                    'value' => function($data) {return $this->formatPage($data);},
                    'contentOptions' => ['class' => 'content-strategy']],
                ['attribute' => 'entrances',
                    'label' => '',
                    'encodeLabel' => false,
                    'format' => 'integer',
                    'contentOptions' => ['class' => 'entrances']],
                ['attribute' => 'visitors',
                    'label' => '',
                    'format' => 'integer',
                    'contentOptions' => ['class' => 'visitors']],
                ['attribute' => 'pageviews',
                    'label' => '',
                    'format' => 'integer',
                    'contentOptions' => ['class' => 'pageviews']],
                ['attribute' => 'avg_time',
                    'label' => '',
                    'value' => function($data) {return $this->formatTime($data);},
                    'contentOptions' => ['class' => 'avg_time']],
                ['attribute' => 'bounce_rate',
                    'label' => '',
                    'format' => 'percent',
                    'contentOptions' => ['class' => 'bounce_rate']],
            ]
        ]);
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        return $html;
    }
    
    ///***** Utility Functions *****///
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
    private function formatPage($data) {
        if (is_object($data)) {
            $page = $data->page;
        } else {
            $page = $data['page'];
        }
        $patterns = [
            '/\/op/',
            '/\-/',
            '/\//',
        ];
        $page = preg_replace($patterns, ' ', $page);
        return trim($page);
    }
}
