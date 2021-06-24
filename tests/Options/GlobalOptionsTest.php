<?php

namespace RicorocksDigitalAgency\Soap\Tests\Options;

use RicorocksDigitalAgency\Soap\Facades\Soap;
use RicorocksDigitalAgency\Soap\Tests\TestCase;

class GlobalOptionsTest extends TestCase
{
    /** @test */
    public function it_can_include_global_options_for_every_request()
    {
        Soap::fake();

        Soap::options(['login' => 'foo', 'password' => 'bar']);
        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intB' => 25]));

        Soap::assertSent(function ($request) {
            return $request->getOptions() == ['login' => 'foo', 'password' => 'bar'];
        });
    }

    /** @test */
    public function it_can_scope_options_based_on_the_endpoint()
    {
        Soap::fake();

        Soap::options(['login' => 'foo', 'password' => 'bar'])->for('https://foo.bar');
        Soap::options(['compression' => SOAP_COMPRESSION_GZIP])->for(static::EXAMPLE_SOAP_ENDPOINT);

        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intB' => 25]));

        Soap::assertSent(function ($request) {
            return $request->getOptions() == ['compression' => SOAP_COMPRESSION_GZIP];
        });
    }

    /** @test */
    public function it_can_scope_options_based_on_the_endpoint_and_method()
    {
        Soap::fake();

        Soap::options(['login' => 'foo', 'password' => 'bar'])->for(static::EXAMPLE_SOAP_ENDPOINT, 'Add');
        Soap::options(['compression' => SOAP_COMPRESSION_GZIP])->for(static::EXAMPLE_SOAP_ENDPOINT, 'Subtract');

        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intB' => 25]));

        Soap::assertSent(function ($request) {
            return $request->getOptions() == ['login' => 'foo', 'password' => 'bar'];
        });
    }
}
