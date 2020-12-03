<?php


namespace RicorocksDigitalAgency\Soap\Tests;


use RicorocksDigitalAgency\Soap\Facades\Soap;
use RicorocksDigitalAgency\Soap\Request\Request;
use RicorocksDigitalAgency\Soap\Response\Response;

class SoapFakeTest extends TestCase
{

    /** @test */
    public function it_can_record_requests()
    {
        Soap::fake();
        Soap::assertNothingSent();

        Soap::to(self::EXAMPLE_SOAP_ENDPOINT)->call('Add', ['intA' => 10, 'intB' => 20]);
        Soap::assertSentCount(1);
    }

    /** @test */
    public function it_can_fake_requests()
    {
        Soap::fake();
        Soap::to(self::EXAMPLE_SOAP_ENDPOINT)->call('Bob', ['intA' => 10, 'intB' => 20]);
        Soap::assertSentCount(1);
    }

    /** @test */
    public function it_can_fake_specific_endpoints()
    {
        Soap::fake();
        Soap::fake(['http://foobar.com' => new Response(['foo' => 'bar'])]);
        Soap::fake(['http://foobar.com/testing' => new Response(['baz' => 'bam'])]);
        Soap::to('http://foobar.com')->call('Bob', ['intA' => 10, 'intB' => 20]);
        Soap::assertSent(fn($request, Response $response) => $response->response['foo'] === 'bar');
        Soap::assertSent(fn(Request $request, Response $response) => $request->getMethod() === 'Bob');
        Soap::assertNotSent(fn(Request $request, Response $response) => $request->getMethod() === 'Trudy');
    }

    /** @test */
    public function it_can_handle_wildcards()
    {
        Soap::fake(['http://foobar.*' => new Response(['foo' => 'bar'])]);

        Soap::to('http://foobar.com')->call('Bob', ['intA' => 10, 'intB' => 20]);
        Soap::to('http://foobar.org')->call('Bob', ['intA' => 20, 'intB' => 30]);
        Soap::to('http://foobar.co.uk')->call('Bob', ['intA' => 30, 'intB' => 40]);

        Soap::assertSentCount(3);
        Soap::assertSent(
            fn($request, $response) => $request->getBody() === [
                    'intA' => 10,
                    'intB' => 20
                ] && $response->response === ['foo' => 'bar']
        );
        Soap::assertSent(
            fn($request, $response) => $request->getBody() === [
                    'intA' => 20,
                    'intB' => 30
                ] && $response->response === ['foo' => 'bar']
        );
        Soap::assertSent(
            fn($request, $response) => $request->getBody() === [
                    'intA' => 30,
                    'intB' => 40
                ] && $response->response === ['foo' => 'bar']
        );
    }

    /** @test */
    public function it_can_handle_multiple_wildcards()
    {
        Soap::fake(
            [
                'http://foobar.*' => new Response(['foo' => 'bar']),
                'http://foobar.co.*' => new Response(['baz' => 'english dear']),
            ]
        );

        Soap::to('http://foobar.com')->call('Bob', ['intA' => 10, 'intB' => 20]);
        Soap::to('http://foobar.org')->call('Bob', ['intA' => 20, 'intB' => 30]);
        Soap::to('http://foobar.co.uk')->call('Bob', ['intA' => 30, 'intB' => 40]);

        Soap::assertSentCount(3);
        Soap::assertSent(
            fn($request, $response) => $request->getBody() === [
                    'intA' => 10,
                    'intB' => 20
                ] && $response->response === ['foo' => 'bar']
        );
        Soap::assertSent(
            fn($request, $response) => $request->getBody() === [
                    'intA' => 20,
                    'intB' => 30
                ] && $response->response === ['foo' => 'bar']
        );
        Soap::assertSent(
            fn($request, $response) => $request->getBody() === [
                    'intA' => 30,
                    'intB' => 40
                ] && $response->response === ['baz' => 'english dear']
        );
    }

    /** @test */
    public function it_can_handle_methods()
    {
        Soap::fake(['http://foobar.com' => new Response(['baz' => 'boom'])]);
        Soap::fake(['http://foobar.com:Add' => new Response(['foo' => 'bar'])]);

        Soap::to('http://foobar.com')->call('Add', ['intA' => 10, 'intB' => 20]);

        Soap::assertSent(
            fn($request, $response) => $request->getMethod() == 'Add' && $response->response == ['foo' => 'bar']
        );
    }

    /** @test */
    public function it_can_handle_multiple_methods_declared_by_a_pipe_operator()
    {
        Soap::fake(['http://foobar.com:Multiply|Divide' => new Response(['baz' => 'boom'])]);
        Soap::fake(['http://foobar.com:Add|Subtract' => new Response(['foo' => 'bar'])]);

        Soap::to('http://foobar.com')->call('Add', ['intA' => 10, 'intB' => 20]);
        Soap::to('http://foobar.com')->Subtract(['intA' => 10, 'intB' => 20]);

        Soap::to('http://foobar.com')->Multiply(['intA' => 10, 'intB' => 20]);
        Soap::to('http://foobar.com')->Divide(['intA' => 10, 'intB' => 20]);

        Soap::assertSent(
            fn($request, $response) => $request->getMethod() == 'Add' && $response->response == ['foo' => 'bar']
        );
        Soap::assertSent(
            fn($request, $response) => $request->getMethod() == 'Subtract' && $response->response == ['foo' => 'bar']
        );
        Soap::assertSent(
            fn($request, $response) => $request->getMethod() == 'Multiply' && $response->response == ['baz' => 'boom']
        );
        Soap::assertSent(
            fn($request, $response) => $request->getMethod() == 'Divide' && $response->response == ['baz' => 'boom']
        );
    }
}
