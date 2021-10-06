<?php

use RicorocksDigitalAgency\Soap\Response\Response;

it('can fake specific methods')
    ->fake([
        'http://foobar.com' => Response::new(['baz' => 'boom']),
        'http://foobar.com:Add' => Response::new(['foo' => 'bar']),
    ])
    ->soap()->to('http://foobar.com')->call('Add', ['intA' => 10, 'intB' => 20])
    ->test()->assertSent(fn ($request, $response) => $request->getMethod() == 'Add' && $response->response == ['foo' => 'bar']);

it('can fake multiple methods declared by a pipe operator')
    ->with([['Add'], ['Subtract'], ['Multiply'], ['Divide']])
    ->fake(['http://foobar.com:Add|Subtract|Multiply|Divide' => Response::new(['foo' => 'bar'])])
    ->tap(fn ($method) => $this->soap()->to('http://foobar.com')->call($method, ['intA' => 10, 'intB' => 20]))
    ->tap(fn ($method) => $this->assertSent(fn ($request, $response) => $request->getMethod() == $method
        && $response->response == ['foo' => 'bar'])
    );

it('will use methods as a precedent over other fakes')
    ->fake([
           '*' => Response::new(['wild' => 'card']),
               'http://foobar.com' => Response::new(['baz' => 'boom']),
               'http://foobar.com:Add' => Response::new(['foo' => 'bar']),
               'http://foobar.com*' => Response::new(['gee' => 'whizz']),
    ])
    ->soap()->to('http://foobar.com')->call('Add', ['intA' => 10, 'intB' => 20])
    ->test()->assertSent(fn ($request, $response) => $request->getMethod() == 'Add' && $response->response == ['foo' => 'bar']);
