<?php

namespace RicorocksDigitalAgency\Soap\Tests\Headers;

use RicorocksDigitalAgency\Soap\Facades\Soap;
use RicorocksDigitalAgency\Soap\Tests\TestCase;

class GlobalHeadersTest extends TestCase
{
    /** @test */
    public function it_can_include_global_headers_for_every_request()
    {
        Soap::fake();

        Soap::headers(soap_header('Auth', 'test.com', ['foo' => 'bar']));
        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intB' => 25]));

        Soap::assertSent(function ($request) {
            return $request->getHeaders() == [
                soap_header('Auth', 'test.com', ['foo' => 'bar']),
            ];
        });
    }

    /** @test */
    public function it_can_scope_headers_based_on_the_endpoint()
    {
        Soap::fake();

        Soap::headers(soap_header('Brand', 'test.coms', ['hello' => 'world']))->for('https://foo.bar');
        Soap::headers(soap_header('Auth', 'test.com', ['foo' => 'bar']))->for(static::EXAMPLE_SOAP_ENDPOINT);

        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intB' => 25]));

        Soap::assertSent(function ($request) {
            return $request->getHeaders() == [
                soap_header('Auth', 'test.com', ['foo' => 'bar']),
            ];
        });
    }

    /** @test */
    public function it_can_scope_headers_based_on_the_endpoint_and_method()
    {
        Soap::fake();

        Soap::headers(soap_header('Brand', 'test.coms', ['hello' => 'world']))->for(static::EXAMPLE_SOAP_ENDPOINT, 'Add');
        Soap::headers(soap_header('Auth', 'test.com', ['foo' => 'bar']))->for(static::EXAMPLE_SOAP_ENDPOINT, 'Subtract');

        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intB' => 25]));

        Soap::assertSent(function ($request) {
            return $request->getHeaders() == [
                soap_header('Brand', 'test.coms', ['hello' => 'world']),
            ];
        });
    }

    /** @test */
    public function the_global_headers_are_merged_with_local_headers()
    {
        Soap::fake();

        Soap::headers(soap_header('Brand', 'test.coms', ['hello' => 'world']));

        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)
            ->withHeaders(soap_header('Auth', 'test.com', ['foo' => 'bar']))
            ->call('Add', (['intB' => 25]));

        Soap::assertSent(function ($request) {
            return $request->getHeaders() == [
                soap_header('Auth', 'test.com', ['foo' => 'bar']),
                soap_header('Brand', 'test.coms', ['hello' => 'world']),
            ];
        });
    }
}
