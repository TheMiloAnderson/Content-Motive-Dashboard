<?php

// Load the Google API PHP Client Library.
require_once '../vendor/autoload.php';

$analytics = initializeAnalytics();
$response = getReport($analytics);
printResults($response);
print_r($response);

/**
 * Initializes an Analytics Reporting API V4 service object.
 *
 * @return An authorized Analytics Reporting API V4 service object.
 */
function initializeAnalytics()
{

  // Use the developers console and download your service account
  // credentials in JSON format. Place them in this directory or
  // change the key file location if necessary.
  $KEY_FILE_LOCATION = __DIR__ . '/Content Motive Dashboard-d1daf489cb9f.json';

  // Create and configure a new client object.
  $client = new Google_Client();
  $client->setApplicationName("Hello Analytics Reporting");
  $client->setAuthConfig($KEY_FILE_LOCATION);
  $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
  $analytics = new Google_Service_AnalyticsReporting($client);

  return $analytics;
}


/**
 * Queries the Analytics Reporting API V4.
 *
 * @param service An authorized Analytics Reporting API V4 service object.
 * @return The Analytics Reporting API V4 response.
 */
function getReport($analytics) {

  // Replace with your view ID, for example XXXX.
  $VIEW_ID = "71893551";

  // Create the DateRange object.
  $dateRange = new Google_Service_AnalyticsReporting_DateRange();
  $dateRange->setStartDate("2015-09-26");
  $dateRange->setEndDate("2015-09-27");

  // Create the Metrics objects
  $pageviews = new Google_Service_AnalyticsReporting_Metric();
  $pageviews->setExpression("ga:pageviews");
  $pageviews->setAlias("Page Views");
  
  $uniquePageviews = new Google_Service_AnalyticsReporting_Metric();
  $uniquePageviews->setExpression("ga:uniquePageviews");
  $uniquePageviews->setAlias("Unique Pageviews");
  
  $entrances = new Google_Service_AnalyticsReporting_Metric();
  $entrances->setExpression("ga:entrances");
  $entrances->setAlias("Entrances");
  
  $avgSessionDuration = new Google_Service_AnalyticsReporting_Metric();
  $avgSessionDuration->setExpression("ga:avgSessionDuration");
  $avgSessionDuration->setAlias("Average Session Duration");
  
  $bounceRate = new Google_Service_AnalyticsReporting_Metric();
  $bounceRate->setExpression('ga:bounceRate');
  $bounceRate->setAlias('Bounce Rate');
  
  // Create the Dimensions objects
  $pagepath = new Google_Service_AnalyticsReporting_Dimension();
  $pagepath->setName("ga:pagePath");
  
  $hostname = new Google_Service_AnalyticsReporting_Dimension();
  $hostname->setName("ga:hostname");
  
  $dimensionFilter = new Google_Service_AnalyticsReporting_DimensionFilter();
  $dimensionFilter->setDimensionName("ga:pagePath");
  $dimensionFilter->setExpressions("/op-*");
  $dimensionFilter2 = new Google_Service_AnalyticsReporting_DimensionFilter();
  $dimensionFilter2->setDimensionName("ga:hostname");
  $dimensionFilter2->setOperator('EXACT');
  $dimensionFilter2->setExpressions("www.magictoyota.com");
  $dimensionFilterClause = new Google_Service_AnalyticsReporting_DimensionFilterClause();
  $dimensionFilterClause->setFilters(array($dimensionFilter));
  
  $ordering = new Google_Service_AnalyticsReporting_OrderBy();
  $ordering->setFieldName("ga:pageviews");
  $ordering->setOrderType("VALUE");   
  $ordering->setSortOrder("DESCENDING");
  

  // Create the ReportRequest object.
  $request = new Google_Service_AnalyticsReporting_ReportRequest();
  $request->setViewId($VIEW_ID);
  $request->setDateRanges($dateRange);
  $request->setDimensions(array($hostname, $pagepath));
  $request->setDimensionFilterClauses(array($dimensionFilterClause));
  $request->setMetrics(array($pageviews, $uniquePageviews, $entrances, $avgSessionDuration, $bounceRate));
  $request->setOrderBys($ordering);

  $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
  $body->setReportRequests( array( $request) );
  return $analytics->reports->batchGet( $body );
}


/**
 * Parses and prints the Analytics Reporting API V4 response.
 *
 * @param An Analytics Reporting API V4 response.
 */
function printResults($reports) {
  print("<table>");
  for ( $reportIndex = 0; $reportIndex < count( $reports ); $reportIndex++ ) {
    $report = $reports[ $reportIndex ];
    $header = $report->getColumnHeader();
    $dimensionHeaders = $header->getDimensions();
    $metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
    $rows = $report->getData()->getRows();

    for ( $rowIndex = 0; $rowIndex < count($rows); $rowIndex++) {
      $row = $rows[ $rowIndex ];
      $dimensions = $row->getDimensions();
      $metrics = $row->getMetrics();
	  print("<tr><td>");
      for ($i = 0; $i < count($dimensionHeaders) && $i < count($dimensions); $i++) {
        print($dimensionHeaders[$i] . ": " . $dimensions[$i] . "</td><td>");
      }

      for ($j = 0; $j < count($metrics); $j++) {
        $values = $metrics[$j]->getValues();
        for ($k = 0; $k < count($values); $k++) {
          $entry = $metricHeaders[$k];
          print($entry->getName() . ": " . $values[$k] . "</td><td>");
        }
      }
	  print("</td></tr>");
    }
  }
  print("</table>");
}

var_dump(openssl_get_cert_locations());