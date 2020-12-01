<?php

namespace RicorocksDigitalAgency\Soap\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use RicorocksDigitalAgency\Soap\Providers\SoapServiceProvider;

abstract class TestCase extends OrchestraTestCase
{

    protected function getPackageProviders($app)
    {
        return [SoapServiceProvider::class];
    }

}