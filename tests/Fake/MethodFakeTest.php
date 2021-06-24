<?php

use RicorocksDigitalAgency\Soap\Response\Response;

it('can fake specific methods', function() {
    $soap = soap();
    $soap->fake(['http://foobar.com' => Response::new(['baz' => 'boom'])]);
    $soap->fake(['http://foobar.com:Add' => Response::new(['foo' => 'bar'])]);

    $soap->to('http://foobar.com')->call('Add', ['intA' => 10, 'intB' => 20]);

    $soap->assertSent(
        fn ($request, $response) => $request->getMethod() == 'Add' && $response->response == ['foo' => 'bar']
    );
});

it('can fake multiple methods declared by a pipe operator', function() {
    $soap = soap();

    $soap->fake(['http://foobar.com:Multiply|Divide' => Response::new(['baz' => 'boom'])]);
    $soap->fake(['http://foobar.com:Add|Subtract' => Response::new(['foo' => 'bar'])]);

    $soap->to('http://foobar.com')->call('Add', ['intA' => 10, 'intB' => 20]);
    $soap->to('http://foobar.com')->Subtract(['intA' => 10, 'intB' => 20]);
    $soap->to('http://foobar.com')->Multiply(['intA' => 10, 'intB' => 20]);
    $soap->to('http://foobar.com')->Divide(['intA' => 10, 'intB' => 20]);

    $soap->assertSent(
        fn ($request, $response) => $request->getMethod() == 'Add' && $response->response == ['foo' => 'bar']
    );
    $soap->assertSent(
        fn ($request, $response) => $request->getMethod() == 'Subtract' && $response->response == ['foo' => 'bar']
    );
    $soap->assertSent(
        fn ($request, $response) => $request->getMethod() == 'Multiply' && $response->response == ['baz' => 'boom']
    );
    $soap->assertSent(
        fn ($request, $response) => $request->getMethod() == 'Divide' && $response->response == ['baz' => 'boom']
    );
});

it('will use methods as a precedent over other fakes', function() {
    $soap = soap();

    $soap->fake(['*' => Response::new(['wild' => 'card'])]);
    $soap->fake(['http://foobar.com' => Response::new(['baz' => 'boom'])]);
    $soap->fake(['http://foobar.com:Add' => Response::new(['foo' => 'bar'])]);
    $soap->fake(['http://foobar.com*' => Response::new(['gee' => 'whizz'])]);

    $soap->to('http://foobar.com')->call('Add', ['intA' => 10, 'intB' => 20]);

    $soap->assertSent(
        fn ($request, $response) => $request->getMethod() == 'Add' && $response->response == ['foo' => 'bar']
    );
});
