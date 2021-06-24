<?php

use RicorocksDigitalAgency\Soap\Facades\Soap;
use RicorocksDigitalAgency\Soap\Parameters\Builder;
use RicorocksDigitalAgency\Soap\Request\SoapClientRequest;
use RicorocksDigitalAgency\Soap\Tests\TestCase;
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

class IncludeTest extends TestCase
{

    /** @test */
    public function it_can_include_a_node_at_the_root_when_specified_using_the_include_method()
    {

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
                    return $parameters ==
                        [
                            'intA' => 10,
                            'intB' => 25,
                        ];
                }
            );

        Soap::include(['foo' => soap_node(['foo' => 'bar'])])->for(static::EXAMPLE_SOAP_ENDPOINT, 'Bar');
        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intA' => 10, 'intB' => 25]));
    }

    /** @test */
    public function inclusions_can_be_placed_further_down_the_tree_using_dot_syntax()
    {
        Soap::fake();
        Soap::include(['foo.bar' => 'Hello World'])->for(static::EXAMPLE_SOAP_ENDPOINT, 'Bar');
        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)->call('Bar', (['foo' => ['baz' => 'cool']]));

        Soap::assertSent(fn ($request) => $request->getBody() == ['foo' => ['baz' => 'cool', 'bar' => 'Hello World']]);
    }
}
