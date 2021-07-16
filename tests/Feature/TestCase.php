<?php

namespace RicorocksDigitalAgency\Soap\Tests\Feature;

use Pest\PendingObjects\TestCall;
use RicorocksDigitalAgency\Soap\Providers\SoapServiceProvider;

/**
 * @mixin TestCall
 */
class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [SoapServiceProvider::class];
    }
}
