<?php

namespace RicorocksDigitalAgency\Soap\Tests\Inclusions;

use RicorocksDigitalAgency\Soap\Facades\Soap;
use RicorocksDigitalAgency\Soap\Parameters\Builder;
use RicorocksDigitalAgency\Soap\Tests\TestCase;

class IncludeTest extends TestCase
{
    /** @test */
    public function itCanIncludeAnArrayAtTheRootWithoutUsingFor()
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

        Soap::include(['intA' => 10]);
        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intB' => 25]));
    }

    /** @test */
    public function itCanIncludeAnArrayAtTheRootWhenSpecifiedUsingTheIncludeMethod()
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

        Soap::include(['intA' => 10])->for(static::EXAMPLE_SOAP_ENDPOINT);
        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intB' => 25]));
    }

    /** @test */
    public function itCanIncludeANodeAtTheRootWhenSpecifiedUsingTheIncludeMethod()
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
                            'foo' => soap_node(['foo' => 'bar']),
                        ];
                }
            );

        Soap::include(['foo' => soap_node(['foo' => 'bar'])])->for(static::EXAMPLE_SOAP_ENDPOINT);
        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intA' => 10, 'intB' => 25]));
    }

    /** @test */
    public function itOnlyIncludesIfTheMethodNameMatches()
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
    public function inclusionsCanBePlacedFurtherDownTheTreeUsingDotSyntax()
    {
        Soap::fake();
        Soap::include(['foo.bar' => 'Hello World'])->for(static::EXAMPLE_SOAP_ENDPOINT, 'Bar');
        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)->call('Bar', (['foo' => ['baz' => 'cool']]));

        Soap::assertSent(fn ($request) => $request->getBody() == ['foo' => ['baz' => 'cool', 'bar' => 'Hello World']]);
    }
}
