<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

// pest()->extend(Tests\TestCase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('_toBeBody', function ($body) {

    $base = <<<XML
        <?xml version="1.0" encoding="utf-16"?>
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:api="http://api.merchant.tlc.com/">
            <soapenv:Header/>
            <soapenv:Body>
                {$body}
            </soapenv:Body>
        </soapenv:Envelope>
        XML;

    return $this->toBe($base);

});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function buildFakeResponse($body): string {

    $data = <<<XML
        <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Body>
            {$body}
        </soap:Body>
    </soap:Envelope>
    XML;

    return $data;
}

function getPushTransactionResponse(): string
{
    $data = <<<XML
        <ns2:Push xmlns:ns2="http://api.merchant.tlc.com/">
            <result>
                <description>description</description>
                <referenceid>12345678</referenceid>
                <status>111</status>
                <transid>tag</transid>
            </result>
        </ns2:Push>
    XML;

    return buildFakeResponse($data);
}

function getPushWithPendingSuccessResponse(): string
{
    $data = <<<XML
        <ns2:PushWithPendingResponse xmlns:ns2="http://api.merchant.tlc.com/">
            <result>
                <description>description</description>
                <referenceid>12345678</referenceid>
                <status>111</status>
                <transid>tag</transid>
            </result>
        </ns2:PushWithPendingResponse>
    XML;

    return buildFakeResponse($data);
}

function getPushWithPendingResponse(): string
{
    $data = <<<XML
        <ns2:PushWithPendingResponse xmlns:ns2="http://api.merchant.tlc.com/">
            <result>
                <description>pending</description>
                <status>100</status>
            </result>
        </ns2:PushWithPendingResponse>
    XML;

    return buildFakeResponse($data);
}

function getResponseError(): string {
    return <<<XML
        <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                <soap:Fault>
                    <faultcode>soap:Server</faultcode>
                    <faultstring>For input string</faultstring>
                    <detail>
                        <ns1:Exception xmlns:ns1="http://api.merchant.tlc.com/"/>
                    </detail>
                </soap:Fault>
            </soap:Body>
        </soap:Envelope>
    XML;

}