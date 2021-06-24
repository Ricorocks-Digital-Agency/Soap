<?php

it('can include global options for every request', function () {
    $this->soap()->fake();

    $this->soap()->options(['login' => 'foo', 'password' => 'bar']);
    $this->soap()->to(EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intB' => 25]));

    $this->soap()->assertSent(function ($request) {
        return $request->getOptions() == ['login' => 'foo', 'password' => 'bar'];
    });
});

it('can scope options based on the endpoint', function () {
    $this->soap()->fake();

    $this->soap()->options(['login' => 'foo', 'password' => 'bar'])->for('https://foo.bar');
    $this->soap()->options(['compression' => SOAP_COMPRESSION_GZIP])->for(EXAMPLE_SOAP_ENDPOINT);

    $this->soap()->to(EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intB' => 25]));

    $this->soap()->assertSent(function ($request) {
        return $request->getOptions() == ['compression' => SOAP_COMPRESSION_GZIP];
    });
});

it('can scope options based on the endpoint and method', function () {
    $this->soap()->fake();

    $this->soap()->options(['login' => 'foo', 'password' => 'bar'])->for(EXAMPLE_SOAP_ENDPOINT, 'Add');
    $this->soap()->options(['compression' => SOAP_COMPRESSION_GZIP])->for(EXAMPLE_SOAP_ENDPOINT, 'Subtract');

    $this->soap()->to(EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intB' => 25]));

    $this->soap()->assertSent(function ($request) {
        return $request->getOptions() == ['login' => 'foo', 'password' => 'bar'];
    });
});
