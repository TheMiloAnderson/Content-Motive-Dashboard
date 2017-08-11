<?php

namespace app\models;

use Yii;
use app\models\GoogleAnalyticsAggregates;
use app\models\GoogleAnalyticsDetails;
use yii\data\ActiveDataProvider;

class DashboardData {
    
    public function aggregates($pids) {
        $data = GoogleAnalyticsAggregates::find()
            ->where(['in', 'property_id', $pids])
            ->joinWith('property', true, 'INNER JOIN')
            ->orderBy(['date_recorded' => SORT_ASC])
            ->asArray()
            ->all();
        foreach ($data as &$datum) {
            $datum['url'] = $datum['property']['url'];
            $datum['dealer_id'] = $datum['property']['dealer_id'];
            unset($datum['property']);
        }
        return $data;
    }
    
    public function details($pids) {
        $query  = GoogleAnalyticsDetails::find()->where(['in', 'property_id', $pids]);
        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'defaultOrder' => [
                    'pageviews' => SORT_DESC,
                ],
            ],
        ]);
        return $provider;        
    }
}