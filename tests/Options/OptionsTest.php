<?php

use RicorocksDigitalAgency\Soap\Facades\Soap;
use RicorocksDigitalAgency\Soap\Request\SoapClientRequest;
use RicorocksDigitalAgency\Soap\Tests\TestCase;

it('can set options', function() {
    $soap = soap();
    $soap->fake();

    $soap->to(EXAMPLE_SOAP_ENDPOINT)
        ->withOptions(['compression' => SOAP_COMPRESSION_GZIP])
        ->call('Add', ['intA' => 10, 'intB' => 25]);

    $soap->assertSent(
        function (SoapClientRequest $request) {
            return $request->getOptions() == [
                    'compression' => SOAP_COMPRESSION_GZIP,
                ];
        }
    );
});

it('merges with other options', function() {
    $soap = soap();
    $soap->fake();

    $soap->to(EXAMPLE_SOAP_ENDPOINT)
        ->withBasicAuth('foo', 'bar')
        ->withOptions(['compression' => SOAP_COMPRESSION_GZIP])
        ->call('Add', ['intA' => 10, 'intB' => 25]);

    $soap->assertSent(
        function (SoapClientRequest $request, $response) {
            return $request->getOptions() == [
                    'authentication' => SOAP_AUTHENTICATION_BASIC,
                    'login' => 'foo',
                    'password' => 'bar',
                    'compression' => SOAP_COMPRESSION_GZIP,
                ];
        }
    );
});

it('overrides previous values on conflict', function() {
    $soap = soap();
    $soap->fake();

    $soap->to(EXAMPLE_SOAP_ENDPOINT)
        ->withOptions(['compression' => SOAP_COMPRESSION_ACCEPT])
        ->withOptions(['compression' => SOAP_COMPRESSION_GZIP])
        ->call('Add', ['intA' => 10, 'intB' => 25]);

    $soap->assertSent(
        function (SoapClientRequest $request, $response) {
            return $request->getOptions() == [
                    'compression' => SOAP_COMPRESSION_GZIP,
                ];
        }
    );
});
