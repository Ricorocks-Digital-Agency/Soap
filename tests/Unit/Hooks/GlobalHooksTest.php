<?php

use RicorocksDigitalAgency\Soap\Request\Request;
use RicorocksDigitalAgency\Soap\Response\Response;
use RicorocksDigitalAgency\Soap\Tests\Concerns\ProvidesIncrementingCounter;

uses(ProvidesIncrementingCounter::class)->afterEach(function () { $this->increment = 0; });

it('can perform global beforeRequesting hooks')
    ->fake(['http://endpoint.com' => Response::new(), 'http://foobar.com' => Response::new()])
    ->tap(fn () => $this->soap()->beforeRequesting(fn () => $this->increment++))
    ->tap(fn () => $this->soap()->to('http://endpoint.com')->call('method'))
    ->tap(fn () => $this->soap()->to('http://foobar.com')->call('method'))
    ->expect(fn () => $this->increment)->toEqual(2);

it('can perform global afterRequesting hooks')
    ->fake(['http://endpoint.com' => Response::new(), 'http://foobar.com' => Response::new()])
    ->tap(fn () => $this->soap()->afterRequesting(fn () => $this->increment++))
    ->tap(fn () => $this->soap()->to('http://endpoint.com')->call('method'))
    ->tap(fn () => $this->soap()->to('http://foobar.com')->call('method'))
    ->expect(fn () => $this->increment)->toEqual(2);

it('can run hooks without having to fake')
    ->throws(Exception::class)
    ->soap()->beforeRequesting(function () { throw new Exception('Yippee! We hit this instead'); })
    ->to('http://endpoint.com')->call('method');

it('can use beforeRequesting hooks to transform the request object')
    ->fake()
    ->soap()
    ->beforeRequesting(fn (Request $request) => $request->set('hello.world', ['foo', 'bar']))
    ->beforeRequesting(fn (Request $request) => $request->set('hello.person', 'Richard'))
    ->to('http://endpoint.com')->call('method')
    ->test()->assertSent(fn ($request) => $request->getBody()['hello'] === ['world' => ['foo', 'bar'], 'person' => 'Richard']);

it('can use afterRequesting hooks to transform the response object')
    ->fake()
    ->soap()
    ->afterRequesting(fn ($request, Response $response) => $response->set('hello.world', ['foo', 'bar']))
    ->afterRequesting(fn ($request, Response $response) => $response->set('hello.person', 'Richard'))
    ->to('http://endpoint.com')->call('method')
    ->test()->assertSent(fn ($request, Response $response) => $response->response['hello'] === ['world' => ['foo', 'bar'], 'person' => 'Richard']);
