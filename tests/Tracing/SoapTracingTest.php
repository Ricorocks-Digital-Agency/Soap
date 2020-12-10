<?php

namespace RicorocksDigitalAgency\Soap\Tests\Tracing;

use RicorocksDigitalAgency\Soap\Facades\Soap;
use RicorocksDigitalAgency\Soap\Support\Tracing\Trace;
use RicorocksDigitalAgency\Soap\Tests\TestCase;

class SoapTracingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeClient();
    }

    /** @test */
    public function a_trace_can_be_requested_at_time_of_request()
    {
        $response = Soap::to(static::EXAMPLE_SOAP_ENDPOINT)
                        ->trace()
                        ->call('Add', ['intA' => 10, 'intB' => 25]);

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><FooBar><Hello>World</Hello></FooBar>', $response->trace()->xmlRequest);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><Status>Success!</Status>', $response->trace()->xmlResponse);
    }

    /** @test */
    public function a_trace_can_be_requested_globally()
    {
        Soap::trace();

        $response = Soap::to(static::EXAMPLE_SOAP_ENDPOINT)
                        ->call('Add', ['intA' => 10, 'intB' => 25]);

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><FooBar><Hello>World</Hello></FooBar>', $response->trace()->xmlRequest);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><Status>Success!</Status>', $response->trace()->xmlResponse);
    }

    /** @test */
    public function by_default_the_trace_has_no_content_on_the_response()
    {
        $response = Soap::to(static::EXAMPLE_SOAP_ENDPOINT)
                        ->call('Add', ['intA' => 10, 'intB' => 25]);

        $this->assertEmpty($response->trace()->xmlRequest);
        $this->assertEmpty($response->trace()->xmlResponse);
    }
}
