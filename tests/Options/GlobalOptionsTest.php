<?php

use RicorocksDigitalAgency\Soap\Facades\Soap;
use RicorocksDigitalAgency\Soap\Tests\TestCase;

it('can include global options for every request', function() {
    $soap = soap();
    $soap->fake();

    $soap->options(['login' => 'foo', 'password' => 'bar']);
    $soap->to(EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intB' => 25]));

    $soap->assertSent(function ($request) {
        return $request->getOptions() == ['login' => 'foo', 'password' => 'bar'];
    });
});

it('can scope options based on the endpoint', function() {
    $soap = soap();
    $soap->fake();

    $soap->options(['login' => 'foo', 'password' => 'bar'])->for('https://foo.bar');
    $soap->options(['compression' => SOAP_COMPRESSION_GZIP])->for(EXAMPLE_SOAP_ENDPOINT);

    $soap->to(EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intB' => 25]));

    $soap->assertSent(function ($request) {
        return $request->getOptions() == ['compression' => SOAP_COMPRESSION_GZIP];
    });
});

it('can scope options based on the endpoint and method', function() {
    $soap = soap();
    $soap->fake();

    $soap->options(['login' => 'foo', 'password' => 'bar'])->for(EXAMPLE_SOAP_ENDPOINT, 'Add');
    $soap->options(['compression' => SOAP_COMPRESSION_GZIP])->for(EXAMPLE_SOAP_ENDPOINT, 'Subtract');

    $soap->to(EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intB' => 25]));

    $soap->assertSent(function ($request) {
        return $request->getOptions() == ['login' => 'foo', 'password' => 'bar'];
    });
});
