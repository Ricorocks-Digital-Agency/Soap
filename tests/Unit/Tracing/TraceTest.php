<?php

use RicorocksDigitalAgency\Soap\Support\Tracing\Trace;

it('has a static client method', function () {
    $trace = Trace::client($client = new SoapClient(EXAMPLE_SOAP_ENDPOINT));

    expect($client)
        ->__getLastRequest()->toBe($trace->xmlRequest)
        ->__getLastResponse()->toBe($trace->xmlResponse)
        ->__getLastRequestHeaders()->toBe($trace->requestHeaders)
        ->__getLastResponseHeaders()->toBe($trace->responseHeaders);
})->skip('This makes a real API call to retrieve the WSDL');

it('returns gracefully', function () {
    expect(new Trace())
        ->client->toBeNull()
        ->xmlRequest->toBeNull()
        ->xmlResponse->toBeNull()
        ->requestHeaders->toBeNull()
        ->responseHeaders->toBeNull();
});
