<?php

namespace RicorocksDigitalAgency\Soap\Tests\Feature;

use RicorocksDigitalAgency\Soap\Providers\SoapServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [SoapServiceProvider::class];
    }
}
