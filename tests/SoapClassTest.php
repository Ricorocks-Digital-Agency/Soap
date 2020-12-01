<?php


namespace RicorocksDigitalAgency\Soap\Tests;


use RicorocksDigitalAgency\Soap\Facades\Soap;

class SoapClassTest extends TestCase
{

    /** @test */
    public function it_can_obtain_a_wsdl()
    {
        $functions = Soap::to(static::EXAMPLE_SOAP_ENDPOINT)->functions();
        $this->assertIsArray($functions);
        $this->assertNotEmpty($functions);
    }

    /** @test */
    public function it_can_call_a_SOAP_function()
    {
        $result = Soap::to(static::EXAMPLE_SOAP_ENDPOINT)->call('Add', ['intA' => 10, 'intB' => 25]);
        $this->assertEquals(35, $result->AddResult);
    }

    /** @test */
    public function it_can_forward_method_calls()
    {
        $result = Soap::to(static::EXAMPLE_SOAP_ENDPOINT)->Add(['intA' => 10, 'intB' => 25]);
        $this->assertEquals(35, $result->AddResult);
    }

}