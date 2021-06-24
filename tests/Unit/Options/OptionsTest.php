<?php

use RicorocksDigitalAgency\Soap\Request\SoapClientRequest;

it('can set options', function () {
    $this->soap()->fake();

    $this->soap()->to(EXAMPLE_SOAP_ENDPOINT)
        ->withOptions(['compression' => SOAP_COMPRESSION_GZIP])
        ->call('Add', ['intA' => 10, 'intB' => 25]);

    $this->soap()->assertSent(
        function (SoapClientRequest $request) {
            return $request->getOptions() == [
                    'compression' => SOAP_COMPRESSION_GZIP,
                ];
        }
    );
});

it('merges with other options', function () {
    $this->soap()->fake();

    $this->soap()->to(EXAMPLE_SOAP_ENDPOINT)
        ->withBasicAuth('foo', 'bar')
        ->withOptions(['compression' => SOAP_COMPRESSION_GZIP])
        ->call('Add', ['intA' => 10, 'intB' => 25]);

    $this->soap()->assertSent(
        function (SoapClientRequest $request) {
            return $request->getOptions() == [
                    'authentication' => SOAP_AUTHENTICATION_BASIC,
                    'login' => 'foo',
                    'password' => 'bar',
                    'compression' => SOAP_COMPRESSION_GZIP,
                ];
        }
    );
});

it('overrides previous values on conflict', function () {
    $this->soap()->fake();

    $this->soap()->to(EXAMPLE_SOAP_ENDPOINT)
        ->withOptions(['compression' => SOAP_COMPRESSION_ACCEPT])
        ->withOptions(['compression' => SOAP_COMPRESSION_GZIP])
        ->call('Add', ['intA' => 10, 'intB' => 25]);

    $this->soap()->assertSent(
        function (SoapClientRequest $request) {
            return $request->getOptions() == [
                    'compression' => SOAP_COMPRESSION_GZIP,
                ];
        }
    );
});
