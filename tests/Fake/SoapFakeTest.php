<?php


namespace RicorocksDigitalAgency\Soap\Tests\Fake;


use RicorocksDigitalAgency\Soap\Facades\Soap;
use RicorocksDigitalAgency\Soap\Request\Request;
use RicorocksDigitalAgency\Soap\Response\Response;
use RicorocksDigitalAgency\Soap\Tests\TestCase;

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
        Soap::fake(['http://foobar.com' => Response::new(['foo' => 'bar'])]);
        Soap::fake(['http://foobar.com/testing' => Response::new(['baz' => 'bam'])]);

        Soap::to('http://foobar.com')->call('Bob', ['intA' => 10, 'intB' => 20]);

        Soap::assertSent(fn($request, Response $response) => $response->response['foo'] === 'bar');
        Soap::assertSent(fn(Request $request, Response $response) => $request->getMethod() === 'Bob');
        Soap::assertNotSent(fn(Request $request, Response $response) => $request->getMethod() === 'Trudy');
    }

    /** @test */
    public function it_can_handle_wildcards()
    {
        Soap::fake(['http://foobar.*' => Response::new(['foo' => 'bar'])]);

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
                'http://foobar.*' => Response::new(['foo' => 'bar']),
                'http://foobar.co.*' => Response::new(['baz' => 'english dear']),
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
}
