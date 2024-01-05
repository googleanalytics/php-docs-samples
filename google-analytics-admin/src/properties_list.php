<?php

/**
 * Copyright 2024 Google LLC.
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
 * Google Analytics Admin API sample application which prints Google Analytics 4
 * properties under the specified parent account that are available to the
 * current user.
 *
 * See
 * https://developers.google.com/analytics/devguides/config/admin/v1/rest/v1alpha/properties/list
 * for more information.
 */

namespace Google\Analytics\Admin\Samples;

// [START analyticsadmin_properties_list]
use Google\Analytics\Admin\V1beta\Client\AnalyticsAdminServiceClient;
use Google\Analytics\Admin\V1beta\ListPropertiesRequest;

/**
 * @param string $accountId Your GA-4 Account ID
 */
function properties_list(string $accountId)
{
    // Create an instance of the Google Analytics Admin API client library.
    $client = new AnalyticsAdminServiceClient();

    // Make an API call.
    $request = (new ListPropertiesRequest())
        ->setFilter('parent:accounts/' . $accountId)
        ->setShowDeleted(true);
    $response = $client->listProperties($request);

    print 'Result:' . PHP_EOL;
    $i = 0;
    foreach($response->iterateAllElements() as $property) {
        printf('Property #%d resource name: %s, parent: %s, display name: "%s", currency: %s, time zone: %s, create time: %s, update time: %s%s',
            $i++,
            $property->getName(),
            $property->getParent(),
            $property->getDisplayName(),
            $property->getCurrencyCode(),
            $property->getTimeZone(),
            $property->getCreateTime()->getSeconds(),
            $property->getUpdateTime()->getSeconds(),
            PHP_EOL,
        );
    }
}
// [END analyticsadmin_properties_list]

// The following 2 lines are only needed to run the samples
require_once __DIR__ . '/../testing/sample_helpers.php';
return \Google\Analytics\Admin\Samples\execute_sample(__FILE__, __NAMESPACE__, $argv);
