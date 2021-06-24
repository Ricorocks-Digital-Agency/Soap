<?php

namespace RicorocksDigitalAgency\Soap\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use RicorocksDigitalAgency\Soap\Providers\SoapServiceProvider;
use RicorocksDigitalAgency\Soap\Response\Response;
use RicorocksDigitalAgency\Soap\Soap;
use RicorocksDigitalAgency\Soap\Support\Fakery\Fakery;
use RicorocksDigitalAgency\Soap\Support\Fakery\Stubs;
use RicorocksDigitalAgency\Soap\Tests\Mocks\MockSoapClient;
use Spatie\LaravelRay\RayServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    public const EXAMPLE_SOAP_ENDPOINT = 'http://www.dneonline.com/calculator.asmx?WSDL';

    protected function fakeClient()
    {
        $this->app->bind(\SoapClient::class, MockSoapClient::class);
    }

    protected function getPackageProviders($app)
    {
        return [RayServiceProvider::class, SoapServiceProvider::class];
    }
}
