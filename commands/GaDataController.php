<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\GaAnalyticsLive;
use app\models\GaProperties;

class GaDataController extends Controller {
    
    public function actionGet($url) {
        set_time_limit(6000);
        $property = GaProperties::find()
            ->where(['url' => $url])->one();
        $analytics = new GaAnalyticsLive();
        $analytics->hostname = $url;
        $analytics->ga_view = $property->ga_view;
        
        //$latestParams = [':url', $url];
        $latest = Yii::$app->db->createCommand('
            SELECT MAX(date) as date FROM (
                SELECT p.start_date as date
                FROM ga_properties p
                WHERE p.url = :url
                UNION
                SELECT a.date_recorded as date 
                FROM ga_analytics a
                INNER JOIN ga_properties p2 ON p2.id = a.property_id
                WHERE p2.url = :url
            ) AS latest;', [':url' => $url])
            ->queryOne();
        
        $startDate = new \DateTime($latest['date']);
        $getDate = $startDate->modify('+1 day');
        $startDate = $startDate->format('Y-m-d');
        $yesterday = new \DateTime(date('Y-m-d', strtotime('yesterday')));
        
        // loop through the missing dates, query API, save records
        $i = 1;
        $count = 0;
        $start = time();
        while($getDate <= $yesterday) {
            $analytics->date = $getDate->format('Y-m-d');
            try {
                $report = $analytics->getReport();
            } catch (Exception $ex) {
                echo "Could not GET report!\n";
            }
            try {
                $data = $analytics->saveAnalytics($report);
            } catch (Exception $ex) {
                echo "Could not SAVE report!\n";
            }
            echo "saved " . count($data) . " records; $analytics->hostname for $analytics->date \n";
            $getDate->modify('+1 day');
            $count += count($data);
            
            // mind Google's speed limit (100 reqs in 100 secs, per user)
            $i++;
            if($i >= 100 && ($dur = time() - $start - $i) < 0) {
                echo "$i requests in $dur seconds\n";
                sleep(abs($dur) + 5);
                $start = time();
                $i = 1;
            }
        }
        echo "all done; added $count records between $startDate and " . $yesterday->format('Y-m-d') . "\n";
    }
    
    public function actionAggregate() {
        
    }
}