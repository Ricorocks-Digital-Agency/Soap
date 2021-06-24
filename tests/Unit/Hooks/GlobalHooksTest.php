<?php

use RicorocksDigitalAgency\Soap\Request\Request;
use RicorocksDigitalAgency\Soap\Response\Response;

trait ProvidesIncrementingCounter {
    static $increment = 0;
}

uses(ProvidesIncrementingCounter::class);

it('can perform global beforeRequesting hooks', function() {
    $soap = soap();

    $soap->fake(['http://endpoint.com' => Response::new(), 'http://foobar.com' => Response::new()]);

    $soap->beforeRequesting(fn () => static::$increment++);

    $soap->to('http://endpoint.com')->Test();
    $soap->to('http://foobar.com')->Test();

    $this->assertEquals(2, static::$increment);
});

it('can perform global afterRequesting hooks', function() {
    $soap = soap();
    $soap->fake(['http://endpoint.com' => Response::new(), 'http://foobar.com' => Response::new()]);

    $soap->afterRequesting(fn () => static::$increment++);

    $soap->to('http://endpoint.com')->Test();
    $soap->to('http://foobar.com')->Test();

    $this->assertEquals(2, static::$increment);
});

it('can run hooks without having to fake', function() {
    $soap = soap();
    $this->expectException(Exception::class);
    $soap->beforeRequesting(function () { throw new Exception('Yippee! We hit this instead'); });
    $soap->to('http://endpoint.com')->Test();
});

it('can use beforeRequesting hooks to transform the request object', function() {
    $soap = soap();
    $soap->fake();

    $soap->beforeRequesting(fn (Request $request) => $request->set('hello.world', ['foo', 'bar']));
    $soap->beforeRequesting(fn (Request $request) => $request->set('hello.person', 'Richard'));
    $soap->to('http://endpoint.com')->Test();

    $soap->assertSent(fn ($request) => $request->getBody()['hello'] === ['world' => ['foo', 'bar'], 'person' => 'Richard']);
});

it('can use afterRequesting hooks to transform the response object', function() {
    $soap = soap();
    $soap->fake();

    $soap->afterRequesting(fn ($request, Response $response) => $response->set('hello.world', ['foo', 'bar']));
    $soap->afterRequesting(fn ($request, Response $response) => $response->set('hello.person', 'Richard'));
    $soap->to('http://endpoint.com')->Test();

    $soap->assertSent(fn ($request, Response $response) => $response->response['hello'] === ['world' => ['foo', 'bar'], 'person' => 'Richard']);
});

afterEach(function() { static::$increment = 0; });
