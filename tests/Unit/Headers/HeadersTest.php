<?php

use RicorocksDigitalAgency\Soap\Request\SoapClientRequest;

it('can set headers')
    ->fake()
    ->tap(fn () => $this->soap()->to(EXAMPLE_SOAP_ENDPOINT)
        ->withHeaders($this->soap()->header('Auth', 'test.com')->data(['foo' => 'bar']))
        ->call('Add', ['intA' => 10, 'intB' => 25])
    )
    ->assertSent(fn (SoapClientRequest $request) => $request->getHeaders() == [
        $this->soap()->header('Auth', 'test.com')->data(['foo' => 'bar']),
    ]);

it('can define multiple headers in the same method')
    ->fake()
    ->tap(fn () => $this->soap()->to(EXAMPLE_SOAP_ENDPOINT)
        ->withHeaders(
            $this->soap()->header('Auth', 'test.com')->data(['foo' => 'bar']),
            $this->soap()->header('Brand', 'test.com')->data(['hello' => 'world'])
        )
        ->call('Add', ['intA' => 10, 'intB' => 25])
    )
    ->assertSent(fn (SoapClientRequest $request) => $request->getHeaders() == [
        $this->soap()->header('Auth', 'test.com')->data(['foo' => 'bar']),
        $this->soap()->header('Brand', 'test.com')->data(['hello' => 'world']),
    ]);

it('can define multiple headers with an array in the same method')
    ->fake()
    ->tap(fn () => $this->soap()->to(EXAMPLE_SOAP_ENDPOINT)
        ->withHeaders(...[
            $this->soap()->header('Auth', 'test.com')->data(['foo' => 'bar']),
            $this->soap()->header('Brand', 'test.com')->data(['hello' => 'world']),
        ])
        ->call('Add', ['intA' => 10, 'intB' => 25])
    )
    ->assertSent(fn (SoapClientRequest $request) => $request->getHeaders() == [
        $this->soap()->header('Auth', 'test.com')->data(['foo' => 'bar']),
        $this->soap()->header('Brand', 'test.com')->data(['hello' => 'world']),
    ]);

it('can define multiple headers using a collection in the same method')
    ->fake()
    ->tap(fn () => $this->soap()->to(EXAMPLE_SOAP_ENDPOINT)
        ->withHeaders(...collect([
            $this->soap()->header('Auth', 'test.com')->data(['foo' => 'bar']),
            $this->soap()->header('Brand', 'test.com')->data(['hello' => 'world']),
        ]))
        ->call('Add', ['intA' => 10, 'intB' => 25])
    )
    ->assertSent(fn (SoapClientRequest $request) => $request->getHeaders() == [
        $this->soap()->header('Auth', 'test.com')->data(['foo' => 'bar']),
        $this->soap()->header('Brand', 'test.com')->data(['hello' => 'world']),
    ]);

it('can define multiple headers in multiple methods')
    ->fake()
    ->tap(fn () => $this->soap()->to(EXAMPLE_SOAP_ENDPOINT)
        ->withHeaders($this->soap()->header('Auth', 'test.com')->data(['foo' => 'bar']))
        ->withHeaders($this->soap()->header('Brand', 'test.com')->data(['hello' => 'world']))
        ->call('Add', ['intA' => 10, 'intB' => 25])
    )
    ->assertSent(fn (SoapClientRequest $request) => $request->getHeaders() == [
        $this->soap()->header('Auth', 'test.com')->data(['foo' => 'bar']),
        $this->soap()->header('Brand', 'test.com')->data(['hello' => 'world']),
    ]);

it('can create a header without any parameters and be composed fluently')
    ->fake()
    ->tap(fn () => $this->soap()->to(EXAMPLE_SOAP_ENDPOINT)->withHeaders(
        $this->soap()->header()
            ->name('Auth')
            ->namespace('test.com')
            ->data(['foo' => 'bar'])
            ->mustUnderstand()
            ->actor('this.test')
    )
        ->call('Add', ['intA' => 10, 'intB' => 25])
    )
    ->assertSent(fn (SoapClientRequest $request) => $request->getHeaders() == [
        $this->soap()->header('Auth', 'test.com')->data(['foo' => 'bar'])->mustUnderstand()->actor('this.test'),
    ]);

it('can set up a header using a SoapVar')
    ->fake()
    ->tap(fn () => $this->soap()->to(EXAMPLE_SOAP_ENDPOINT)
        ->withHeaders($this->soap()->header('Auth', 'test.com', new SoapVar(['foo' => 'bar'], null)))
        ->call('Add', ['intA' => 10, 'intB' => 25])
    )
    ->assertSent(fn (SoapClientRequest $request) => $request->getHeaders() == [
        $this->soap()->header('Auth', 'test.com')->data(new SoapVar(['foo' => 'bar'], null)),
    ]);

it('does not require the data parameter')
    ->fake()
    ->tap(fn () => $this->soap()->to(EXAMPLE_SOAP_ENDPOINT)
        ->withHeaders($this->soap()->header('Auth', 'test.com')->data(null))
        ->withHeaders($this->soap()->header('Brand', 'test.com', null))
        ->call('Add', ['intA' => 10, 'intB' => 25])
    )
    ->assertSent(fn (SoapClientRequest $request) => $request->getHeaders() == [
        $this->soap()->header('Auth', 'test.com', null),
        $this->soap()->header('Brand', 'test.com', null),
    ]);
