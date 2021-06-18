<?php

namespace RicorocksDigitalAgency\Soap\Tests\Tracing;

use RicorocksDigitalAgency\Soap\Facades\Soap;
use RicorocksDigitalAgency\Soap\Tests\TestCase;

class SoapTracingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeClient();
    }

    /** @test */
    public function aTraceCanBeRequestedAtTimeOfRequest()
    {
        $response = Soap::to(static::EXAMPLE_SOAP_ENDPOINT)
                        ->trace()
                        ->call('Add', ['intA' => 10, 'intB' => 25]);

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><FooBar><Hello>World</Hello></FooBar>', $response->trace()->xmlRequest);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><Status>Success!</Status>', $response->trace()->xmlResponse);
        $this->assertEquals('Hello World', $response->trace()->requestHeaders);
        $this->assertEquals('Foo Bar', $response->trace()->responseHeaders);
    }

    /** @test */
    public function aTraceCanBeRequestedGlobally()
    {
        Soap::trace();

        $response = Soap::to(static::EXAMPLE_SOAP_ENDPOINT)
                        ->call('Add', ['intA' => 10, 'intB' => 25]);

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><FooBar><Hello>World</Hello></FooBar>', $response->trace()->xmlRequest);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><Status>Success!</Status>', $response->trace()->xmlResponse);
        $this->assertEquals('Hello World', $response->trace()->requestHeaders);
        $this->assertEquals('Foo Bar', $response->trace()->responseHeaders);
    }

    /** @test */
    public function byDefaultTheTraceHasNoContentOnTheResponse()
    {
        $response = Soap::to(static::EXAMPLE_SOAP_ENDPOINT)
                        ->call('Add', ['intA' => 10, 'intB' => 25]);

        $this->assertEmpty($response->trace()->xmlRequest);
        $this->assertEmpty($response->trace()->xmlResponse);
        $this->assertEmpty($response->trace()->requestHeaders);
        $this->assertEmpty($response->trace()->responseHeaders);
    }
}
