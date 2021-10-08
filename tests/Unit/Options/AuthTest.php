<?php

declare(strict_types=1);

use RicorocksDigitalAgency\Soap\Request\SoapPhpRequest;

it('can use basic auth')
    ->fake()
    ->soap()->to(EXAMPLE_SOAP_ENDPOINT)
    ->withBasicAuth('hello', 'world')
    ->call('Add', ['intA' => 10, 'intB' => 25])
    ->test()->assertSent(function (SoapPhpRequest $request) {
        return $request->getOptions() == [
                'authentication' => SOAP_AUTHENTICATION_BASIC,
                'login' => 'hello',
                'password' => 'world',
            ];
    });

it('can use digest auth')
    ->fake()
    ->soap()->to(EXAMPLE_SOAP_ENDPOINT)
    ->withDigestAuth('hello', 'world')
    ->call('Add', ['intA' => 10, 'intB' => 25])
    ->test()->assertSent(function (SoapPhpRequest $request) {
        return $request->getOptions() == [
                'authentication' => SOAP_AUTHENTICATION_DIGEST,
                'login' => 'hello',
                'password' => 'world',
            ];
    });
