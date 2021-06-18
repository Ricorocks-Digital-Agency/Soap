<?php

namespace RicorocksDigitalAgency\Soap\Tests\Options;

use RicorocksDigitalAgency\Soap\Facades\Soap;
use RicorocksDigitalAgency\Soap\Tests\TestCase;

class GlobalOptionsTest extends TestCase
{
    /** @test */
    public function itCanIncludeGlobalOptionsForEveryRequest()
    {
        Soap::fake();

        Soap::options(['login' => 'foo', 'password' => 'bar']);
        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intB' => 25]));

        Soap::assertSent(function ($request) {
            return $request->getOptions() == ['login' => 'foo', 'password' => 'bar'];
        });
    }

    /** @test */
    public function itCanScopeOptionsBasedOnTheEndpoint()
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
    public function itCanScopeOptionsBasedOnTheEndpointAndMethod()
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
