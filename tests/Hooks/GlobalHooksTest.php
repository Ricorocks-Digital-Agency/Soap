<?php

namespace RicorocksDigitalAgency\Soap\Tests\Hooks;

use Exception;
use RicorocksDigitalAgency\Soap\Facades\Soap;
use RicorocksDigitalAgency\Soap\Response\Response;
use RicorocksDigitalAgency\Soap\Tests\TestCase;

class GlobalHooksTest extends TestCase
{
    public static $increment = 0;

    /** @test */
    public function it_can_perform_global_before_requesting_hooks()
    {
        Soap::fake(['http://endpoint.com' => Response::new(), 'http://foobar.com' => Response::new()]);

        Soap::beforeRequesting(fn() => static::$increment++);

        Soap::to('http://endpoint.com')->Test();
        Soap::to('http://foobar.com')->Test();

        $this->assertEquals(2, static::$increment);
    }

    /** @test */
    public function it_can_perform_global_after_requesting_hooks()
    {
        Soap::fake(['http://endpoint.com' => Response::new(), 'http://foobar.com' => Response::new()]);

        Soap::afterRequesting(fn() => static::$increment++);

        Soap::to('http://endpoint.com')->Test();
        Soap::to('http://foobar.com')->Test();

        $this->assertEquals(2, static::$increment);
    }

    /** @test */
    public function hooks_run_even_without_faking()
    {
        $this->expectException(Exception::class);
        Soap::beforeRequesting(function() { throw new Exception("Yippee! We hit this instead"); });
        Soap::to('http://endpoint.com')->Test();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        static::$increment = 0;
    }
}
