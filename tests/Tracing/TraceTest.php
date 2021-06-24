<?php

namespace RicorocksDigitalAgency\Soap\Tests\Tracing;

use RicorocksDigitalAgency\Soap\Support\Tracing\Trace;
use RicorocksDigitalAgency\Soap\Tests\TestCase;
use SoapClient;

class TraceTest extends TestCase
{
    /** @test */
    public function the_trace_object_has_a_static_client_method()
    {
        $trace = Trace::client($client = new SoapClient(static::EXAMPLE_SOAP_ENDPOINT));

        $this->assertSame($client->__getLastRequest(), $trace->xmlRequest);
        $this->assertSame($client->__getLastResponse(), $trace->xmlResponse);
        $this->assertSame($client->__getLastRequestHeaders(), $trace->requestHeaders);
        $this->assertSame($client->__getLastResponseHeaders(), $trace->responseHeaders);
    }

    /** @test */
    public function a_fresh_trace_returns_gracefully()
    {
        $trace = new Trace();

        $this->assertNull($trace->client);
        $this->assertNull($trace->xmlRequest);
        $this->assertNull($trace->xmlResponse);
        $this->assertNull($trace->requestHeaders);
        $this->assertNull($trace->responseHeaders);
    }
}
