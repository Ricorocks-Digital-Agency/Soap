<?php

use RicorocksDigitalAgency\Soap\Request\Request;
use RicorocksDigitalAgency\Soap\Response\Response;

trait ProvidesIncrementingCounter
{
    public $increment = 0;
}

uses(ProvidesIncrementingCounter::class)->afterEach(function () { $this->increment = 0; });

it('can perform global beforeRequesting hooks')
    ->fake(['http://endpoint.com' => Response::new(), 'http://foobar.com' => Response::new()])
    ->tap(fn($t) => $t->soap()->beforeRequesting(fn () => $t->increment++))
    ->tap(fn($t) => $t->soap()->to('http://endpoint.com')->Test())
    ->tap(fn($t) => $t->soap()->to('http://foobar.com')->Test())
    ->defer(fn($t) => expect($t->increment)->toEqual(2));

it('can perform global afterRequesting hooks')
    ->fake(['http://endpoint.com' => Response::new(), 'http://foobar.com' => Response::new()])
    ->tap(fn($t) => $t->soap()->afterRequesting(fn () => $t->increment++))
    ->tap(fn($t) => $t->soap()->to('http://endpoint.com')->Test())
    ->tap(fn($t) => $t->soap()->to('http://foobar.com')->Test())
    ->defer(fn($t) => expect($t->increment)->toEqual(2));

it('can run hooks without having to fake')
    ->tap(fn($t) => $t->expectException(Exception::class))
    ->tap(fn($t) => $t->soap()->beforeRequesting(function () { throw new Exception('Yippee! We hit this instead'); }))
    ->tap(fn($t) => $t->soap()->to('http://endpoint.com')->Test());

it('can use beforeRequesting hooks to transform the request object', function () {
    $this->soap()->fake();

    $this->soap()->beforeRequesting(fn (Request $request) => $request->set('hello.world', ['foo', 'bar']));
    $this->soap()->beforeRequesting(fn (Request $request) => $request->set('hello.person', 'Richard'));
    $this->soap()->to('http://endpoint.com')->Test();

    $this->soap()->assertSent(fn ($request) => $request->getBody()['hello'] === ['world' => ['foo', 'bar'], 'person' => 'Richard']);
});

it('can use afterRequesting hooks to transform the response object', function () {
    $this->soap()->fake();

    $this->soap()->afterRequesting(fn ($request, Response $response) => $response->set('hello.world', ['foo', 'bar']));
    $this->soap()->afterRequesting(fn ($request, Response $response) => $response->set('hello.person', 'Richard'));
    $this->soap()->to('http://endpoint.com')->Test();

    $this->soap()->assertSent(fn ($request, Response $response) => $response->response['hello'] === ['world' => ['foo', 'bar'], 'person' => 'Richard']);
});
