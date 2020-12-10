<?php

namespace RicorocksDigitalAgency\Soap\Tests\Tracing;

use RicorocksDigitalAgency\Soap\Support\Tracing\Trace;
use RicorocksDigitalAgency\Soap\Tests\TestCase;

class TraceTest extends TestCase
{
    /** @test */
    public function the_trace_object_has_a_static_thisXmlRequest_method()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?><FooBar><Hello>World</Hello></FooBar>';

        $trace = Trace::thisXmlRequest($xml);

        $this->assertSame($xml, $trace->xmlRequest);
    }

    /** @test */
    public function the_trace_object_has_a_thisXmlResponse_method()
    {
        $request = '<?xml version="1.0" encoding="UTF-8"?><FooBar><Hello>World</Hello></FooBar>';
        $response = '<?xml version="1.0" encoding="UTF-8"?><Status>Success!</Status>';

        $trace = Trace::thisXmlRequest($request)->thisXmlResponse($response);

        $this->assertSame($response, $trace->xmlResponse);
    }

    /** @test */
    public function a_null_trace_returns_gracefully()
    {
        $trace = Trace::thisXmlRequest(null)->thisXmlResponse(null);

        $this->assertNull($trace->xmlRequest);
        $this->assertNull($trace->xmlResponse);
    }

    /** @test */
    public function a_fresh_trace_returns_gracefully()
    {
        $trace = new Trace;

        $this->assertNull($trace->xmlRequest);
        $this->assertNull($trace->xmlResponse);
    }
}
