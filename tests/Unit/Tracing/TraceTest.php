<?php

use RicorocksDigitalAgency\Soap\Support\DecoratedClient;
use RicorocksDigitalAgency\Soap\Support\Tracing\Trace;

it('has a static client method', function () {
    $trace = Trace::client($client = new DecoratedClient(new SoapClient(EXAMPLE_SOAP_ENDPOINT)));

    expect($client)
        ->lastRequestAsXml()->toBe($trace->xmlRequest)
        ->lastResponseAsXml()->toBe($trace->xmlResponse)
        ->lastRequestHeaders()->toBe($trace->requestHeaders)
        ->lastResponseHeaders()->toBe($trace->responseHeaders);
})->group('integration');

it('returns gracefully')
    ->expect(new Trace())
    ->client->toBeNull()
    ->xmlRequest->toBeNull()
    ->xmlResponse->toBeNull()
    ->requestHeaders->toBeNull()
    ->responseHeaders->toBeNull();
