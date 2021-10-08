<?php

declare(strict_types=1);

it('can include global headers for every request')
    ->fake()
    ->tap(fn () => $this->soap()->headers($this->soap()->header('Auth', 'test.com', ['foo' => 'bar'])))
    ->soap()->to(EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intB' => 25]))
    ->test()->assertSent(fn ($request) => $request->getHeaders() == [
        $this->soap()->header('Auth', 'test.com', ['foo' => 'bar']),
    ]);

it('can include scoped headers based on the endpoint')
    ->fake()
    ->tap(fn () => $this->soap()->headers($this->soap()->header('Brand', 'test.coms', ['hello' => 'world']))->for('https://foo.bar'))
    ->tap(fn () => $this->soap()->headers($this->soap()->header('Auth', 'test.com', ['foo' => 'bar']))->for(EXAMPLE_SOAP_ENDPOINT))
    ->soap()->to(EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intB' => 25]))
    ->test()->assertSent(fn ($request) => $request->getHeaders() == [
        $this->soap()->header('Auth', 'test.com', ['foo' => 'bar']),
    ]);

it('can scope headers based on the endpoint and method')
    ->fake()
    ->tap(fn () => $this->soap()->headers($this->soap()->header('Brand', 'test.coms', ['hello' => 'world']))->for(EXAMPLE_SOAP_ENDPOINT, 'Add'))
    ->tap(fn () => $this->soap()->headers($this->soap()->header('Auth', 'test.com', ['foo' => 'bar']))->for(EXAMPLE_SOAP_ENDPOINT, 'Subtract'))
    ->soap()->to(EXAMPLE_SOAP_ENDPOINT)->call('Add', (['intB' => 25]))
    ->test()->assertSent(fn ($request) => $request->getHeaders() == [
        $this->soap()->header('Brand', 'test.coms', ['hello' => 'world']),
    ]);

it('merges the global headers with the local headers')
    ->fake()
    ->tap(fn () => $this->soap()->headers($this->soap()->header('Brand', 'test.coms', ['hello' => 'world'])))
    ->tap(fn () => $this->soap()
        ->to(EXAMPLE_SOAP_ENDPOINT)
        ->withHeaders($this->soap()->header('Auth', 'test.com', ['foo' => 'bar']))
        ->call('Add', (['intB' => 25]))
    )
    ->assertSent(fn ($request) => $request->getHeaders() == [
        $this->soap()->header('Auth', 'test.com', ['foo' => 'bar']),
        $this->soap()->header('Brand', 'test.coms', ['hello' => 'world']),
    ]);
