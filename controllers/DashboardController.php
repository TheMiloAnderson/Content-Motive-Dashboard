<?php

namespace app\controllers;

use Yii;
use app\models\DashboardData;
use app\models\Users;
use yii\web\Controller;

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
        $currentUserId = Yii::$app->user->id;
        $currentUser = Users::find()->where(['id' => $currentUserId])->one();
        $dealers = $currentUser->getDealers()->with($category)->asArray()->all();
        foreach ($dealers as &$dealer) { // array cleanup :(
            $dealer['properties'] = $dealer[$category];
            unset($dealer[$category]);
        }
        $count = count($dealers) - 1;
        for ($i=$count; $i>=0; $i--) {
            if (empty($dealers[$i]['properties'])) {
                unset($dealers[$i]);
            }
        }
        $dealers = array_values($dealers); // end array cleanup
        return (!empty($dealers[0])) ? $this->render('index', ['dealers' => $dealers]) : $this->render('nodata', []);
    }
    ///***** AJAX Actions *****///
    public function actionAggregate(array $pids) {
        $model = new DashboardData();
        $data = $model->aggregates($pids);
        if ($data) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
            return json_encode($data);
        } else {
            return $this->render('nodata', []);
        }
    }
    public function actionDetails(array $pids, $start='', $end='') {
        $model = new DashboardData();
        $html = $model->details($pids, $start, $end);
        if ($html) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
            return $html;
        } else {
            return $this->render('nodata', []);
        }
    }
}