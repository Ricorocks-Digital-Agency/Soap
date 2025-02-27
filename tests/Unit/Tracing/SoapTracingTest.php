<?php

it('can request a trace at the time of request')
    ->expect(fn () => $this->traceableSoap()->to(EXAMPLE_SOAP_ENDPOINT)->trace()->call('Add', ['intA' => 10, 'intB' => 25])->trace())
    ->xmlRequest->toEqual('<?xml version="1.0" encoding="UTF-8"?><FooBar><Hello>World</Hello></FooBar>')
    ->xmlResponse->toEqual('<?xml version="1.0" encoding="UTF-8"?><Status>Success!</Status>')
    ->requestHeaders->toEqual('Hello World')
    ->responseHeaders->toEqual('Foo Bar');

it('can request traces globally')
    ->expect(fn () => with(
        $this->traceableSoap()->trace(),
        fn ($soap) => $soap->to(EXAMPLE_SOAP_ENDPOINT)->call('Add', ['intA' => 10, 'intB' => 25])->trace())
    )
    ->xmlRequest->toEqual('<?xml version="1.0" encoding="UTF-8"?><FooBar><Hello>World</Hello></FooBar>')
    ->xmlResponse->toEqual('<?xml version="1.0" encoding="UTF-8"?><Status>Success!</Status>')
    ->requestHeaders->toEqual('Hello World')
    ->responseHeaders->toEqual('Foo Bar');

it('has no content in the trace by default')
    ->expect(fn () => $this->traceableSoap()->to(EXAMPLE_SOAP_ENDPOINT)->call('Add', ['intA' => 10, 'intB' => 25])->trace())
    ->xmlRequest->toBeEmpty()
    ->xmlResponse->toBeEmpty()
    ->requestHeaders->toBeEmpty()
    ->responseHeaders->toBeEmpty();
