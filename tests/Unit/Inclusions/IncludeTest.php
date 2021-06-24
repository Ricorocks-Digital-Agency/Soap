<?php

use RicorocksDigitalAgency\Soap\Parameters\Builder;
use RicorocksDigitalAgency\Soap\Request\SoapClientRequest;
use Mockery as m;

it('can include an array at the root without using for', function() {
    $mock = m::mock(Builder::class);
    $mock->shouldReceive('handle')
        ->once()
        ->withArgs(fn($parameters) => $parameters == ['intA' => 10, 'intB' => 25]);

    $soap = soap(null, new SoapClientRequest($mock));
    $soap->fake();

    $soap->include(['intA' => 10]);
    $soap->to(EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intB' => 25]));
});

it('can include an array at the root when specified using the include method', function() {
    $mock = m::mock(Builder::class);
    $mock->shouldReceive('handle')
        ->once()
        ->withArgs(fn($parameters) => $parameters == ['intA' => 10, 'intB' => 25]);

    $soap = soap(null, new SoapClientRequest($mock));
    $soap->fake();

    $soap->include(['intA' => 10])->for(EXAMPLE_SOAP_ENDPOINT);
    $soap->to(EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intB' => 25]));
});

it('can include a node at the root when specified using the include method', function() {
    $mock = m::mock(Builder::class);

    $soap = soap(null, new SoapClientRequest($mock));

    $mock->shouldReceive('handle')
        ->once()
        ->withArgs(fn($parameters) => $parameters ==
            [
                'intA' => 10,
                'intB' => 25,
                'foo' => $soap->node(['foo' => 'bar']),
            ]
        );

    $soap->fake();

    $soap->include(['foo' => $soap->node(['foo' => 'bar'])])->for(EXAMPLE_SOAP_ENDPOINT);
    $soap->to(EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intA' => 10, 'intB' => 25]));
});

it('only includes the inclusion if the method name matches', function() {
    $mock = m::mock(Builder::class);
    $mock->shouldReceive('handle')
        ->once()
        ->withArgs(fn($parameters) => $parameters == [
            'intA' => 10,
            'intB' => 25,
        ]);

    $soap = soap(null, new SoapClientRequest($mock));
    $soap->fake();

    $soap->include(['foo' => $soap->node(['foo' => 'bar'])])->for(EXAMPLE_SOAP_ENDPOINT, 'Bar');
    $soap->to(EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intA' => 10, 'intB' => 25]));
});

it('allows inclusions to permeate further down the XML DOM using dot syntax', function() {
    $soap = soap();
    $soap->fake();
    $soap->include(['foo.bar' => 'Hello World'])->for(EXAMPLE_SOAP_ENDPOINT, 'Bar');
    $soap->to(EXAMPLE_SOAP_ENDPOINT)->call('Bar', (['foo' => ['baz' => 'cool']]));

    $soap->assertSent(fn ($request) => $request->getBody() == ['foo' => ['baz' => 'cool', 'bar' => 'Hello World']]);
});
