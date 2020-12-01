<?php


namespace RicorocksDigitalAgency\Soap\Tests;


use RicorocksDigitalAgency\Soap\Facades\Soap;
use RicorocksDigitalAgency\Soap\Parameters\Builder;
use RicorocksDigitalAgency\Soap\Request\Request;

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
    public function it_can_use_nodes()
    {
        $result = Soap::to(static::EXAMPLE_SOAP_ENDPOINT)->call('Add', soap_node()->body(['intA' => 10, 'intB' => 25]));
        $this->assertEquals(35, $result->AddResult);
    }

    /** @test */
    public function it_can_forward_method_calls()
    {
        $result = Soap::to(static::EXAMPLE_SOAP_ENDPOINT)->Add(['intA' => 10, 'intB' => 25]);
        $this->assertEquals(35, $result->AddResult);
    }

    /** @test */
    public function it_can_include_an_array_at_the_root_when_specified_using_the_include_method()
    {
        $this->mock(Builder::class)
            ->shouldReceive('handle')
            ->once()
            ->withArgs(
                function ($parameters) {
                    return $parameters ===
                        [
                            'intA' => 10,
                            'intB' => 25
                        ];
                }
            );

        Soap::include(['intA' => 10])->for(static::EXAMPLE_SOAP_ENDPOINT);
        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intB' => 25]));
    }

    /** @test */
    public function it_can_include_a_node_at_the_root_when_specified_using_the_include_method()
    {
        $this->mock(Builder::class)
            ->shouldReceive('handle')
            ->once()
            ->withArgs(
                function ($parameters) {
                    return $parameters ===
                        [
                            'foo' => [
                                "_" => "",
                                'foo' => 'bar'
                            ],
                            'intA' => 10,
                            'intB' => 25
                        ];
                }
            );

        Soap::include(['foo' => soap_node(['foo' => 'bar'])])->for(static::EXAMPLE_SOAP_ENDPOINT);
        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intA' => 10, 'intB' => 25]));
    }

    /** @test */
    public function it_only_includes_if_the_method_name_matches()
    {
        $this->mock(Builder::class)
            ->shouldReceive('handle')
            ->once()
            ->withArgs(
                function ($parameters) {
                    return $parameters ===
                        [
                            'intA' => 10,
                            'intB' => 25
                        ];
                }
            );

        Soap::include(['foo' => soap_node(['foo' => 'bar'])])->for(static::EXAMPLE_SOAP_ENDPOINT, 'Bar');
        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intA' => 10, 'intB' => 25]));
    }
}