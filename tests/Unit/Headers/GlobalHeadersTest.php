<?php

it('can include global headers for every request', function () {
    $this->soap()->fake();

    $this->soap()->headers($this->soap()->header('Auth', 'test.com', ['foo' => 'bar']));
    $this->soap()->to(EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intB' => 25]));

    $this->soap()->assertSent(fn ($request) => $request->getHeaders() == [
        $this->soap()->header('Auth', 'test.com', ['foo' => 'bar']),
    ]);
});

it('can include scoped headers based on the endpoint', function () {
    $this->soap()->fake();

    $this->soap()->headers($this->soap()->header('Brand', 'test.coms', ['hello' => 'world']))->for('https://foo.bar');
    $this->soap()->headers($this->soap()->header('Auth', 'test.com', ['foo' => 'bar']))->for(EXAMPLE_SOAP_ENDPOINT);

    $this->soap()->to(EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intB' => 25]));

    $this->soap()->assertSent(fn ($request) => $request->getHeaders() == [
        $this->soap()->header('Auth', 'test.com', ['foo' => 'bar']),
    ]);
});

it('can scope headers based on the endpoint and method', function () {
    $this->soap()->fake();

    $this->soap()->headers($this->soap()->header('Brand', 'test.coms', ['hello' => 'world']))->for(EXAMPLE_SOAP_ENDPOINT, 'Add');
    $this->soap()->headers($this->soap()->header('Auth', 'test.com', ['foo' => 'bar']))->for(EXAMPLE_SOAP_ENDPOINT, 'Subtract');

    $this->soap()->to(EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intB' => 25]));

    $this->soap()->assertSent(fn ($request) => $request->getHeaders() == [
        $this->soap()->header('Brand', 'test.coms', ['hello' => 'world']),
    ]);
});

it('merges the global headers with the local headers', function () {
    $this->soap()->fake();

    $this->soap()->headers($this->soap()->header('Brand', 'test.coms', ['hello' => 'world']));

    $this->soap()->to(EXAMPLE_SOAP_ENDPOINT)
        ->withHeaders($this->soap()->header('Auth', 'test.com', ['foo' => 'bar']))
        ->call('Add', (['intB' => 25]));

    $this->soap()->assertSent(fn ($request) => $request->getHeaders() == [
        $this->soap()->header('Auth', 'test.com', ['foo' => 'bar']),
        $this->soap()->header('Brand', 'test.coms', ['hello' => 'world']),
    ]);
});
