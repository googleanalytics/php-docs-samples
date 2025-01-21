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
 * Google Analytics Admin API sample quickstart application.
 *
 * This application demonstrates the usage of the Analytics Admin API to list
 * all Google Analytics accounts available to the authenticated user, using
 * service account credentials.
 *
 * Before you start the application, please review the comments starting with
 * "TODO(developer)" and update the code to use the correct values.
 *
 * Usage:
 * composer update
 * php quickstart.php
*/

// [START analytics_admin_quickstart]
require 'vendor/autoload.php';

use Google\Analytics\Admin\V1beta\Account;
use Google\Analytics\Admin\V1beta\Client\AnalyticsAdminServiceClient;
use Google\Analytics\Admin\V1beta\ListAccountsRequest;

/**
 * TODO(developer): Replace this variable with your Google Analytics 4
 *   property ID before running the sample.
 */
$property_id = 'YOUR-GA4-PROPERTY-ID';

// Using a default constructor instructs the client to use the credentials
// specified in GOOGLE_APPLICATION_CREDENTIALS environment variable.
// See https://cloud.google.com/docs/authentication/production for more information
// about managing credentials.
$client = new AnalyticsAdminServiceClient();

// Calls listAccounts() method of the Google Analytics Admin API and prints
// the response for each account.
$request = new ListAccountsRequest();
$response = $client->listAccounts($request);

print 'Result:' . PHP_EOL;
foreach ($response->iterateAllElements() as $account) {
    print 'Account name: ' . $account->getName() . PHP_EOL;
    print 'Display name: ' . $account->getDisplayName() . PHP_EOL;
    print 'Country code: ' . $account->getRegionCode() . PHP_EOL;
    print 'Create time: ' . $account->getCreateTime()->getSeconds() . PHP_EOL;
    print 'Update time: ' . $account->getUpdateTime()->getSeconds() . PHP_EOL;
}
// [END analytics_admin_quickstart]
