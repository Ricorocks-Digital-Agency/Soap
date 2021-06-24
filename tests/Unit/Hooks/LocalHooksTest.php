<?php

use RicorocksDigitalAgency\Soap\Request\Request;
use RicorocksDigitalAgency\Soap\Response\Response;

trait ProvidesCounter
{
    public $counter = [];
}

uses(ProvidesCounter::class)->afterEach(function () { $this->counter = []; });

it('can perform local beforeRequesting hooks', function () {
    $this->soap()->fake(['http://endpoint.com' => Response::new(), 'http://foobar.com' => Response::new()]);

    $this->counter['endpoint'] = 0;
    $this->coutner['foobar'] = 0;

    $this->soap()->to('http://endpoint.com')->beforeRequesting(fn () => $this->counter['endpoint'] = 5)->Test();
    $this->soap()->to('http://foobar.com')->beforeRequesting(fn () => $this->counter['foobar'] = 2)->Test();

    $this->assertEquals(5, $this->counter['endpoint']);
    $this->assertEquals(2, $this->counter['foobar']);
});

it('can perform local afterRequesting hooks', function () {
    $this->soap()->fake(['http://endpoint.com' => Response::new(), 'http://foobar.com' => Response::new()]);

    $this->counter['endpoint'] = 0;
    $this->coutner['foobar'] = 0;

    $this->soap()->to('http://endpoint.com')->afterRequesting(fn () => $this->counter['endpoint'] = 5)->Test();
    $this->soap()->to('http://foobar.com')->afterRequesting(fn () => $this->counter['foobar'] = 2)->Test();

    $this->assertEquals(5, $this->counter['endpoint']);
    $this->assertEquals(2, $this->counter['foobar']);
});

it('can chain hooks', function () {
    $this->soap()->fake(['http://endpoint.com' => Response::new()]);

    $this->counter['before'] = 0;
    $this->counter['after'] = 0;

    $this->soap()->to('http://endpoint.com')
        ->beforeRequesting(fn () => $this->counter['before'] = 5)
        ->afterRequesting(fn () => $this->counter['after'] = 2)
        ->Test();

    $this->assertEquals(5, $this->counter['before']);
    $this->assertEquals(2, $this->counter['after']);
});

it('provides access to the request in the before hook', function () {
    $this->soap()->fake(['http://endpoint.com' => Response::new()]);

    $this->soap()->to('http://endpoint.com')
        ->beforeRequesting(fn (Request $request) => $this->assertEquals('http://endpoint.com', $request->getEndpoint()))
        ->Test();
});

it('provides access to the request and the response in the after hook', function () {
    $this->soap()->fake(['http://endpoint.com' => Response::new(['foo' => 'bar'])]);

    $this->soap()->to('http://endpoint.com')
        ->afterRequesting(fn (Request $request) => $this->assertEquals('http://endpoint.com', $request->getEndpoint()))
        ->afterRequesting(fn (Request $request, Response $response) => $this->assertEquals('bar', $response->foo))
        ->Test();
});

it('allows beforeRequesting hooks to transform the request object', function () {
    $this->soap()->fake();

    $this->soap()->to('http://endpoint.com')
        ->beforeRequesting(fn (Request $request) => $request->set('hello.world', ['foo', 'bar']))
        ->beforeRequesting(fn (Request $request) => $request->set('hello.person', 'Richard'))
        ->Test();

    $this->soap()->assertSent(fn ($request) => $request->getBody()['hello'] === ['world' => ['foo', 'bar'], 'person' => 'Richard']);
});

it('allows afterRequesting hooks to transform the response object', function () {
    $this->soap()->fake();

    $this->soap()->to('http://endpoint.com')
        ->afterRequesting(fn ($request, Response $response) => $response->set('hello.world', ['foo', 'bar']))
        ->afterRequesting(fn ($request, Response $response) => $response->set('hello.person', 'Richard'))
        ->Test();

    $this->soap()->assertSent(fn ($request, Response $response) => $response->response['hello'] === ['world' => ['foo', 'bar'], 'person' => 'Richard']);
});