<?php

use RicorocksDigitalAgency\Soap\Facades\Soap;
use RicorocksDigitalAgency\Soap\Request\Request;
use RicorocksDigitalAgency\Soap\Response\Response;
use RicorocksDigitalAgency\Soap\Tests\TestCase;

it('can record requests', function() {
    $soap = soap();
    $soap->fake();
    $soap->assertNothingSent();

    $soap->to(EXAMPLE_SOAP_ENDPOINT)->call('Add', ['intA' => 10, 'intB' => 20]);
    $soap->assertSentCount(1);
});

it('can fake requests', function() {
    $soap = soap();
    $soap->fake();
    $soap->to(EXAMPLE_SOAP_ENDPOINT)->call('Bob', ['intA' => 10, 'intB' => 20]);
    $soap->assertSentCount(1);
});

it('returns a new response when fake is called with no parameters', function() {
    $soap = soap();
    $soap->fake();
    $soap->to(EXAMPLE_SOAP_ENDPOINT)->call('Bob', ['intA' => 10, 'intB' => 20]);
    $soap->assertSent(fn (Request $request, Response $response) => $response->response == []);
});

it('can fake specific endpoints', function() {
    $soap = soap();
    $soap->fake();
    $soap->fake(['http://foobar.com' => Response::new(['foo' => 'bar'])]);
    $soap->fake(['http://foobar.com/testing' => Response::new(['baz' => 'bam'])]);

    $soap->to('http://foobar.com')->call('Bob', ['intA' => 10, 'intB' => 20]);

    $soap->assertSent(fn ($request, Response $response) => $response->response['foo'] === 'bar');
    $soap->assertSent(fn (Request $request, Response $response) => $request->getMethod() === 'Bob');
    $soap->assertNotSent(fn (Request $request, Response $response) => $request->getMethod() === 'Trudy');
});

it('can handle wildcards', function() {
    $soap = soap();
    $soap->fake(['http://foobar.*' => Response::new(['foo' => 'bar'])]);

    $soap->to('http://foobar.com')->call('Bob', ['intA' => 10, 'intB' => 20]);
    $soap->to('http://foobar.org')->call('Bob', ['intA' => 20, 'intB' => 30]);
    $soap->to('http://foobar.co.uk')->call('Bob', ['intA' => 30, 'intB' => 40]);

    $soap->assertSentCount(3);
    $soap->assertSent(
        fn ($request, $response) => $request->getBody() === [
                'intA' => 10,
                'intB' => 20,
            ] && $response->response === ['foo' => 'bar']
    );
    $soap->assertSent(
        fn ($request, $response) => $request->getBody() === [
                'intA' => 20,
                'intB' => 30,
            ] && $response->response === ['foo' => 'bar']
    );
    $soap->assertSent(
        fn ($request, $response) => $request->getBody() === [
                'intA' => 30,
                'intB' => 40,
            ] && $response->response === ['foo' => 'bar']
    );
});

it('can handle multiple wildcards', function() {
    $soap = soap();
    $soap->fake(
        [
            'http://foobar.*' => Response::new(['foo' => 'bar']),
            'http://foobar.co.*' => Response::new(['baz' => 'english dear']),
        ]
    );

    $soap->to('http://foobar.com')->call('Bob', ['intA' => 10, 'intB' => 20]);
    $soap->to('http://foobar.org')->call('Bob', ['intA' => 20, 'intB' => 30]);
    $soap->to('http://foobar.co.uk')->call('Bob', ['intA' => 30, 'intB' => 40]);

    $soap->assertSentCount(3);
    $soap->assertSent(
        fn ($request, $response) => $request->getBody() === [
                'intA' => 10,
                'intB' => 20,
            ] && $response->response === ['foo' => 'bar']
    );
    $soap->assertSent(
        fn ($request, $response) => $request->getBody() === [
                'intA' => 20,
                'intB' => 30,
            ] && $response->response === ['foo' => 'bar']
    );
    $soap->assertSent(
        fn ($request, $response) => $request->getBody() === [
                'intA' => 30,
                'intB' => 40,
            ] && $response->response === ['baz' => 'english dear']
    );
});

