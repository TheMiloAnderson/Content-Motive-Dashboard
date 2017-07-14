<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;

use Yii;
use app\models\GaAnalyticsData;
use yii\helpers\ArrayHelper;

class GaAnalyticsReports {
    
    public function report($dealer_id) {
        $properties = GaProperties::find()
            ->select('id')
            ->where(['dealer_id' => $dealer_id])
            ->asArray()
            ->all();
        $propIds = ArrayHelper::getColumn($properties, 'id');
        $reports = GaAnalyticsData::find()
            ->where(['property_id' => $propIds])
            ->asArray()
            ->all();
    }
    
    public function chartData($pids, $start = false, $end = false) {
        \Yii::$app->db->createCommand('SET @runtot1 = 0; SET @runtot2 = 0; SET @runtot3 = 0;')->query();
        $query = '
            SELECT 
                ga.property_id,
                ga.avg_time,
                ga.avg_bounce_rate,
                ga.date_recorded,
                (@runtot1 := @runtot1 + ga.pv) AS total_pageviews,
                (@runtot2 := @runtot2 + ga.upv) AS total_unique_pageviews,
                (@runtot3 := @runtot3 + ga.ent) AS total_entrances,
                ga.pv,
                ga.upv
            FROM
                (SELECT SUM(pageviews) AS pv, SUM(unique_pageviews) AS upv, SUM(entrances) AS ent,
                date_recorded, property_id, entrances, AVG(avg_time) AS avg_time, AVG(bounce_rate) AS avg_bounce_rate
                FROM ga_analytics
                WHERE property_id IN (';
        $params = [];
        for ($i=0; $i<count($pids); $i++) {
            $query .= $i?',':'';
            $query .= ':pid' . $i;
            $params[':pid' . $i] = $pids[$i];
        }
        $query .= ')';
        if ($start && $end) {
            $params[':start'] = $start;
            $params[':end'] = $end;
            $query .= ' AND date_recorded BETWEEN ":start" and ":end"';
        }
        $query .= ' GROUP BY property_id, date_recorded
                 ORDER BY date_recorded
                ) AS ga';
        $results = \Yii::$app->db->createCommand($query, $params)->queryAll();
        $data = $this->arr2csv($results);
        return $data;
    }
    
    private function arr2csv($reports) {
        $fld = ',';
        $lnd = '\n';
        
        $reportStr = '';
        if ($reports) {
            foreach($reports[0] as $key => $val) {
                $reportStr .= $key . $fld;
            }
            $reportStr = rtrim($reportStr, $fld);
            $reportStr .= $lnd;
            foreach($reports as $report) {
                foreach($report as $key => $val) {
                    $reportStr .= $val . $fld;
                }
                $reportStr = rtrim($reportStr, $fld);
                $reportStr .= $lnd;
            }
        }
        return $reportStr;
    }
}