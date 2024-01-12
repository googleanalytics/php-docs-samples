<?php
/**
 * Copyright 2023 Google LLC.
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
 * Google Analytics Data API sample application demonstrating the creation
 * of a funnel report.
 * See https://developers.google.com/analytics/devguides/reporting/data/v1/rest/v1alpha/properties/runFunnelReport
 * for more information.
 */

namespace Google\Analytics\Data\Samples;

// [START analyticsdata_run_funnel_report]

// TODO(jradcliff): This client is missing from the library. See:
// https://github.com/googleapis/google-cloud-php/issues/6961.

use Google\Analytics\Data\V1alpha\Client\AlphaAnalyticsDataClient;
use Google\Analytics\Data\V1alpha\DateRange;
use Google\Analytics\Data\V1alpha\Dimension;
use Google\Analytics\Data\V1alpha\FunnelBreakdown;
use Google\Analytics\Data\V1alpha\FunnelEventFilter;
use Google\Analytics\Data\V1alpha\FunnelFieldFilter;
use Google\Analytics\Data\V1alpha\FunnelFilterExpression;
use Google\Analytics\Data\V1alpha\FunnelFilterExpressionList;
use Google\Analytics\Data\V1alpha\FunnelStep;
use Google\Analytics\Data\V1alpha\Funnel;
use Google\Analytics\Data\V1alpha\FunnelSubReport;
use Google\Analytics\Data\V1alpha\RunFunnelReportRequest;
use Google\Analytics\Data\V1alpha\RunFunnelReportResponse;
use Google\Analytics\Data\V1alpha\StringFilter;
use Google\Analytics\Data\V1alpha\StringFilter\MatchType;

/**
 * Runs a funnel query to build a report with 5 funnel steps.
 *
 * Step 1: First open/visit (event name is `first_open` or `first_visit`).
 * Step 2: Organic visitors (`firstUserMedium` dimension contains the term "organic").
 * Step 3: Session start (event name is `session_start`).
 * Step 4: Screen/Page view (event name is `screen_view` or `page_view`).
 * Step 5: Purchase (event name is `purchase` or `in_app_purchase`).
 *
 * The report configuration reproduces the default funnel report provided in the Funnel
 * Exploration template of the Google Analytics UI. See more at
 * https://support.google.com/analytics/answer/9327974
 *
 * @param string $propertyId Your GA-4 Property ID
 */
function run_funnel_report(string $propertyId)
{
    // Create an instance of the Google Analytics Data API client library.
    $client = new AlphaAnalyticsDataClient();


    // Create the funnel report request.
    $request = (new RunFunnelReportRequest())
        ->setProperty('properties/' . $propertyId)
        ->setDateRanges([
            new DateRange([
                'start_date' => '30daysAgo',
                'end_date' => 'today',
            ]),
        ])
        ->setFunnelBreakdown(
            new FunnelBreakdown([
                'breakdown_dimension' =>
                    new Dimension([
                        'name' => 'deviceCategory'
                    ])
            ])
        )
        ->setFunnel(new Funnel());

    // Add funnel steps to the funnel.

    // 1. Add first open/visit step.
    $request->getFunnel()->getSteps()[] = new FunnelStep([
        'name' => 'First open/visit',
        'filter_expression' => new FunnelFilterExpression([
            'or_group' => new FunnelFilterExpressionList([
                'expressions' => [
                    new FunnelFilterExpression([
                        'funnel_event_filter' => new FunnelEventFilter([
                            'event_name' => 'first_open',
                        ])
                    ]),
                    new FunnelFilterExpression([
                        'funnel_event_filter' => new FunnelEventFilter([
                            'event_name' => 'first_visit'
                        ])
                    ])
                ]
            ])
        ])
    ]);
 
    // 2. Add organic visitors step.
    $request->getFunnel()->getSteps()[] = new FunnelStep([
        'name' => 'Organic visitors',
        'filter_expression' => new FunnelFilterExpression([
            'funnel_field_filter' => new FunnelFieldFilter([
                'field_name' => 'firstUserMedium',
                'string_filter' => new StringFilter([
                    'match_type' => MatchType::CONTAINS,
                    'case_sensitive' => false,
                    'value' => 'organic',
                ])
            ])
        ])
    ]);

    // 3. Add session start step.
    $request->getFunnel()->getSteps()[] = new FunnelStep([
        'name' => 'Session start',
        'filter_expression' => new FunnelFilterExpression([
            'funnel_event_filter' => new FunnelEventFilter([
                'event_name' => 'session_start',
            ])
        ])
    ]);

    // 4. Add screen/page view step.
    $request->getFunnel()->getSteps()[] = new FunnelStep([
        'name' => 'Screen/Page view',
        'filter_expression' => new FunnelFilterExpression([
            'or_group' => new FunnelFilterExpressionList([
                'expressions' => [
                    new FunnelFilterExpression([
                        'funnel_event_filter' => new FunnelEventFilter([
                            'event_name' => 'screen_view',
                        ])
                    ]),
                    new FunnelFilterExpression([
                        'funnel_event_filter' => new FunnelEventFilter([
                            'event_name' => 'page_view'
                        ])
                    ])
                ]
            ])
        ])
    ]);

    // 5. Add purchase step.
    $request->getFunnel()->getSteps()[] = new FunnelStep([
        'name' => 'Purchase',
        'filter_expression' => new FunnelFilterExpression([
            'or_group' => new FunnelFilterExpressionList([
                'expressions' => [
                    new FunnelFilterExpression([
                        'funnel_event_filter' => new FunnelEventFilter([
                            'event_name' => 'purchase',
                        ])
                    ]),
                    new FunnelFilterExpression([
                        'funnel_event_filter' => new FunnelEventFilter([
                            'event_name' => 'in_app_purchase'
                        ])
                    ])
                ]
            ])
        ])
    ]);

    // Make an API call.
    $response = $client->runFunnelReport($request);

    printRunFunnelReportResponse($response);
}

