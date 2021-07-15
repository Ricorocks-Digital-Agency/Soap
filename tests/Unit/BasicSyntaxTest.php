<?php

use Mockery as m;
use RicorocksDigitalAgency\Soap\Request\Request;

it('can obtain a WSDL', function () {
    $mock = m::mock(Request::class);
    $mock->shouldReceive('beforeRequesting', 'afterRequesting', 'to')->andReturnSelf()
        ->shouldReceive('functions')->andReturn(
            [
                'AddResponse Add(Add $parameters)',
                'SubtractResponse Subtract(Subtract $parameters)',
                'MultiplyResponse Multiply(Multiply $parameters)',
                'DivideResponse Divide(Divide $parameters)',
                'AddResponse Add(Add $parameters)',
                'SubtractResponse Subtract(Subtract $parameters)',
                'MultiplyResponse Multiply(Multiply $parameters)',
                'DivideResponse Divide(Divide $parameters)',
            ]
        );

    $soap = soap(null, $mock);

    expect($soap->to(EXAMPLE_SOAP_ENDPOINT)->functions())
        ->toBeArray()
        ->not->toBeEmpty();
});

it('can call a SOAP function', function () {})
    ->fakeRequest(35)
    ->expect(fn() => $this->soap()->to(EXAMPLE_SOAP_ENDPOINT)->Add(['intA' => 10, 'intB' => 25])->AddResult)
    ->toEqual(35);

it('can use nodes', function () {})
    ->fakeRequest(35)
    ->defer(fn ($instance) => expect($instance->soap()->to(EXAMPLE_SOAP_ENDPOINT)->call('Add', soap()->node()->body(['intA' => 10, 'intB' => 25]))->AddResult))
    ->toEqual(35);

it('can forward method calls', function () {})
    ->fakeRequest(35)
    ->expect(fn() => $this->soap()->to(EXAMPLE_SOAP_ENDPOINT)->Add(['intA' => 10, 'intB' => 25])->AddResult)
    ->toEqual(35);

it('works with a soapable', function () {})
    ->fakeRequest(35)
    ->defer(fn ($instance) => expect($instance->soap()->to(EXAMPLE_SOAP_ENDPOINT)->Add(new ExampleSoapable())->AddResult))
    ->toEqual(35);
