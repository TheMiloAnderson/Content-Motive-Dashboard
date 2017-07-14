<?php

namespace app\models;

use Google_Client;
use Google_Service_AnalyticsReporting;
use Google_Service_AnalyticsReporting_DateRange;
use Google_Service_AnalyticsReporting_Metric;
use Google_Service_AnalyticsReporting_Dimension;
use Google_Service_AnalyticsReporting_DimensionFilter;
use Google_Service_AnalyticsReporting_DimensionFilterClause;
use Google_Service_AnalyticsReporting_ReportRequest;
use Google_Service_AnalyticsReporting_GetReportsRequest;

use app\models\GaAnalyticsData;

class GaAnalyticsLive {
    
    // this is set up to handle one analytics API request at a time
    // within a request, the url/hostname/poperty_id is the same
    // 
    // api requests also must specify date range 
    // this takes it one day at a time 
    // doesn't have to be set up like this, but it's simpler
    // requests are smaller, but there are more of them
    
    private $analytics;
    public $hostname;
    public $ga_view;
    public $date;

    private function initializeAnalytics() {

        // Use the developers console and download your service account
        // credentials in JSON format. Place them in this directory or
        // change the key file location if necessary.
        $KEY_FILE_LOCATION = __DIR__ . '/Content Motive Dashboard-d1daf489cb9f.json';

        // Create and configure a new client object.
        $client = new Google_Client();
        $client->setApplicationName('Hello Analytics Reporting');
        $client->setAuthConfig($KEY_FILE_LOCATION);
        $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
        $analytics = new Google_Service_AnalyticsReporting($client);

        $this->analytics = $analytics;
    }
    
    public function getReport() {
        
        $this->initializeAnalytics();
        
        // Create the DateRange object.
        $startDate = new \DateTime($this->date);
        $dateRange = new Google_Service_AnalyticsReporting_DateRange();
        $dateRange->setStartDate($startDate->format('Y-m-d'));
        $endDate = $startDate->modify('+1 day');
        $dateRange->setEndDate($endDate->format('Y-m-d'));

        // Create the Metrics objects
        $pageviews = new Google_Service_AnalyticsReporting_Metric();
        $pageviews->setExpression('ga:pageviews');

        $uniquePageviews = new Google_Service_AnalyticsReporting_Metric();
        $uniquePageviews->setExpression('ga:uniquePageviews');

        $entrances = new Google_Service_AnalyticsReporting_Metric();
        $entrances->setExpression('ga:entrances');

        $avgSessionDuration = new Google_Service_AnalyticsReporting_Metric();
        $avgSessionDuration->setExpression('ga:avgSessionDuration');

        $bounceRate = new Google_Service_AnalyticsReporting_Metric();
        $bounceRate->setExpression('ga:bounceRate');

        // Create the Dimensions objects
        $pagepath = new Google_Service_AnalyticsReporting_Dimension();
        $pagepath->setName('ga:pagePath');

        $hostname = new Google_Service_AnalyticsReporting_Dimension();
        $hostname->setName('ga:hostname');

        $dimensionFilter = new Google_Service_AnalyticsReporting_DimensionFilter();
        $dimensionFilter->setDimensionName('ga:pagePath');
        $dimensionFilter->setExpressions('/op-*');
        $dimensionFilter2 = new Google_Service_AnalyticsReporting_DimensionFilter();
        $dimensionFilter2->setDimensionName('ga:hostname');
        $dimensionFilter2->setOperator('EXACT');
        $dimensionFilter2->setExpressions($this->hostname);
        $dimensionFilterClause = new Google_Service_AnalyticsReporting_DimensionFilterClause();
        $dimensionFilterClause->setFilters(array($dimensionFilter));
        
        // Create the ReportRequest object.
        $request = new Google_Service_AnalyticsReporting_ReportRequest();
        $request->setViewId($this->ga_view);
        $request->setDateRanges($dateRange);
        $request->setDimensions(array($hostname, $pagepath));
        $request->setDimensionFilterClauses(array($dimensionFilterClause));
        $request->setMetrics(array($pageviews, $uniquePageviews, $entrances, $avgSessionDuration, $bounceRate));

        $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
        $body->setReportRequests(array($request));
        $report = $this->analytics->reports->batchGet($body);
        return $this->formatAnalyticsData($report[0]);
    }
    
    private function formatAnalyticsData($report) {
        
        // get foreign key from GaProperties, for loop below
        $property = GaProperties::find()->where(['url' => $this->hostname])->one();
        
        // get labels and data out of Google object, put in simple array
        $header = $report->getColumnHeader();
        $dimensionHeaders = $header->getDimensions();
        $metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
        $headers = $dimensionHeaders;
        foreach ($metricHeaders as $metricHeader) {
            $headers[] = $metricHeader['name'];
        }
        $rows = $report->getData()->getRows();
        for ($rowIndex = 0; $rowIndex < count($rows); $rowIndex++) {
            $row = $rows[$rowIndex];
            $dimensions = $row->getDimensions();
            $metrics = $row->getMetrics(); 
            foreach ($metrics as $metric) {
                $values = array_merge($dimensions, $metric['values']);
                $data = array_combine($headers, $values);
                $data['date_recorded'] = $this->date;
                $data['property_id'] = $property->id;
                $dataArray[] = $data;
            }
        }
        return $dataArray;
    }
    
    public function saveAnalytics($data) {
        
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
            $model = new GaAnalyticsData();
            $model->attributes = $row;
            $model->save();
        }
        return $data;
    }
}