<?php

/**
 * Copyright 2022 Google LLC.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * Google Analytics Data API sample application demonstrating the use of
 * pagination to retrieve large result sets.
 * See https://developers.google.com/analytics/devguides/reporting/data/v1/rest/v1beta/properties/runReport#body.request_body.FIELDS.offset
 * for more information.
 * Usage:
 *   composer update
 *   php run_report_with_pagination.php YOUR-GA4-PROPERTY-ID
 */

namespace Google\Analytics\Data\Samples;

// [START analyticsdata_run_report_with_pagination]
use Google\Analytics\Data\V1beta\Client\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\MetricType;
use Google\Analytics\Data\V1beta\RunReportRequest;
use Google\Analytics\Data\V1beta\RunReportResponse;

/**
 * Runs a report several times, each time retrieving a portion of result
 * using pagination.
 * @param string $propertyId Your GA-4 Property ID
 */
function run_report_with_pagination(string $propertyId)
{
    // Create an instance of the Google Analytics Data API client library.
    $client = new BetaAnalyticsDataClient();

    // [START analyticsdata_run_report_with_pagination_page1]
    // Make an API call.
    $request = (new RunReportRequest())
        ->setProperty('properties/' . $propertyId)
        ->setDateRanges([
            new DateRange([
                'start_date' => '350daysAgo',
                'end_date' => 'yesterday',
            ])
        ])
        ->setDimensions([
            new Dimension(['name' => 'firstUserSource']),
            new Dimension(['name' => 'firstUserMedium']),
            new Dimension(['name' => 'firstUserCampaignName']),
        ])
        ->setMetrics([
            new Metric(['name' => 'sessions']),
            new Metric(['name' => 'keyEvents']),
            new Metric(['name' => 'totalRevenue']),
        ])
        ->setLimit(100000)
        ->setOffset(0);

    $requestCount = 1;
    printf('Sending request #%d' . PHP_EOL, $requestCount);

    $response = $client->runReport($request);
    # [END analyticsdata_run_report_with_pagination_page1]

    printRunReportResponseWithPagination($response, $requestCount);

    // [START analyticsdata_run_report_with_pagination_page2]
    $rowsReceived = count($response->getRows());
    $totalRows = $response->getRowCount();

    // Run the same report with an increased offset value to retrieve each additional
    // page until all rows are received.
    while ($rowsReceived < $totalRows) {
        $request = $request->setOffset($rowsReceived);
        $requestCount++;
        printf('Sending request #%d' . PHP_EOL, $requestCount);

        $response = $client->runReport($request);
        $rowsReceived += count($response->getRows());
        printRunReportResponseWithPagination($response, $requestCount);
    }
    // [END analyticsdata_run_report_with_pagination_page2]
}

/**
 * Print results of a runReport call.
 * @param RunReportResponse $response
 * @param int $requestCount
 */
function printRunReportResponseWithPagination(RunReportResponse $response, int $requestCount)
{
    // [START analyticsdata_print_run_report_response_header]
    printf('%s rows received for request #%d%s', count($response->getRows()), $requestCount, PHP_EOL);
    foreach ($response->getDimensionHeaders() as $dimensionHeader) {
        printf('Dimension header name: %s%s', $dimensionHeader->getName(), PHP_EOL);
    }
    foreach ($response->getMetricHeaders() as $metricHeader) {
        printf(
            'Metric header name: %s (%s)' . PHP_EOL,
            $metricHeader->getName(),
            MetricType::name($metricHeader->getType())
        );
    }
    // [END analyticsdata_print_run_report_response_header]

    // [START analyticsdata_print_run_report_response_rows]
    print 'Report result: ' . PHP_EOL;

    foreach ($response->getRows() as $row) {
        printf(
            '%s %s' . PHP_EOL,
            $row->getDimensionValues()[0]->getValue(),
            $row->getMetricValues()[0]->getValue()
        );
    }
    // [END analyticsdata_print_run_report_response_rows]
}
// [END analyticsdata_run_report_with_pagination]

// The following 2 lines are only needed to run the samples
require_once __DIR__ . '/../testing/sample_helpers.php';
return \Google\Analytics\Data\Samples\execute_sample(__FILE__, __NAMESPACE__, $argv);
