<?php


namespace RicorocksDigitalAgency\Soap\Tests\Inclusions;


use RicorocksDigitalAgency\Soap\Facades\Soap;
use RicorocksDigitalAgency\Soap\Parameters\Builder;
use RicorocksDigitalAgency\Soap\Request\Request;
use RicorocksDigitalAgency\Soap\Response\Response;
use RicorocksDigitalAgency\Soap\Tests\TestCase;

class IncludeTest extends TestCase
{
    /** @test */
    public function it_can_include_an_array_at_the_root_when_specified_using_the_include_method()
    {
        Soap::fake();
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
        Soap::fake();
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
        Soap::fake();
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
