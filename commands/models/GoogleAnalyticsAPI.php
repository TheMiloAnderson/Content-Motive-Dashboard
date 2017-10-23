<?php

namespace app\commands\models;

use Yii;
use Google_Client;
use Google_Service_AnalyticsReporting;
use Google_Service_AnalyticsReporting_DateRange;
use Google_Service_AnalyticsReporting_Metric;
use Google_Service_AnalyticsReporting_Dimension;
use Google_Service_AnalyticsReporting_DimensionFilter;
use Google_Service_AnalyticsReporting_DimensionFilterClause;
use Google_Service_AnalyticsReporting_ReportRequest;
use Google_Service_AnalyticsReporting_GetReportsRequest;

use yii\base\Object;

class GoogleAnalyticsAPI extends Object {
    
    private $analytics_connection;
    public $recordDate;
    public $nextRecordDate;
    public $report;
    private $_property;
    protected $requestCount;
    
    public function setProperty($property) {
        $this->_property = $property;
    }
    public function getProperty() {
        return $this->_property;
    }
    
    function __construct($config = []) {

        // Initialize analytics
        $KEY_FILE_LOCATION = Yii::$app->params['GoogleAnalyticsKeyFile'];

        // Create and configure a new client object.
        $client = new Google_Client();
        $client->setApplicationName('Hello Analytics Reporting');
        $client->setAuthConfig($KEY_FILE_LOCATION);
        $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
        $analytics = new Google_Service_AnalyticsReporting($client);

        $this->analytics_connection = $analytics;
        $this->requestCount = 0;
        
        parent::__construct($config);
    }
    
    public function fetchReport() {
        
        // Create the DateRange object.
        $startDate = new \DateTime($this->recordDate);
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
        
        // Create the ReportRequest object.
        $request = new Google_Service_AnalyticsReporting_ReportRequest();
        if ($this->_property->type == 'Content') {
            $dimensionFilter = new Google_Service_AnalyticsReporting_DimensionFilter();
            $dimensionFilter->setDimensionName('ga:pagePath');
            $dimensionFilter->setExpressions('/op-*');
            $dimensionFilterClause = new Google_Service_AnalyticsReporting_DimensionFilterClause();
            $dimensionFilterClause->setFilters(array($dimensionFilter));
            $request->setDimensionFilterClauses(array($dimensionFilterClause));
        }
        $request->setViewId($this->_property->ga_view);
        $request->setDateRanges($dateRange);
        $request->setDimensions(array($hostname, $pagepath));
        if ($this->_property->type == 'Blogs') {
            $uniqueEvents = new Google_Service_AnalyticsReporting_Metric();
            $uniqueEvents->setExpression('ga:totalEvents');
            $request->setMetrics(array($pageviews, $uniquePageviews, $entrances, $avgSessionDuration, $uniqueEvents));
        } else {
            $request->setMetrics(array($pageviews, $uniquePageviews, $entrances, $avgSessionDuration, $bounceRate));
        }
        $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
        $body->setReportRequests(array($request));
        $report = $this->analytics_connection->reports->batchGet($body);

        $this->report = $report;
    }
}