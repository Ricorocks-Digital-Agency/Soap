<?php

use RicorocksDigitalAgency\Soap\Facades\Soap;
use RicorocksDigitalAgency\Soap\Request\SoapClientRequest;

it('can set headers', function() {
    $soap = soap();
    $soap->fake();

    $soap->to(EXAMPLE_SOAP_ENDPOINT)
        ->withHeaders($soap->header('Auth', 'test.com')->data(['foo' => 'bar']))
        ->call('Add', ['intA' => 10, 'intB' => 25]);

    $soap->assertSent(
        function (SoapClientRequest $request) use ($soap) {
            return $request->getHeaders() == [
                    $soap->header('Auth', 'test.com')->data(['foo' => 'bar']),
                ];
        }
    );
});

it('can define multiple headers in the same method', function() {
    $soap = soap();
    $soap->fake();

    $soap->to(EXAMPLE_SOAP_ENDPOINT)
        ->withHeaders(
            $soap->header('Auth', 'test.com')->data(['foo' => 'bar']),
            $soap->header('Brand', 'test.com')->data(['hello' => 'world'])
        )
        ->call('Add', ['intA' => 10, 'intB' => 25]);

    $soap->assertSent(
        function (SoapClientRequest $request) use ($soap) {
            return $request->getHeaders() == [
                    $soap->header('Auth', 'test.com')->data(['foo' => 'bar']),
                    $soap->header('Brand', 'test.com')->data(['hello' => 'world']),
                ];
        }
    );
});

it('can define multiple headers with an array in the same method', function() {
    $soap = soap();
    $soap->fake();

    $soap->to(EXAMPLE_SOAP_ENDPOINT)
        ->withHeaders(...[
                             $soap->header('Auth', 'test.com')->data(['foo' => 'bar']),
                             $soap->header('Brand', 'test.com')->data(['hello' => 'world']),
                         ])
        ->call('Add', ['intA' => 10, 'intB' => 25]);

    $soap->assertSent(
        function (SoapClientRequest $request) use($soap) {
            return $request->getHeaders() == [
                    $soap->header('Auth', 'test.com')->data(['foo' => 'bar']),
                    $soap->header('Brand', 'test.com')->data(['hello' => 'world']),
                ];
        }
    );
});

it('can define multiple headers using a collection in the same method', function() {
    $soap = soap();
    $soap->fake();

    $soap->to(EXAMPLE_SOAP_ENDPOINT)
        ->withHeaders(...collect([
            $soap->header('Auth', 'test.com')->data(['foo' => 'bar']),
            $soap->header('Brand', 'test.com')->data(['hello' => 'world']),
        ]))
        ->call('Add', ['intA' => 10, 'intB' => 25]);

    $soap->assertSent(
        function (SoapClientRequest $request) use ($soap) {
            return $request->getHeaders() == [
                $soap->header('Auth', 'test.com')->data(['foo' => 'bar']),
                $soap->header('Brand', 'test.com')->data(['hello' => 'world']),
            ];
        }
    );
});

it('can define multiple headers in multiple methods', function() {
    $soap = soap();
    $soap->fake();

    $soap->to(EXAMPLE_SOAP_ENDPOINT)
        ->withHeaders($soap->header('Auth', 'test.com')->data(['foo' => 'bar']))
        ->withHeaders($soap->header('Brand', 'test.com')->data(['hello' => 'world']))
        ->call('Add', ['intA' => 10, 'intB' => 25]);

    $soap->assertSent(
        function (SoapClientRequest $request) use ($soap) {
            return $request->getHeaders() == [
                    $soap->header('Auth', 'test.com')->data(['foo' => 'bar']),
                    $soap->header('Brand', 'test.com')->data(['hello' => 'world']),
                ];
        }
    );
});

