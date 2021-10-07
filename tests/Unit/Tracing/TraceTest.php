<?php

declare(strict_types=1);

use RicorocksDigitalAgency\Soap\Support\DecoratedClient;
use RicorocksDigitalAgency\Soap\Support\Tracing\Trace;

it('has a static client method', function () {
    $trace = Trace::client($client = new DecoratedClient(new SoapClient(EXAMPLE_SOAP_ENDPOINT)));

    expect($client)
        ->__getLastRequest()->toBe($trace->xmlRequest)
        ->__getLastResponse()->toBe($trace->xmlResponse)
        ->__getLastRequestHeaders()->toBe($trace->requestHeaders)
        ->__getLastResponseHeaders()->toBe($trace->responseHeaders);
})->group('integration');

it('returns gracefully')
    ->expect(new Trace())
    ->client->toBeNull()
    ->xmlRequest->toBeNull()
    ->xmlResponse->toBeNull()
    ->requestHeaders->toBeNull()
    ->responseHeaders->toBeNull();
