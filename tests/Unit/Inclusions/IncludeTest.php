<?php

declare(strict_types=1);

use Mockery as m;
use RicorocksDigitalAgency\Soap\Contracts\Builder;

it('can include an array at the root without using for', function () {
    $mock = m::mock(Builder::class);
    $mock->shouldReceive('handle')
        ->once()
        ->withArgs(fn ($parameters) => $parameters == ['intA' => 10, 'intB' => 25]);

    $this->soap(null, soapRequest($mock))->fake();

    $this->soap()->include(['intA' => 10]);
    $this->soap()->to(EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intB' => 25]));
});

it('can include an array at the root when specified using the include method', function () {
    $mock = m::mock(Builder::class);
    $mock->shouldReceive('handle')
        ->once()
        ->withArgs(fn ($parameters) => $parameters == ['intA' => 10, 'intB' => 25]);

    $this->soap(null, soapRequest($mock))->fake();

    $this->soap()->include(['intA' => 10])->for(EXAMPLE_SOAP_ENDPOINT);
    $this->soap()->to(EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intB' => 25]));
});

it('can include a node at the root when specified using the include method', function () {
    $this->soap(null, soapRequest($mock = m::mock(Builder::class)));

    $mock->shouldReceive('handle')
        ->once()
        ->withArgs(fn ($parameters) => $parameters ==
            [
                'intA' => 10,
                'intB' => 25,
                'foo' => $this->soap()->node(['foo' => 'bar']),
            ]
        );

    $this->soap()->fake();

    $this->soap()->include(['foo' => $this->soap()->node(['foo' => 'bar'])])->for(EXAMPLE_SOAP_ENDPOINT);
    $this->soap()->to(EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intA' => 10, 'intB' => 25]));
});

it('only includes the inclusion if the method name matches', function () {
    $mock = m::mock(Builder::class);
    $mock->shouldReceive('handle')
        ->once()
        ->withArgs(fn ($parameters) => $parameters == [
            'intA' => 10,
            'intB' => 25,
        ]);

    $this->soap(null, soapRequest($mock))->fake();

    $this->soap()->include(['foo' => $this->soap()->node(['foo' => 'bar'])])->for(EXAMPLE_SOAP_ENDPOINT, 'Bar');
    $this->soap()->to(EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intA' => 10, 'intB' => 25]));
});

it('allows inclusions to permeate further down the XML DOM using dot syntax', function () {
    $this->soap()->fake();
    $this->soap()->include(['foo.bar' => 'Hello World'])->for(EXAMPLE_SOAP_ENDPOINT, 'Bar');
    $this->soap()->to(EXAMPLE_SOAP_ENDPOINT)->call('Bar', (['foo' => ['baz' => 'cool']]));

    $this->soap()->assertSent(fn ($request) => $request->getBody() == ['foo' => ['baz' => 'cool', 'bar' => 'Hello World']]);
});
