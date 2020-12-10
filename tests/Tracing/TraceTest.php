<?php

namespace RicorocksDigitalAgency\Soap\Tests\Tracing;

use RicorocksDigitalAgency\Soap\Support\Tracing\Trace;
use RicorocksDigitalAgency\Soap\Tests\TestCase;

class TraceTest extends TestCase
{
    /** @test */
    public function the_trace_object_has_a_static_xmlRequest_method()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?><FooBar><Hello>World</Hello></FooBar>';

        $trace = Trace::xmlRequest($xml);

        $this->assertSame($xml, $trace->xmlRequest);
    }

    /** @test */
    public function the_trace_object_has_a_xmlResponse_method()
    {
        $request = '<?xml version="1.0" encoding="UTF-8"?><FooBar><Hello>World</Hello></FooBar>';
        $response = '<?xml version="1.0" encoding="UTF-8"?><Status>Success!</Status>';

        $trace = Trace::xmlRequest($request)->xmlResponse($response);

        $this->assertSame($response, $trace->xmlResponse);
    }
}
