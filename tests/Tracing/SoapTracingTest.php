<?php

namespace RicorocksDigitalAgency\Soap\Tests\Tracing;

use RicorocksDigitalAgency\Soap\Facades\Soap;
use RicorocksDigitalAgency\Soap\Support\Tracing\Trace;
use RicorocksDigitalAgency\Soap\Tests\TestCase;

class SoapTracingTest extends TestCase
{
    /** @test */
    public function a_trace_can_be_requested_at_time_of_request()
    {
        $this->markTestSkipped('This makes a real API call');

        $response = Soap::to(static::EXAMPLE_SOAP_ENDPOINT)
                        ->trace()
                        ->call('Add', ['intA' => 10, 'intB' => 25]);

        $this->assertNotEmpty($response->trace()->xmlRequest);
        $this->assertNotEmpty($response->trace()->xmlResponse);
    }

    /** @test */
    public function a_trace_can_be_requested_globally()
    {
        $this->markTestSkipped('This makes a real API call');

        Soap::trace();

        $response = Soap::to(static::EXAMPLE_SOAP_ENDPOINT)
                        ->call('Add', ['intA' => 10, 'intB' => 25]);

        $this->assertNotEmpty($response->trace()->xmlRequest);
        $this->assertNotEmpty($response->trace()->xmlResponse);
    }

    /** @test */
    public function by_default_the_trace_has_no_content_on_the_response()
    {
        Soap::fake();

        $response = Soap::to(static::EXAMPLE_SOAP_ENDPOINT)
                        ->call('Add', ['intA' => 10, 'intB' => 25]);

        $this->assertEmpty($response->trace()->xmlRequest);
        $this->assertEmpty($response->trace()->xmlResponse);
    }
}
