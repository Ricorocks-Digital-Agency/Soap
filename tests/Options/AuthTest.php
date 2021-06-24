<?php

use RicorocksDigitalAgency\Soap\Facades\Soap;
use RicorocksDigitalAgency\Soap\Request\SoapClientRequest;
use RicorocksDigitalAgency\Soap\Tests\TestCase;

it('can use basic auth', function() {
    $soap = soap();
    $soap->fake();

    $soap->to(EXAMPLE_SOAP_ENDPOINT)
        ->withBasicAuth('hello', 'world')
        ->call('Add', ['intA' => 10, 'intB' => 25]);

    $soap->assertSent(function (SoapClientRequest $request) {
        return $request->getOptions() == [
                'authentication' => SOAP_AUTHENTICATION_BASIC,
                'login' => 'hello',
                'password' => 'world',
            ];
    });
});

it('can use digest auth', function() {
    $soap = soap();
    $soap->fake();

    $soap->to(EXAMPLE_SOAP_ENDPOINT)
        ->withDigestAuth('hello', 'world')
        ->call('Add', ['intA' => 10, 'intB' => 25]);

    $soap->assertSent(function (SoapClientRequest $request) {
        return $request->getOptions() == [
                'authentication' => SOAP_AUTHENTICATION_DIGEST,
                'login' => 'hello',
                'password' => 'world',
            ];
    });
});

