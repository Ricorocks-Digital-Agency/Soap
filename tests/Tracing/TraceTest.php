<?php

namespace RicorocksDigitalAgency\Soap\Tests\Tracing;

use RicorocksDigitalAgency\Soap\Support\Tracing\Trace;
use RicorocksDigitalAgency\Soap\Tests\TestCase;
use SoapClient;

class TraceTest extends TestCase
{
    /** @test */
    public function theTraceObjectHasAStaticClientMethod()
    {
        $trace = Trace::client($client = new SoapClient(static::EXAMPLE_SOAP_ENDPOINT));

        $this->assertSame($client->__getLastRequest(), $trace->xmlRequest);
        $this->assertSame($client->__getLastResponse(), $trace->xmlResponse);
        $this->assertSame($client->__getLastRequestHeaders(), $trace->requestHeaders);
        $this->assertSame($client->__getLastResponseHeaders(), $trace->responseHeaders);
    }

    /** @test */
    public function aFreshTraceReturnsGracefully()
    {
        $trace = new Trace();

        $this->assertNull($trace->client);
        $this->assertNull($trace->xmlRequest);
        $this->assertNull($trace->xmlResponse);
        $this->assertNull($trace->requestHeaders);
        $this->assertNull($trace->responseHeaders);
    }
}
