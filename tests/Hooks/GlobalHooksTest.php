<?php

namespace RicorocksDigitalAgency\Soap\Tests\Hooks;

use Exception;
use RicorocksDigitalAgency\Soap\Facades\Soap;
use RicorocksDigitalAgency\Soap\Request\Request;
use RicorocksDigitalAgency\Soap\Response\Response;
use RicorocksDigitalAgency\Soap\Tests\TestCase;

class GlobalHooksTest extends TestCase
{
    public static $increment = 0;

    /** @test */
    public function itCanPerformGlobalBeforeRequestingHooks()
    {
        Soap::fake(['http://endpoint.com' => Response::new(), 'http://foobar.com' => Response::new()]);

        Soap::beforeRequesting(fn () => static::$increment++);

        Soap::to('http://endpoint.com')->Test();
        Soap::to('http://foobar.com')->Test();

        $this->assertEquals(2, static::$increment);
    }

    /** @test */
    public function itCanPerformGlobalAfterRequestingHooks()
    {
        Soap::fake(['http://endpoint.com' => Response::new(), 'http://foobar.com' => Response::new()]);

        Soap::afterRequesting(fn () => static::$increment++);

        Soap::to('http://endpoint.com')->Test();
        Soap::to('http://foobar.com')->Test();

        $this->assertEquals(2, static::$increment);
    }

    /** @test */
    public function hooksRunEvenWithoutFaking()
    {
        $this->expectException(Exception::class);
        Soap::beforeRequesting(function () { throw new Exception('Yippee! We hit this instead'); });
        Soap::to('http://endpoint.com')->Test();
    }

    /** @test */
    public function theBeforeRequestingHooksCanTransformTheRequestObject()
    {
        Soap::fake();

        Soap::beforeRequesting(fn (Request $request) => $request->set('hello.world', ['foo', 'bar']));
        Soap::beforeRequesting(fn (Request $request) => $request->set('hello.person', 'Richard'));
        Soap::to('http://endpoint.com')->Test();

        Soap::assertSent(fn ($request) => $request->getBody()['hello'] === ['world' => ['foo', 'bar'], 'person' => 'Richard']);
    }

    /** @test */
    public function theAfterRequestingHooksCanTransformTheResponseObject()
    {
        Soap::fake();

        Soap::afterRequesting(fn ($request, Response $response) => $response->set('hello.world', ['foo', 'bar']));
        Soap::afterRequesting(fn ($request, Response $response) => $response->set('hello.person', 'Richard'));
        Soap::to('http://endpoint.com')->Test();

        Soap::assertSent(fn ($request, Response $response) => $response->response['hello'] === ['world' => ['foo', 'bar'], 'person' => 'Richard']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        static::$increment = 0;
    }
}
