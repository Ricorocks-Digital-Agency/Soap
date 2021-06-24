<?php

use RicorocksDigitalAgency\Soap\Request\SoapClientRequest;

it('can use basic auth', function () {
    $this->soap()->fake();

    $this->soap()->to(EXAMPLE_SOAP_ENDPOINT)
        ->withBasicAuth('hello', 'world')
        ->call('Add', ['intA' => 10, 'intB' => 25]);

    $this->soap()->assertSent(function (SoapClientRequest $request) {
        return $request->getOptions() == [
                'authentication' => SOAP_AUTHENTICATION_BASIC,
                'login' => 'hello',
                'password' => 'world',
            ];
    });
});

it('can use digest auth', function () {
    $this->soap()->fake();

    $this->soap()->to(EXAMPLE_SOAP_ENDPOINT)
        ->withDigestAuth('hello', 'world')
        ->call('Add', ['intA' => 10, 'intB' => 25]);

    $this->soap()->assertSent(function (SoapClientRequest $request) {
        return $request->getOptions() == [
                'authentication' => SOAP_AUTHENTICATION_DIGEST,
                'login' => 'hello',
                'password' => 'world',
            ];
    });
});
