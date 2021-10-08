<?php

declare(strict_types=1);

use RicorocksDigitalAgency\Soap\Contracts\Request;
use RicorocksDigitalAgency\Soap\Response\Response;

it('can perform local beforeRequesting hooks')
    ->fake(['http://endpoint.com' => Response::new(), 'http://foobar.com' => Response::new()])
    ->tap(fn () => $this->soap()->to('http://endpoint.com')->beforeRequesting(fn () => $this->data['endpoint'] = 5)->Test())
    ->tap(fn () => $this->soap()->to('http://foobar.com')->beforeRequesting(fn () => $this->data['foobar'] = 2)->Test())
    ->expect(fn () => $this->data['endpoint'])->toBe(5)
    ->and(fn () => $this->data['foobar'])->toBe(2);

it('can perform local afterRequesting hooks')
    ->fake(['http://endpoint.com' => Response::new(), 'http://foobar.com' => Response::new()])
    ->tap(fn () => $this->soap()->to('http://endpoint.com')->afterRequesting(fn () => $this->data['endpoint'] = 5)->Test())
    ->tap(fn () => $this->soap()->to('http://foobar.com')->afterRequesting(fn () => $this->data['foobar'] = 2)->Test())
    ->expect(fn () => $this->data['endpoint'])->toBe(5)
    ->and(fn () => $this->data['foobar'])->toBe(2);

it('can chain hooks')
    ->fake(['http://endpoint.com' => Response::new()])
    ->tap(fn () => $this->soap()->to('http://endpoint.com')
        ->beforeRequesting(fn () => $this->data['before'] = 5)
        ->afterRequesting(fn () => $this->data['after'] = 2)
        ->call('method')
    )
    ->expect(fn () => $this->data['before'])->toBe(5)
    ->and(fn () => $this->data['after'])->toBe(2);

it('provides access to the request in the before hook')
    ->fake(['http://endpoint.com' => Response::new()])
    ->soap()->to('http://endpoint.com')
    ->beforeRequesting(fn (Request $request) => expect($request->getEndpoint())->toBe('http://endpoint.com'))
    ->call('method');

it('provides access to the request and the response in the after hook')
    ->fake(['http://endpoint.com' => Response::new(['foo' => 'bar'])])
    ->soap()->to('http://endpoint.com')
    ->afterRequesting(fn (Request $request) => expect($request->getEndpoint())->toBe('http://endpoint.com'))
    ->afterRequesting(fn (Request $request, Response $response) => expect($response->foo)->toBe('bar'))
    ->call('method');

it('allows beforeRequesting hooks to transform the request object')
    ->fake()
    ->soap()->to('http://endpoint.com')
    ->beforeRequesting(fn (Request $request) => $request->set('hello.world', ['foo', 'bar']))
    ->beforeRequesting(fn (Request $request) => $request->set('hello.person', 'Richard'))
    ->call('method')
    ->test()->assertSent(fn ($request) => $request->getBody()['hello'] === ['world' => ['foo', 'bar'], 'person' => 'Richard']);

it('allows afterRequesting hooks to transform the response object')
    ->fake()
    ->soap()->to('http://endpoint.com')
    ->afterRequesting(fn ($request, Response $response) => $response->set('hello.world', ['foo', 'bar']))
    ->afterRequesting(fn ($request, Response $response) => $response->set('hello.person', 'Richard'))
    ->call('method')
    ->test()->assertSent(fn ($request, Response $response) => $response->response['hello'] === ['world' => ['foo', 'bar'], 'person' => 'Richard']);
