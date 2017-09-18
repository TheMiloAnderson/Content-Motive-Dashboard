<?php

namespace app\models;

use Yii;
use app\models\GoogleAnalyticsAggregates;
use app\models\GoogleAnalyticsDetails;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;

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
    
    public function details($pids, $start, $end) {
        $pagesize = 10;
        if ($start == null && $end == null) {
            $query  = GoogleAnalyticsDetails::find()
                ->where(['in', 'property_id', $pids]);
            $provider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pageSize' => $pagesize,
                ],
            ]);
            return $provider; 
        } else {
            $totalCount = Yii::$app->db->createCommand('SELECT COUNT(*) FROM 
                (SELECT page
                FROM ga_analytics
                WHERE property_id IN (:pids)
                AND date_recorded BETWEEN :start AND :end
                GROUP BY page) as c')
                ->bindValue(':pids', implode(',', $pids))
                ->bindValue(':start', $start)
                ->bindValue(':end', $end)
                ->queryScalar();
            $query = 'SELECT
                property_id,
                page,
                SUM(pageviews) as pageviews,
                SUM(visitors) as visitors,
                SUM(entrances) as entrances,
                AVG(avg_time) as avg_time,
                IFNULL(SUM(bounce_rate * entrances)/SUM(entrances), 0) / 100 AS bounce_rate
            FROM ga_analytics
            WHERE property_id IN (:pids)
                AND date_recorded BETWEEN :start AND :end
            GROUP BY page';
            $provider = new SqlDataProvider([
                'sql' => $query,
                'pagination' => [
                    'pageSize' => $pagesize,
                ],
                'totalCount' => $totalCount,
                'params' => [
                    ':pids' => implode(',', $pids),
                    ':start' => $start,
                    ':end' => $end,
                ],
            ]);
            return $provider; 
        }
    }
}