<?php

namespace RicorocksDigitalAgency\Soap\Tests\Headers;

use RicorocksDigitalAgency\Soap\Facades\Soap;
use RicorocksDigitalAgency\Soap\Request\SoapClientRequest;
use RicorocksDigitalAgency\Soap\Tests\TestCase;
use SoapVar;

class HeadersTest extends TestCase
{
    /** @test */
    public function headersCanBeSet()
    {
        Soap::fake();

        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)
            ->withHeaders(Soap::header('Auth', 'test.com')->data(['foo' => 'bar']))
            ->call('Add', ['intA' => 10, 'intB' => 25]);

        Soap::assertSent(
            function (SoapClientRequest $request, $response) {
                return $request->getHeaders() == [
                    Soap::header('Auth', 'test.com')->data(['foo' => 'bar']),
                ];
            }
        );
    }

    /** @test */
    public function multipleHeadersCanBeDefinedInTheSameMethod()
    {
        Soap::fake();

        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)
            ->withHeaders(
                Soap::header('Auth', 'test.com')->data(['foo' => 'bar']),
                Soap::header('Brand', 'test.com')->data(['hello' => 'world'])
            )
            ->call('Add', ['intA' => 10, 'intB' => 25]);

        Soap::assertSent(
            function (SoapClientRequest $request, $response) {
                return $request->getHeaders() == [
                    Soap::header('Auth', 'test.com')->data(['foo' => 'bar']),
                    Soap::header('Brand', 'test.com')->data(['hello' => 'world']),
                ];
            }
        );
    }

    /** @test */
    public function multipleHeadersCanBeDefinedWithAnArrayInTheSameMethod()
    {
        Soap::fake();

        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)
            ->withHeaders(...[
                Soap::header('Auth', 'test.com')->data(['foo' => 'bar']),
                Soap::header('Brand', 'test.com')->data(['hello' => 'world']),
            ])
            ->call('Add', ['intA' => 10, 'intB' => 25]);

        Soap::assertSent(
            function (SoapClientRequest $request, $response) {
                return $request->getHeaders() == [
                    Soap::header('Auth', 'test.com')->data(['foo' => 'bar']),
                    Soap::header('Brand', 'test.com')->data(['hello' => 'world']),
                ];
            }
        );
    }

    /** @test */
    public function multipleHeadersCanBeDefinedWithACollectionInTheSameMethod()
    {
        Soap::fake();

        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)
            ->withHeaders(...collect([
                Soap::header('Auth', 'test.com')->data(['foo' => 'bar']),
                Soap::header('Brand', 'test.com')->data(['hello' => 'world']),
            ]))
            ->call('Add', ['intA' => 10, 'intB' => 25]);

        Soap::assertSent(
            function (SoapClientRequest $request, $response) {
                return $request->getHeaders() == [
                    Soap::header('Auth', 'test.com')->data(['foo' => 'bar']),
                    Soap::header('Brand', 'test.com')->data(['hello' => 'world']),
                ];
            }
        );
    }

    /** @test */
    public function multipleHeadersCanBeDefinedInTheMultipleMethods()
    {
        Soap::fake();

        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)
            ->withHeaders(Soap::header('Auth', 'test.com')->data(['foo' => 'bar']))
            ->withHeaders(Soap::header('Brand', 'test.com')->data(['hello' => 'world']))
            ->call('Add', ['intA' => 10, 'intB' => 25]);

        Soap::assertSent(
            function (SoapClientRequest $request, $response) {
                return $request->getHeaders() == [
                    Soap::header('Auth', 'test.com')->data(['foo' => 'bar']),
                    Soap::header('Brand', 'test.com')->data(['hello' => 'world']),
                ];
            }
        );
    }

    /** @test */
    public function aHeaderCanBeCreatedWithoutAnyParametersAndBeComposedFluently()
    {
        Soap::fake();

        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)
            ->withHeaders(
                Soap::header()
                    ->name('Auth')
                    ->namespace('test.com')
                    ->data(['foo' => 'bar'])
                    ->mustUnderstand()
                    ->actor('this.test')
            )
            ->call('Add', ['intA' => 10, 'intB' => 25]);

        Soap::assertSent(
            function (SoapClientRequest $request, $response) {
                return $request->getHeaders() == [
                    Soap::header('Auth', 'test.com')
                        ->data(['foo' => 'bar'])
                        ->mustUnderstand()
                        ->actor('this.test'),
                ];
            }
        );
    }

    /** @test */
    public function aHeaderCanBeCreatedWithTheHelperMethod()
    {
        Soap::fake();

        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)
            ->withHeaders(soap_header('Auth', 'test.com', ['foo' => 'bar']))
            ->call('Add', ['intA' => 10, 'intB' => 25]);

        Soap::assertSent(
            function (SoapClientRequest $request, $response) {
                return $request->getHeaders() == [
                    Soap::header('Auth', 'test.com')->data(['foo' => 'bar']),
                ];
            }
        );
    }

    /** @test */
    public function aHeaderCanBeSetUsingASoapVar()
    {
        Soap::fake();

        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)
            ->withHeaders(soap_header('Auth', 'test.com', new SoapVar(['foo' => 'bar'], null)))
            ->call('Add', ['intA' => 10, 'intB' => 25]);

        Soap::assertSent(
            function (SoapClientRequest $request, $response) {
                return $request->getHeaders() == [
                    Soap::header('Auth', 'test.com')->data(new SoapVar(['foo' => 'bar'], null)),
                ];
            }
        );
    }

    /** @test */
    public function theOptionalParametersOfTheSoapHeaderAreOptional()
    {
        Soap::fake();

        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)
            ->withHeaders(Soap::header('Auth', 'test.com')->data(null)->actor(null))
            ->withHeaders(soap_header('Brand', 'test.com', null, false, null))
            ->call('Add', ['intA' => 10, 'intB' => 25]);

        Soap::assertSent(
            function (SoapClientRequest $request, $response) {
                return $request->getHeaders() == [
                    Soap::header('Auth', 'test.com', null, false, null),
                    Soap::header('Brand', 'test.com', null, false, null),
                ];
            }
        );
    }
}
