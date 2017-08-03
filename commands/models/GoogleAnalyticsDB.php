<?php

namespace app\commands\models;

use Yii;
use app\commands\models\GoogleAnalytics;
use app\models\GoogleAnalyticsData;

class GoogleAnalyticsDB extends GoogleAnalytics {
    
    public function updateDB() {
        // either start from 'start_date' or wherever the script left off
        $mostRecentUpdate = Yii::$app->db->createCommand('
            SELECT MAX(date) as date FROM (
                SELECT p.start_date as date
                FROM ga_properties p
                WHERE p.url = :url
                UNION
                SELECT a.date_recorded as date 
                FROM ga_analytics a
                INNER JOIN ga_properties p2 ON p2.id = a.property_id
                WHERE p2.url = :url
            ) AS latest;', [':url' => $this->property->url])
            ->queryOne();
        $startDate = new \DateTime($mostRecentUpdate['date']);
        $getUpdateDate = $startDate->modify('+1 day');
        $startUpdateDate = $startDate->format('Y-m-d');
        $endUpdateDate = new \DateTime(date('Y-m-d', strtotime('yesterday')));
        $endUpdateDate = $endUpdateDate->format('Y-m-d');
        // loop through the missing dates, query API, save records
        $count = 0;
        $start = microtime(true);
        while($getUpdateDate <= $endUpdateDate) {
            $this->recordDate = $getUpdateDate->format('Y-m-d');
            try {
                $report = $this->fetchReport();
            } catch (Exception $ex) {
                echo "Could not GET report!\n $ex";
            }
            try {
                $data = $this->saveAnalytics();
            } catch (Exception $ex) {
                echo "Could not SAVE report!\n $ex";
            }
            echo "saved " . count($data) . " records; " . $this->property->url . " for $this->recordDate \n";
            $getUpdateDate->modify('+1 day');
            $count += count($data);
            // mind Google's speed limit (100 reqs in 100 secs, per user)
            if (($dur = (microtime(true)) - $start) < 1) {
                $nap = 1 - $dur;
                echo "Nap! $nap \n";
                usleep($nap * 1000000);
            }
            $start = microtime(true);
        }
        echo "Done with " . $this->property->url . "; added $count records between $startUpdateDate and $endUpdateDate\n";
        echo $this->updateAggregates($startUpdateDate, $endUpdateDate) . "\n";
        echo $this->updateDetails($startUpdateDate, $endUpdateDate) . "\n\n";
    }
    
    private function formatAnalyticsData() {
        $report = $this->report[0];
        // get labels and data out of Google object, put in simple array
        $header = $report->getColumnHeader();
        $dimensionHeaders = $header->getDimensions();
        $metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
        $headers = $dimensionHeaders;
        foreach ($metricHeaders as $metricHeader) {
            $headers[] = $metricHeader['name'];
        }
        $rows = $report->getData()->getRows();
        $dataArray = array();
        for ($rowIndex = 0; $rowIndex < count($rows); $rowIndex++) {
            $row = $rows[$rowIndex];
            $dimensions = $row->getDimensions();
            $metrics = $row->getMetrics();
            foreach ($metrics as $metric) {
                $values = array_merge($dimensions, $metric['values']);
                $data = array_combine($headers, $values);
                $data['date_recorded'] = $this->recordDate;
                $data['property_id'] = $this->property->id;
                $dataArray[] = $data;
            }
        }
        return $dataArray;
    }
    
    public function saveAnalytics() {
        $data = $this->formatAnalyticsData();
        // fix the GA array keys so they match the DB fields
        array_walk($data, function(& $item) {  
            unset($item['ga:hostname']);
            $item['page'] = $item['ga:pagePath'];
                unset($item['ga:pagePath']);
            $item['pageviews'] = $item['ga:pageviews'];
                unset($item['ga:pageviews']);
            $item['unique_pageviews'] = $item['ga:uniquePageviews'];
                unset($item['ga:uniquePageviews']);
            $item['entrances'] = $item['ga:entrances'];
                unset($item['ga:entrances']);
            $item['avg_time'] = $item['ga:avgSessionDuration'];
                unset($item['ga:avgSessionDuration']);
            $item['bounce_rate'] = $item['ga:bounceRate'];
                unset($item['ga:bounceRate']);
        });
        // do the thing
        foreach ($data as $row) {
            $model = new GoogleAnalyticsData();
            $model->attributes = $row;
            $model->save();
        }
        return $data;
    }
    
    public function updateAggregates($start, $end) {
        $result = Yii::$app->db->createCommand("
            INSERT INTO ga_analytics_aggregates
            SELECT 
                property_id,
                date_recorded, 
                SUM(pageviews) AS pageviews, 
                SUM(unique_pageviews) AS visitors, 
                SUM(entrances) AS entrances, 
                AVG(avg_time) AS avg_time, 
                IFNULL(SUM(bounce_rate * entrances)/SUM(entrances), 0) AS bounce_rate
            FROM ga_analytics 
            WHERE property_id = :pid
                AND date_recorded BETWEEN ':start' AND ':end'
            GROUP BY property_id, date_recorded;")
            ->bindValue(':pid', $this->property->id)
            ->bindValue(':start', $start)
            ->bindValue(':end', $end)
            ->queryOne();
        return $result;
    }
    
    public function updateDetails($start, $end) {
        $result = Yii::$app->db->createCommand("
            INSERT INTO ga_analytics_details
            SELECT 
                property_id,
                page,
                SUM(pageviews) as pageviews,
                SUM(unique_pageviews) as visitors,
                SUM(entrances) as entrances,
                AVG(avg_time) as avg_time,
                IFNULL(SUM(bounce_rate * entrances)/SUM(entrances), 0) AS bounce_rate
            FROM ga_analytics 
            WHERE property_id = :pid
                AND date_recorded BETWEEN ':start' AND ':end'
            GROUP BY page, property_id;")
            ->bindValue(':pid', $this->property->id)
            ->bindValue(':start', $start)
            ->bindValue(':end', $end)
            ->queryOne();
        return $result;
    }
}