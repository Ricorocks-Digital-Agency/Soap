<?php

namespace RicorocksDigitalAgency\Soap\Tests;

use Exception;
use RicorocksDigitalAgency\Soap\Facades\Soap;

class MacroableTest extends TestCase
{
    /** @test */
    public function soapIsMacroable()
    {
        $this->expectExceptionObject(new Exception('You sucessfully called this!'));

        Soap::macro('test', function () {
            throw new Exception('You sucessfully called this!');
        });

        Soap::test();

        $this->fail('An exception should have been thrown!');
    }
}
