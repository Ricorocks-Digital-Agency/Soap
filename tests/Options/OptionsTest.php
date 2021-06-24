<?php

namespace RicorocksDigitalAgency\Soap\Tests\Options;

use RicorocksDigitalAgency\Soap\Facades\Soap;
use RicorocksDigitalAgency\Soap\Request\SoapClientRequest;
use RicorocksDigitalAgency\Soap\Tests\TestCase;

class OptionsTest extends TestCase
{
    /** @test */
    public function options_can_be_set()
    {
        Soap::fake();

        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)
            ->withOptions(['compression' => SOAP_COMPRESSION_GZIP])
            ->call('Add', ['intA' => 10, 'intB' => 25]);

        Soap::assertSent(
            function (SoapClientRequest $request, $response) {
                return $request->getOptions() == [
                    'compression' => SOAP_COMPRESSION_GZIP,
                ];
            }
        );
    }

    /** @test */
    public function it_merges_with_other_options()
    {
        Soap::fake();

        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)
            ->withBasicAuth('foo', 'bar')
            ->withOptions(['compression' => SOAP_COMPRESSION_GZIP])
            ->call('Add', ['intA' => 10, 'intB' => 25]);

        Soap::assertSent(
            function (SoapClientRequest $request, $response) {
                return $request->getOptions() == [
                    'authentication' => SOAP_AUTHENTICATION_BASIC,
                    'login' => 'foo',
                    'password' => 'bar',
                    'compression' => SOAP_COMPRESSION_GZIP,
                ];
            }
        );
    }

    /** @test */
    public function it_overrides_previous_values()
    {
        Soap::fake();

        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)
            ->withOptions(['compression' => SOAP_COMPRESSION_ACCEPT])
            ->withOptions(['compression' => SOAP_COMPRESSION_GZIP])
            ->call('Add', ['intA' => 10, 'intB' => 25]);

        Soap::assertSent(
            function (SoapClientRequest $request, $response) {
                return $request->getOptions() == [
                    'compression' => SOAP_COMPRESSION_GZIP,
                ];
            }
        );
    }
}
