<?php

use RicorocksDigitalAgency\Soap\Response\Response;

it('can fake specific methods')
    ->fake([
        'http://foobar.com' => Response::new(['baz' => 'boom']),
        'http://foobar.com:Add' => Response::new(['foo' => 'bar']),
    ])
    ->tap(fn ($t) => $t->soap()->to('http://foobar.com')->call('Add', ['intA' => 10, 'intB' => 20]))
    ->assertSent(fn ($request, $response) => $request->getMethod() == 'Add' && $response->response == ['foo' => 'bar']);

it('can fake multiple methods declared by a pipe operator')
    ->fake([
        'http://foobar.com:Multiply|Divide' => Response::new(['baz' => 'boom']),
        'http://foobar.com:Add|Subtract' => Response::new(['foo' => 'bar']),
    ])
    ->tap(fn ($t) => $t->soap()->to('http://foobar.com')->call('Add', ['intA' => 10, 'intB' => 20]))
    ->tap(fn ($t) => $t->soap()->to('http://foobar.com')->Subtract(['intA' => 10, 'intB' => 20]))
    ->tap(fn ($t) => $t->soap()->to('http://foobar.com')->Multiply(['intA' => 10, 'intB' => 20]))
    ->tap(fn ($t) => $t->soap()->to('http://foobar.com')->Divide(['intA' => 10, 'intB' => 20]))
    ->assertSent(fn ($request, $response) => $request->getMethod() == 'Add' && $response->response == ['foo' => 'bar'])
    ->assertSent(fn ($request, $response) => $request->getMethod() == 'Subtract' && $response->response == ['foo' => 'bar'])
    ->assertSent(fn ($request, $response) => $request->getMethod() == 'Multiply' && $response->response == ['baz' => 'boom'])
    ->assertSent(fn ($request, $response) => $request->getMethod() == 'Divide' && $response->response == ['baz' => 'boom']);

it('will use methods as a precedent over other fakes')
    ->fake([
           '*' => Response::new(['wild' => 'card']),
               'http://foobar.com' => Response::new(['baz' => 'boom']),
               'http://foobar.com:Add' => Response::new(['foo' => 'bar']),
               'http://foobar.com*' => Response::new(['gee' => 'whizz']),
    ])
    ->tap(fn ($t) => $t->soap()->to('http://foobar.com')->call('Add', ['intA' => 10, 'intB' => 20]))
    ->assertSent(fn ($request, $response) => $request->getMethod() == 'Add' && $response->response == ['foo' => 'bar']);
