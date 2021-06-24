<?php

use RicorocksDigitalAgency\Soap\Parameters\IntelligentBuilder;
use RicorocksDigitalAgency\Soap\Request\SoapClientRequest;
use RicorocksDigitalAgency\Soap\Tests\Mocks\MockSoapClient;

function traceableSoap()
{
    return soap(null, new SoapClientRequest(
                        new IntelligentBuilder(),
                        new MockSoapClient(EXAMPLE_SOAP_ENDPOINT, ['trace' => true]))
    );
}

it('can request a trace at the time of request', function () {
    expect(traceableSoap()->to(EXAMPLE_SOAP_ENDPOINT)->trace()->call('Add', ['intA' => 10, 'intB' => 25])->trace())
        ->xmlRequest->toEqual('<?xml version="1.0" encoding="UTF-8"?><FooBar><Hello>World</Hello></FooBar>')
        ->xmlResponse->toEqual('<?xml version="1.0" encoding="UTF-8"?><Status>Success!</Status>')
        ->requestHeaders->toEqual('Hello World')
        ->responseHeaders->toEqual('Foo Bar');
});

it('can request traces globally', function () {
    expect(with(traceableSoap()->trace(), fn ($soap) => $soap->to(EXAMPLE_SOAP_ENDPOINT)->call('Add', ['intA' => 10, 'intB' => 25])->trace()))
        ->xmlRequest->toEqual('<?xml version="1.0" encoding="UTF-8"?><FooBar><Hello>World</Hello></FooBar>')
        ->xmlResponse->toEqual('<?xml version="1.0" encoding="UTF-8"?><Status>Success!</Status>')
        ->requestHeaders->toEqual('Hello World')
        ->responseHeaders->toEqual('Foo Bar');
});

it('has no content in the trace be default', function () {
    expect(traceableSoap()->to(EXAMPLE_SOAP_ENDPOINT)->call('Add', ['intA' => 10, 'intB' => 25])->trace())
        ->xmlRequest->toBeEmpty()
        ->xmlResponse->toBeEmpty()
        ->requestHeaders->toBeEmpty()
        ->responseHeaders->toBeEmpty();
});
