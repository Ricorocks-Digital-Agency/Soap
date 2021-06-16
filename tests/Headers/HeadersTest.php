<?php

namespace RicorocksDigitalAgency\Soap\Tests\Headers;

use RicorocksDigitalAgency\Soap\Facades\Soap;
use RicorocksDigitalAgency\Soap\Request\SoapClientRequest;
use RicorocksDigitalAgency\Soap\Tests\TestCase;
use SoapHeader;
use SoapVar;

class HeadersTest extends TestCase
{
    /** @test */
    public function headers_can_be_set()
    {
        Soap::fake();

        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)
            ->withHeaders(Soap::header('Auth', 'test.com')->data(['foo' => 'bar']))
            ->call('Add', ['intA' => 10, 'intB' => 25]);

        Soap::assertSent(
            function (SoapClientRequest $request, $response) {
                return $request->getHeaders() == [
                    Soap::header('Auth', 'test.com')->data(['foo' => 'bar'])
                ];
            }
        );
    }

    /** @test */
    public function multiple_headers_can_be_defined_in_the_same_method()
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
    public function multiple_headers_can_be_defined_with_an_array_in_the_same_method()
    {
        Soap::fake();

        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)
            ->withHeaders(...[
                Soap::header('Auth', 'test.com')->data(['foo' => 'bar']),
                Soap::header('Brand', 'test.com')->data(['hello' => 'world'])
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
    public function multiple_headers_can_be_defined_with_a_collection_in_the_same_method()
    {
        Soap::fake();

        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)
            ->withHeaders(...collect([
                Soap::header('Auth', 'test.com')->data(['foo' => 'bar']),
                Soap::header('Brand', 'test.com')->data(['hello' => 'world'])
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
    public function multiple_headers_can_be_defined_in_the_multiple_methods()
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
    public function a_header_can_be_created_without_any_parameters_and_be_composed_fluently()
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
            ->withHeaders(soap_header('Auth', 'test.com', ['foo' => 'bar']))
            ->call('Add', ['intA' => 10, 'intB' => 25]);

        Soap::assertSent(
            function (SoapClientRequest $request, $response) {
                return $request->getHeaders() == [
                    Soap::header('Auth', 'test.com')->data(['foo' => 'bar'])
                ];
            }
        );
    }

    /** @test */
    public function a_header_can_be_set_using_a_SoapVar()
    {
        Soap::fake();

        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)
            ->withHeaders(soap_header('Auth', 'test.com', new SoapVar(['foo' => 'bar'], null)))
            ->call('Add', ['intA' => 10, 'intB' => 25]);

        Soap::assertSent(
            function (SoapClientRequest $request, $response) {
                return $request->getHeaders() == [
                    Soap::header('Auth', 'test.com')->data(new SoapVar(['foo' => 'bar'], null))
                ];
            }
        );
    }

    /** @test */
    public function the_data_parameter_is_optional()
    {
        Soap::fake();

        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)
            ->withHeaders(Soap::header('Auth', 'test.com')->data(null))
            ->withHeaders(soap_header('Brand', 'test.com', null))
            ->call('Add', ['intA' => 10, 'intB' => 25]);

        Soap::assertSent(
            function (SoapClientRequest $request, $response) {
                return $request->getHeaders() == [
                    Soap::header('Auth', 'test.com', null),
                    Soap::header('Brand', 'test.com', null),
                ];
            }
        );
    }

    /** @test */
    public function if_the_actor_is_provided_it_is_passed_to_the_php_soap_header()
    {
        $this->markTestSkipped('This makes a real API call to assert the correct header construction');

        $this->app->beforeResolving(SoapHeader::class, function ($class, $arguments) {
            $this->assertTrue(array_key_exists('actor', $arguments));
        });

        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)
            ->withHeaders(Soap::header('Auth', 'test.com')->actor('test.com'))
            ->withHeaders(soap_header('Brand', 'test.com', ['hi'], false, 'test.com'))
            ->withHeaders(soap_header('Service', 'bar.com')->actor('test.com'))
            ->call('Add', ['intA' => 10, 'intB' => 25]);
    }

    /** @test */
    public function if_the_actor_is_not_provided_it_is_not_passed_to_the_php_soap_header()
    {
        $this->markTestSkipped('This makes a real API call to assert the correct header construction');

        $this->app->beforeResolving(SoapHeader::class, function ($class, $arguments) {
            $this->assertFalse(array_key_exists('actor', $arguments));
        });

        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)
            ->withHeaders(Soap::header('Auth', 'test.com')->actor(null))
            ->withHeaders(soap_header('Brand', 'test.com', null, false, null))
            ->withHeaders(soap_header('Service', 'bar.com'))
            ->call('Add', ['intA' => 10, 'intB' => 25]);
    }
}
