<?php

/**
 * Copyright 2017 Intacct Corporation.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"). You may not
 * use this file except in compliance with the License. You may obtain a copy
 * of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * or in the "LICENSE" file accompanying this file. This file is distributed on
 * an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */

namespace Intacct;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use Intacct\Functions\Company\ApiSessionCreate;

/**
 * @coversDefaultClass \Intacct\OfflineClient
 */
class OfflineClientTest extends \PHPUnit\Framework\TestCase
{

    public function testExecute()
    {
        $xml = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<response>
      <acknowledgement>
            <status>success</status>
      </acknowledgement>
      <control>
            <status>success</status>
            <senderid>testsenderid</senderid>
            <controlid>requestUnitTest</controlid>
            <uniqueid>false</uniqueid>
            <dtdversion>3.0</dtdversion>
      </control>
</response>
EOF;
        $headers = [
            'Content-Type' => 'text/xml; encoding="UTF-8"',
        ];
        $mockResponse = new Response(200, $headers, $xml);
        $mock = new MockHandler([
            $mockResponse,
        ]);

        $clientConfig = new ClientConfig();
        $clientConfig->setSenderId('testsender');
        $clientConfig->setSenderPassword('testsendpass');
        $clientConfig->setSessionId('testsession..');
        $clientConfig->setMockHandler($mock);

        $requestConfig = new RequestConfig();
        $requestConfig->setPolicyId('asyncPolicyId');

        $content = [
            new ApiSessionCreate('func1UnitTest')
        ];

        $client = new OfflineClient($clientConfig);

        $response = $client->execute($content, $requestConfig);

        $this->assertEquals('requestUnitTest', $response->getControl()->getControlId());
    }
}