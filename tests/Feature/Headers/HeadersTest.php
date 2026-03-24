<?php

declare(strict_types=1);

use RicorocksDigitalAgency\Soap\Contracts\Request;
use RicorocksDigitalAgency\Soap\Facades\Soap;
use RicorocksDigitalAgency\Soap\Tests\Mocks\MockSoapClient;

beforeEach(function () {
    $this->client = new MockSoapClient();
    $this->instance(Request::class, soapRequest(null, $this->client));
});

it('can create a header with the helper method', function () {
    Soap::to(EXAMPLE_SOAP_ENDPOINT)
        ->withHeaders(soap_header('Auth', 'test.com', ['foo' => 'bar']))
        ->call('Add', ['intA' => 10, 'intB' => 25]);

    expect($this->client->headers[0])
        ->name->toBe('Auth')
        ->namespace->toBe('test.com')
        ->data->toBe(['foo' => 'bar']);
});

it('will pass a provided actor to the php soap header', function () {
    Soap::to(EXAMPLE_SOAP_ENDPOINT)
        ->withHeaders(Soap::header('Auth', 'test.com')->actor('test.com'))
        ->withHeaders(soap_header('Brand', 'test.com', ['hi'], false, 'test.com'))
        ->withHeaders(soap_header('Service', 'bar.com')->actor('test.com'))
        ->call('Add', ['intA' => 10, 'intB' => 25]);

    expect($this->client->headers)->each(fn ($header) => $header->actor->toBe('test.com'));
});

it('will send the SOAP_ACTOR_NONE constant if no actor is provided', function () {
    Soap::to(EXAMPLE_SOAP_ENDPOINT)
        ->withHeaders(Soap::header('Auth', 'test.com')->actor(null))
        ->withHeaders(soap_header('Brand', 'test.com', null, false, null))
        ->withHeaders(soap_header('Service', 'bar.com'))
        ->call('Add', ['intA' => 10, 'intB' => 25]);

    expect($this->client->headers)->each(fn ($header) => $header->actor->toBe(SOAP_ACTOR_NONE));
});