// [START analyticsdata_print_run_funnel_report_response]
/**
 * Print results of a runFunnelReport call.
 * @param RunFunnelReportResponse $response
 */
function printRunFunnelReportResponse(RunFunnelReportResponse $response)
{
    print 'Report result: ';
    print '=== FUNNEL VISUALIZATION ===';
    printFunnelSubReport($response->getFunnelVisualization());

    print '=== FUNNEL TABLE ===';
    printFunnelSubReport($response->getFunnelTable());
}

/**
 * Print the contents of a FunnelSubReport object.
 * @param FunnelSubReport $subReport
 */
function printFunnelSubReport(FunnelSubReport $subReport)
{
    print 'Dimension headers:';
    foreach ($subReport->getDimensionHeaders() as $dimensionHeader) {
        print $dimensionHeader->getName();
    }

    print PHP_EOL . 'Metric headers:';
    foreach ($subReport->getMetricHeaders() as $metricHeader) {
        print $metricHeader->getName();
    }

    print PHP_EOL . 'Dimension and metric values for each row in the report:';
    foreach ($subReport->getRows() as $rowIndex => $row) {
        print 'Row #' . $rowIndex;
        foreach ($row->getDimensionValues() as $dimIndex => $dimValue) {
            $dimName = $subReport->getDimensionHeaders()[$dimIndex]->getName();
            print $dimName . ": '" . $dimValue->getValue() . "'";
        }
        foreach ($row->getMetricValues() as $metricIndex => $metricValue) {
            $metricName = $subReport->getMetricHeaders()[$metricIndex]->getName();
            print $metricName . ": '" . $metricValue->getValue() . "'";
        }
    }

    print PHP_EOL . 'Sampling metadata for each date range:';
    foreach($subReport->getMetadata()->getSamplingMetadatas() as $metadataIndex => $metadata) {
        printf('Sampling metadata for date range #%d: samplesReadCount=%d' .
            'samplingSpaceSize=%d%s',
            $metadataIndex, $metadata->getSamplesReadCount(), $metadata->getSamplingSpaceSize(), PHP_EOL);
    }
}
// [END analyticsdata_print_run_funnel_report_response]
// [END analyticsdata_run_funnel_report]

// The following 2 lines are only needed to run the samples
require_once __DIR__ . '/../testing/sample_helpers.php';
return \Google\Analytics\Data\Samples\execute_sample(__FILE__, __NAMESPACE__, $argv);
