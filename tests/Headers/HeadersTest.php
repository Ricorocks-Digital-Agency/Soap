<?php

namespace RicorocksDigitalAgency\Soap\Tests\Headers;

use RicorocksDigitalAgency\Soap\Facades\Soap;
use RicorocksDigitalAgency\Soap\Request\SoapClientRequest;
use RicorocksDigitalAgency\Soap\Tests\TestCase;

class HeadersTest extends TestCase
{
    /** @test */
    public function headers_can_be_set()
    {
        Soap::fake();

        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)
            ->withHeaders(Soap::header('Auth', 'xml')->data(['foo' => 'bar']))
            ->call('Add', ['intA' => 10, 'intB' => 25]);

        Soap::assertSent(
            function (SoapClientRequest $request, $response) {
                return $request->getHeaders() == [
                    Soap::header('Auth', 'xml')->data(['foo' => 'bar'])
                ];
            }
        );
    }

    /** @test */
    public function multiple_headers_can_be_defined_in_the_same_method()
    {
        Soap::fake();

        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)
            ->withHeaders(...[
                Soap::header('Auth', 'xml')->data(['foo' => 'bar']),
                Soap::header('Brand', 'xml')->data(['hello' => 'world']),
            ])
            ->call('Add', ['intA' => 10, 'intB' => 25]);

        Soap::assertSent(
            function (SoapClientRequest $request, $response) {
                return $request->getHeaders() == [
                    Soap::header('Auth', 'xml')->data(['foo' => 'bar']),
                    Soap::header('Brand', 'xml')->data(['hello' => 'world']),
                ];
            }
        );
    }

    /** @test */
    public function multiple_headers_can_be_defined_in_the_multiple_methods()
    {
        Soap::fake();

        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)
            ->withHeaders(Soap::header('Auth', 'xml')->data(['foo' => 'bar']))
            ->withHeaders(Soap::header('Brand', 'xml')->data(['hello' => 'world']))
            ->call('Add', ['intA' => 10, 'intB' => 25]);

        Soap::assertSent(
            function (SoapClientRequest $request, $response) {
                return $request->getHeaders() == [
                    Soap::header('Auth', 'xml')->data(['foo' => 'bar']),
                    Soap::header('Brand', 'xml')->data(['hello' => 'world']),
                ];
            }
        );
    }

    /** @test */
    public function a_header_can_be_created_without_any_parameters_and_be_composed_fluently()
    {
        Soap::fake();

        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)
            ->withHeaders(
                Soap::header()
                    ->name('Auth')
                    ->namespace('xml')
                    ->data(['foo' => 'bar'])
                    ->mustUnderstand()
                    ->actor('this.test')
            )
            ->call('Add', ['intA' => 10, 'intB' => 25]);

        Soap::assertSent(
            function (SoapClientRequest $request, $response) {
                return $request->getHeaders() == [
                    Soap::header('Auth', 'xml')
                        ->data(['foo' => 'bar'])
                        ->mustUnderstand()
                        ->actor('this.test')
                ];
            }
        );
    }

    /** @test */
    public function a_header_can_be_created_with_the_helper_method()
    {
        Soap::fake();

        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)
            ->withHeaders(soap_header('Auth', 'xml', ['foo' => 'bar']))
            ->call('Add', ['intA' => 10, 'intB' => 25]);

        Soap::assertSent(
            function (SoapClientRequest $request, $response) {
                return $request->getHeaders() == [
                    Soap::header('Auth', 'xml')->data(['foo' => 'bar'])
                ];
            }
        );
    }
}
