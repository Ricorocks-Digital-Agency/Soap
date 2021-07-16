<?php

use RicorocksDigitalAgency\Soap\Facades\Soap;

it('can create a header with the helper method', function () {
    Soap::fake();

    Soap::to(EXAMPLE_SOAP_ENDPOINT)
        ->withHeaders(soap_header('Auth', 'test.com', ['foo' => 'bar']))
        ->call('Add', ['intA' => 10, 'intB' => 25]);

    Soap::assertSent(fn ($request) => $request->getHeaders() == [
        Soap::header('Auth', 'test.com')->data(['foo' => 'bar']),
    ]);
});

it('will pass a provided actor to the php soap header', function () {
    app()->afterResolving(SoapHeader::class, function ($header) {
        $this->assertEquals('test.com', $header->actor);
    });

    Soap::to(EXAMPLE_SOAP_ENDPOINT)
        ->withHeaders(Soap::header('Auth', 'test.com')->actor('test.com'))
        ->withHeaders(soap_header('Brand', 'test.com', ['hi'], false, 'test.com'))
        ->withHeaders(soap_header('Service', 'bar.com')->actor('test.com'))
        ->call('Add', ['intA' => 10, 'intB' => 25]);
})->skip('This makes a real API call to assert the correct header construction');

it('will send the SOAP_ACTOR_NONE constant if no actor is provided', function () {
    app()->afterResolving(SoapHeader::class, function ($header) {
        $this->assertEquals(SOAP_ACTOR_NONE, $header->actor);
    });

    Soap::to(EXAMPLE_SOAP_ENDPOINT)
        ->withHeaders(Soap::header('Auth', 'test.com')->actor(null))
        ->withHeaders(soap_header('Brand', 'test.com', null, false, null))
        ->withHeaders(soap_header('Service', 'bar.com'))
        ->call('Add', ['intA' => 10, 'intB' => 25]);
})->skip('This makes a real API call to assert the correct header construction');
