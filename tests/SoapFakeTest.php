<?php


namespace RicorocksDigitalAgency\Soap\Tests;


use RicorocksDigitalAgency\Soap\Facades\Soap;

class SoapFakeTest extends TestCase
{

    /** @test */
    public function it_can_record_requests()
    {
        Soap::fake();
        Soap::assertNothingSent();

        Soap::to(self::EXAMPLE_SOAP_ENDPOINT)->call('Add', ['intA' => 10, 'intB' => 20]);
        Soap::assertSentCount(1);
    }

    /** @test */
    public function it_can_fake_requests()
    {
        Soap::fake();
        Soap::to(self::EXAMPLE_SOAP_ENDPOINT)->call('Bob', ['intA' => 10, 'intB' => 20]);
        Soap::assertSentCount(1);
    }

}