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
    ->tap(fn() => $this->soap()->beforeRequesting(fn () => $this->increment++))
    ->tap(fn() => $this->soap()->to('http://endpoint.com')->Test())
    ->tap(fn() => $this->soap()->to('http://foobar.com')->Test())
    ->expect(fn() => $this->increment)->toEqual(2);

it('can perform global afterRequesting hooks')
    ->fake(['http://endpoint.com' => Response::new(), 'http://foobar.com' => Response::new()])
    ->tap(fn() => $this->soap()->afterRequesting(fn () => $this->increment++))
    ->tap(fn() => $this->soap()->to('http://endpoint.com')->Test())
    ->tap(fn() => $this->soap()->to('http://foobar.com')->Test())
    ->expect(fn() => $this->increment)->toEqual(2);

it('can run hooks without having to fake')
    ->tap(fn() => $this->expectException(Exception::class))
    ->tap(fn() => $this->soap()->beforeRequesting(function () { throw new Exception('Yippee! We hit this instead'); }))
    ->tap(fn() => $this->soap()->to('http://endpoint.com')->Test());

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
