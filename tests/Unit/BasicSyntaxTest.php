<?php

use Mockery as m;
use RicorocksDigitalAgency\Soap\Request\Request;
use RicorocksDigitalAgency\Soap\Response\Response;

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

    expect(soap(null, $mock)->to(EXAMPLE_SOAP_ENDPOINT)->functions())
        ->toBeArray()
        ->not->toBeEmpty();
});

it('can call a SOAP function')
    ->fake(['*' => Response::new(['AddResult' => 35])])
    ->expect(fn () => $this->soap()->to(EXAMPLE_SOAP_ENDPOINT)->Add(['intA' => 10, 'intB' => 25])->AddResult)
    ->toEqual(35);

it('can use nodes')
    ->fake(['*' => Response::new(['AddResult' => 35])])
    ->expect(fn () => $this->soap()->to(EXAMPLE_SOAP_ENDPOINT)->call('Add', soap()->node()->body(['intA' => 10, 'intB' => 25]))->AddResult)
    ->toEqual(35);

it('can forward method calls')
    ->fake(['*' => Response::new(['AddResult' => 35])])
    ->expect(fn () => $this->soap()->to(EXAMPLE_SOAP_ENDPOINT)->Add(['intA' => 10, 'intB' => 25])->AddResult)
    ->toEqual(35);

it('works with a soapable')
    ->fake(['*' => Response::new(['AddResult' => 35])])
    ->expect(fn () => $this->soap()->to(EXAMPLE_SOAP_ENDPOINT)->Add(new ExampleSoapable())->AddResult)
    ->toEqual(35);
