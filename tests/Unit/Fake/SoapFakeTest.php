<?php

use RicorocksDigitalAgency\Soap\Request\Request;
use RicorocksDigitalAgency\Soap\Response\Response;

it('can record requests')
    ->fake()
    ->assertNothingSent()
    ->tap(fn ($t) => $t->soap()->to(EXAMPLE_SOAP_ENDPOINT)->call('Add', ['intA' => 10, 'intB' => 20]))
    ->assertSentCount(1);

it('can fake requests')
    ->fake()
    ->tap(fn ($t) => $t->soap()->to(EXAMPLE_SOAP_ENDPOINT)->call('Bob', ['intA' => 10, 'intB' => 20]))
    ->assertSentCount(1);

it('returns a new response when fake is called with no parameters')
    ->fake()
    ->tap(fn ($t) => $t->soap()->to(EXAMPLE_SOAP_ENDPOINT)->call('Bob', ['intA' => 10, 'intB' => 20]))
    ->assertSent(fn (Request $request, Response $response) => $response->response == []);

it('can fake specific endpoints')
    ->fake([
        'http://foobar.com' => Response::new(['foo' => 'bar']),
               'http://foobar.com/testing' => Response::new(['baz' => 'bam']),
       ])
    ->tap(fn ($t) => $t->soap()->to('http://foobar.com')->call('Bob', ['intA' => 10, 'intB' => 20]))
    ->assertSent(fn ($request, Response $response) => $response->response['foo'] === 'bar')
    ->assertSent(fn (Request $request, Response $response) => $request->getMethod() === 'Bob')
    ->assertNotSent(fn (Request $request, Response $response) => $request->getMethod() === 'Trudy');

it('can handle wildcards')
    ->fake(['http://foobar.*' => Response::new(['foo' => 'bar'])])
    ->tap(fn ($t) => $t->soap()->to('http://foobar.com')->call('Bob', ['intA' => 10, 'intB' => 20]))
    ->tap(fn ($t) => $t->soap()->to('http://foobar.org')->call('Bob', ['intA' => 20, 'intB' => 30]))
    ->tap(fn ($t) => $t->soap()->to('http://foobar.co.uk')->call('Bob', ['intA' => 30, 'intB' => 40]))
    ->assertSentCount(3)
    ->assertSent(fn ($request, $response) => $request->getBody() === [
            'intA' => 10,
            'intB' => 20,
        ] && $response->response === ['foo' => 'bar'])
    ->assertSent(fn ($request, $response) => $request->getBody() === [
            'intA' => 20,
            'intB' => 30,
        ] && $response->response === ['foo' => 'bar'])
    ->assertSent(fn ($request, $response) => $request->getBody() === [
            'intA' => 30,
            'intB' => 40,
        ] && $response->response === ['foo' => 'bar']);

it('can handle multiple wildcards')
    ->fake([
               'http://foobar.*' => Response::new(['foo' => 'bar']),
               'http://foobar.co.*' => Response::new(['baz' => 'english dear']),
           ])
    ->tap(fn ($t) => $t->soap()->to('http://foobar.com')->call('Bob', ['intA' => 10, 'intB' => 20]))
    ->tap(fn ($t) => $t->soap()->to('http://foobar.org')->call('Bob', ['intA' => 20, 'intB' => 30]))
    ->tap(fn ($t) => $t->soap()->to('http://foobar.co.uk')->call('Bob', ['intA' => 30, 'intB' => 40]))
    ->assertSentCount(3)
    ->assertSent(fn ($request, $response) => $request->getBody() === [
            'intA' => 10,
            'intB' => 20,
        ] && $response->response === ['foo' => 'bar'])
    ->assertSent(fn ($request, $response) => $request->getBody() === [
            'intA' => 20,
            'intB' => 30,
        ] && $response->response === ['foo' => 'bar'])
    ->assertSent(fn ($request, $response) => $request->getBody() === [
            'intA' => 30,
            'intB' => 40,
        ] && $response->response === ['baz' => 'english dear']);
