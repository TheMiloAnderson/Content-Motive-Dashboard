<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Users;
use app\models\DashboardData;
use yii\grid\GridView;

class DashboardController extends Controller {
    
    public function actionContent() {
        return $this->loadPage('contentProperties');
    }
    public function actionBlogs() {
        return $this->loadPage('blogProperties');
    } 
    public function actionReviews() {
        return $this->loadPage('reviewProperties');
    } 
    public function actionMicrosites() {
        return $this->loadPage('microProperties');
    }
    private function loadPage($category) {
        $this->layout = 'main';
        $currentUser = $this->getCurrentUser();
        $dealers = $currentUser->getDealers()->with($category)->asArray()->all();
        $this->simplifyArray($dealers, $category);
        return (!empty($dealers[0])) ? $this->render('index', ['dealers' => $dealers]) : $this->render('nodata', []);
    }
    ///***** AJAX Actions *****///
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
                    'label' => $this->createSummary($dataProvider),
                    'encodeLabel' => false,
                    'value' => function($data) {return $this->formatPage($data);},
                    'contentOptions' => ['class' => 'content-strategy']],
                ['attribute' => 'entrances',
                    'label' => '',
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
    private function simplifyArray(&$array, $key) { // this makes it easier to handle Content, Blogs, Micro in the same view template
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
    private function createSummary($dataProvider) {
        $dataProvider->prepare();
        $paginationObj = $dataProvider->getPagination();
        $rangeStart = (($paginationObj->pageSize * $paginationObj->page) + 1);
        $rangeEnd = ($paginationObj->pageSize * ($paginationObj->page + 1));
        $total = $paginationObj->totalCount;
        $summary = 'Showing ' . $rangeStart . '-';
        $summary .= $rangeEnd > $total ? $total : $rangeEnd;
        $summary .= ' of ' . $total . ' items.';
        return $summary;
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
