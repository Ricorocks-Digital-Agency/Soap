<?php


namespace RicorocksDigitalAgency\Soap\Tests\Options;


use RicorocksDigitalAgency\Soap\Facades\Soap;
use RicorocksDigitalAgency\Soap\Request\Request;
use RicorocksDigitalAgency\Soap\Request\SoapClientRequest;
use RicorocksDigitalAgency\Soap\Tests\TestCase;

class AuthTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function basic_auth_can_be_set()
    {
        Soap::fake();

        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)
            ->withBasicAuth('hello', 'world')
            ->call('Add', ['intA' => 10, 'intB' => 25]);

        Soap::assertSent(function(SoapClientRequest $request, $response) {
            return $request->getOptions() == [
                    'authentication' => SOAP_AUTHENTICATION_BASIC,
                    'login' => 'hello',
                    'password' => 'world',
                ];
        });
    }

    /** @test */
    public function digest_auth_can_be_set()
    {
        Soap::fake();

        Soap::to(static::EXAMPLE_SOAP_ENDPOINT)
            ->withDigestAuth('hello', 'world')
            ->call('Add', ['intA' => 10, 'intB' => 25]);

        Soap::assertSent(function(SoapClientRequest $request, $response) {
            return $request->getOptions() == [
                    'authentication' => SOAP_AUTHENTICATION_DIGEST,
                    'login' => 'hello',
                    'password' => 'world',
                ];
        });
    }

}
