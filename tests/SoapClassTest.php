<?php

namespace RicorocksDigitalAgency\Soap\Tests;

use RicorocksDigitalAgency\Soap\Contracts\Soapable;
use RicorocksDigitalAgency\Soap\Facades\Soap;
use RicorocksDigitalAgency\Soap\Request\Request;
use RicorocksDigitalAgency\Soap\Response\Response;

class SoapClassTest extends TestCase
{
    /** @test */
    public function it_can_obtain_a_wsdl()
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
    public function it_can_call_asoap_function()
    {
        Soap::fake(['*' => Response::new(['AddResult' => 35])]);
        $result = Soap::to(static::EXAMPLE_SOAP_ENDPOINT)->call('Add', ['intA' => 10, 'intB' => 25]);
        $this->assertEquals(35, $result->AddResult);
    }

    /** @test */
    public function it_can_use_nodes()
    {
        Soap::fake(['*' => Response::new(['AddResult' => 35])]);
        $result = Soap::to(static::EXAMPLE_SOAP_ENDPOINT)->call('Add', soap_node()->body(['intA' => 10, 'intB' => 25]));
        $this->assertEquals(35, $result->AddResult);
    }

    /** @test */
    public function it_can_forward_method_calls()
    {
        Soap::fake(['*' => Response::new(['AddResult' => 35])]);
        $result = Soap::to(static::EXAMPLE_SOAP_ENDPOINT)->Add(['intA' => 10, 'intB' => 25]);
        $this->assertEquals(35, $result->AddResult);
    }

    /** @test */
    public function it_works_with_a_soapable()
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
