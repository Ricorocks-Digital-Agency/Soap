<?php

declare(strict_types=1);

use RicorocksDigitalAgency\Soap\Request\SoapPhpClientRequest;

it('can set options')
    ->fake()
    ->soap()->to(EXAMPLE_SOAP_ENDPOINT)
    ->withOptions(['compression' => SOAP_COMPRESSION_GZIP])
    ->call('Add', ['intA' => 10, 'intB' => 25])
    ->test()->assertSent(fn (SoapPhpClientRequest $request) => $request->getOptions() == [
        'compression' => SOAP_COMPRESSION_GZIP,
    ]);

it('merges with other options')
    ->fake()
    ->soap()->to(EXAMPLE_SOAP_ENDPOINT)
    ->withBasicAuth('foo', 'bar')
    ->withOptions(['compression' => SOAP_COMPRESSION_GZIP])
    ->call('Add', ['intA' => 10, 'intB' => 25])
    ->test()->assertSent(fn (SoapPhpClientRequest $request) => $request->getOptions() == [
        'authentication' => SOAP_AUTHENTICATION_BASIC,
        'login' => 'foo',
        'password' => 'bar',
        'compression' => SOAP_COMPRESSION_GZIP,
    ]);

it('overrides previous values on conflict')
    ->fake()
    ->soap()->to(EXAMPLE_SOAP_ENDPOINT)
    ->withOptions(['compression' => SOAP_COMPRESSION_ACCEPT])
    ->withOptions(['compression' => SOAP_COMPRESSION_GZIP])
    ->call('Add', ['intA' => 10, 'intB' => 25])
    ->test()->assertSent(fn (SoapPhpClientRequest $request) => $request->getOptions() == [
        'compression' => SOAP_COMPRESSION_GZIP,
    ]);
