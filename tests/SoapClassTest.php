<?php

namespace RicorocksDigitalAgency\Soap\Tests;

use RicorocksDigitalAgency\Soap\Contracts\Soapable;
use RicorocksDigitalAgency\Soap\Facades\Soap;
use RicorocksDigitalAgency\Soap\Request\Request;
use RicorocksDigitalAgency\Soap\Response\Response;

class SoapClassTest extends TestCase
{
    /** @test */
    public function itCanObtainAWsdl()
    {
        $this->mock(Request::class)
            ->shouldReceive('beforeRequesting', 'afterRequesting', 'to')->andReturnSelf()
            ->shouldReceive('functions')
            ->andReturn(
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

        $functions = Soap::to(static::EXAMPLE_SOAP_ENDPOINT)->functions();
        $this->assertIsArray($functions);
        $this->assertNotEmpty($functions);
    }

    /** @test */
    public function itCanCallASOAPFunction()
    {
        Soap::fake(['*' => Response::new(['AddResult' => 35])]);
        $result = Soap::to(static::EXAMPLE_SOAP_ENDPOINT)->call('Add', ['intA' => 10, 'intB' => 25]);
        $this->assertEquals(35, $result->AddResult);
    }

    /** @test */
    public function itCanUseNodes()
    {
        Soap::fake(['*' => Response::new(['AddResult' => 35])]);
        $result = Soap::to(static::EXAMPLE_SOAP_ENDPOINT)->call('Add', soap_node()->body(['intA' => 10, 'intB' => 25]));
        $this->assertEquals(35, $result->AddResult);
    }

    /** @test */
    public function itCanForwardMethodCalls()
    {
        Soap::fake(['*' => Response::new(['AddResult' => 35])]);
        $result = Soap::to(static::EXAMPLE_SOAP_ENDPOINT)->Add(['intA' => 10, 'intB' => 25]);
        $this->assertEquals(35, $result->AddResult);
    }

    /** @test */
    public function itWorksWithASoapable()
    {
        Soap::fake(['*' => Response::new(['AddResult' => 35])]);
        $result = Soap::to(static::EXAMPLE_SOAP_ENDPOINT)->Add(new ExampleSoapable());
        $this->assertEquals(35, $result->AddResult);
    }
}

class ExampleSoapable implements Soapable
{
    public function toSoap()
    {
        return [
            'intA' => 10,
            'intB' => 25,
        ];
    }
}
