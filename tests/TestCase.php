<?php

namespace RicorocksDigitalAgency\Soap\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use RicorocksDigitalAgency\Soap\Providers\SoapServiceProvider;
use RicorocksDigitalAgency\Soap\Tests\Mocks\MockSoapClient;

abstract class TestCase extends OrchestraTestCase
{
    const EXAMPLE_SOAP_ENDPOINT = "http://www.dneonline.com/calculator.asmx?WSDL";

    protected function fakeClient()
    {
        $this->app->bind(\SoapClient::class, MockSoapClient::class);
    }

    protected function getPackageProviders($app)
    {
        return [SoapServiceProvider::class];
    }

}