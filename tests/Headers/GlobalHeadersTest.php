<?php

it('can include global headers for every request', function() {
    $soap = soap();
    $soap->fake();

    $soap->headers($soap->header('Auth', 'test.com', ['foo' => 'bar']));
    $soap->to(EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intB' => 25]));

    $soap->assertSent(function ($request) use ($soap) {
        return $request->getHeaders() == [
                $soap->header('Auth', 'test.com', ['foo' => 'bar']),
            ];
    });
});

it('can include scoped headers based on the endpoint', function() {
    $soap = soap();
    $soap->fake();

    $soap->headers($soap->header('Brand', 'test.coms', ['hello' => 'world']))->for('https://foo.bar');
    $soap->headers($soap->header('Auth', 'test.com', ['foo' => 'bar']))->for(EXAMPLE_SOAP_ENDPOINT);

    $soap->to(EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intB' => 25]));

    $soap->assertSent(function ($request) use ($soap) {
        return $request->getHeaders() == [
                $soap->header('Auth', 'test.com', ['foo' => 'bar']),
            ];
    });
});

it('can scope headers based on the endpoint and method', function() {
    $soap = soap();
    $soap->fake();

    $soap->headers($soap->header('Brand', 'test.coms', ['hello' => 'world']))->for(EXAMPLE_SOAP_ENDPOINT, 'Add');
    $soap->headers($soap->header('Auth', 'test.com', ['foo' => 'bar']))->for(EXAMPLE_SOAP_ENDPOINT, 'Subtract');

    $soap->to(EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intB' => 25]));

    $soap->assertSent(function ($request) use ($soap) {
        return $request->getHeaders() == [
                $soap->header('Brand', 'test.coms', ['hello' => 'world']),
            ];
    });
});

it('merges the global headers with the local headers', function() {
    $soap = soap();
    $soap->fake();

    $soap->headers($soap->header('Brand', 'test.coms', ['hello' => 'world']));

    $soap->to(EXAMPLE_SOAP_ENDPOINT)
        ->withHeaders($soap->header('Auth', 'test.com', ['foo' => 'bar']))
        ->call('Add', (['intB' => 25]));

    $soap->assertSent(function ($request) use ($soap) {
        return $request->getHeaders() == [
                $soap->header('Auth', 'test.com', ['foo' => 'bar']),
                $soap->header('Brand', 'test.coms', ['hello' => 'world']),
            ];
    });
});


