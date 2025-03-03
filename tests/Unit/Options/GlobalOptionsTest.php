<?php

it('can include global options for every request')
    ->fake()
    ->soap()->options(['login' => 'foo', 'password' => 'bar'])
    ->test()->soap()->to(EXAMPLE_SOAP_ENDPOINT)->call('Add', ['intB' => 25])
    ->test()->assertSent(function ($request) {
        return $request->getOptions() == ['login' => 'foo', 'password' => 'bar'];
    });

it('can scope options based on the endpoint')
    ->fake()
    ->defer(fn () => $this->soap()->options(['login' => 'foo', 'password' => 'bar'])->for('https://foo.bar'))
    ->defer(fn () => $this->soap()->options(['compression' => SOAP_COMPRESSION_GZIP])->for(EXAMPLE_SOAP_ENDPOINT))
    ->soap()->to(EXAMPLE_SOAP_ENDPOINT)->call('Add', ['intB' => 25])
    ->test()->assertSent(function ($request) {
        return $request->getOptions() == ['compression' => SOAP_COMPRESSION_GZIP];
    });

it('can scope options based on the endpoint and method')
    ->fake()
    ->defer(fn () => $this->soap()->options(['login' => 'foo', 'password' => 'bar'])->for(EXAMPLE_SOAP_ENDPOINT, 'Add'))
    ->defer(fn () => $this->soap()->options(['compression' => SOAP_COMPRESSION_GZIP])->for(EXAMPLE_SOAP_ENDPOINT, 'Subtract'))
    ->soap()->to(EXAMPLE_SOAP_ENDPOINT)->call('Add', ['intB' => 25])
    ->test()->assertSent(function ($request) {
        return $request->getOptions() == ['login' => 'foo', 'password' => 'bar'];
    });