it('can create a header without any parameters and be composed fluently', function() {
    $soap = soap();
    $soap->fake();

    $soap->to(EXAMPLE_SOAP_ENDPOINT)
        ->withHeaders(
            $soap->header()
                ->name('Auth')
                ->namespace('test.com')
                ->data(['foo' => 'bar'])
                ->mustUnderstand()
                ->actor('this.test')
        )
        ->call('Add', ['intA' => 10, 'intB' => 25]);

    $soap->assertSent(
        function (SoapClientRequest $request) use ($soap) {
            return $request->getHeaders() == [
                    $soap->header('Auth', 'test.com')
                        ->data(['foo' => 'bar'])
                        ->mustUnderstand()
                        ->actor('this.test'),
                ];
        }
    );
});

it('can create a header with the helper method', function() {
    $soap = soap();
    $soap->fake();

    $soap->to(EXAMPLE_SOAP_ENDPOINT)
        ->withHeaders(soap_header('Auth', 'test.com', ['foo' => 'bar']))
        ->call('Add', ['intA' => 10, 'intB' => 25]);

    $soap->assertSent(
        function (SoapClientRequest $request) use ($soap) {
            return $request->getHeaders() == [
                $soap->header('Auth', 'test.com')->data(['foo' => 'bar']),
            ];
        }
    );
})->skip('Needs moving to a Laravel test case because it uses facades');

it('can set up a header using a SoapVar', function() {
    $soap = soap();
    $soap->fake();

    $soap->to(EXAMPLE_SOAP_ENDPOINT)
        ->withHeaders($soap->header('Auth', 'test.com', new SoapVar(['foo' => 'bar'], null)))
        ->call('Add', ['intA' => 10, 'intB' => 25]);

    $soap->assertSent(fn(SoapClientRequest $request) => $request->getHeaders() == [
        $soap->header('Auth', 'test.com')->data(new SoapVar(['foo' => 'bar'], null)),
    ]);
});

it('does not require the data parameter', function() {
    $soap = soap();
    $soap->fake();

    $soap->to(EXAMPLE_SOAP_ENDPOINT)
        ->withHeaders($soap->header('Auth', 'test.com')->data(null))
        ->withHeaders($soap->header('Brand', 'test.com', null))
        ->call('Add', ['intA' => 10, 'intB' => 25]);

    $soap->assertSent(fn(SoapClientRequest $request) => $request->getHeaders() == [
        $soap->header('Auth', 'test.com', null),
        $soap->header('Brand', 'test.com', null),
    ]);
});

it('will pass a provided actor to the php soap header', function() {
    $this->app->afterResolving(SoapHeader::class, function ($header) {
        $this->assertEquals('test.com', $header->actor);
    });

    Soap::to(EXAMPLE_SOAP_ENDPOINT)
        ->withHeaders(Soap::header('Auth', 'test.com')->actor('test.com'))
        ->withHeaders(soap_header('Brand', 'test.com', ['hi'], false, 'test.com'))
        ->withHeaders(soap_header('Service', 'bar.com')->actor('test.com'))
        ->call('Add', ['intA' => 10, 'intB' => 25]);
})
    ->skip('This makes a real API call to assert the correct header construction')
    ->addWarning('Needs moving to a Laravel enabled test environment');

it('will send the SOAP_ACTOR_NONE constant if no actor is provided', function() {
    $this->app->afterResolving(SoapHeader::class, function ($header) {
        $this->assertEquals(SOAP_ACTOR_NONE, $header->actor);
    });

    Soap::to(EXAMPLE_SOAP_ENDPOINT)
        ->withHeaders(Soap::header('Auth', 'test.com')->actor(null))
        ->withHeaders(soap_header('Brand', 'test.com', null, false, null))
        ->withHeaders(soap_header('Service', 'bar.com'))
        ->call('Add', ['intA' => 10, 'intB' => 25]);
})
    ->skip('This makes a real API call to assert the correct header construction')
    ->addWarning('Needs moving to a Laravel enabled test environment');


