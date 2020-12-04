<?php

namespace RicorocksDigitalAgency\Soap\Tests\Hooks;

use RicorocksDigitalAgency\Soap\Facades\Soap;
use RicorocksDigitalAgency\Soap\Request\Request;
use RicorocksDigitalAgency\Soap\Response\Response;
use RicorocksDigitalAgency\Soap\Tests\TestCase;

class LocalHooksTest extends TestCase
{
    protected $counter = [];

    /** @test */
    public function it_can_perform_local_before_requesting_hooks()
    {
        Soap::fake(['http://endpoint.com' => Response::new(), 'http://foobar.com' => Response::new()]);

        $this->counter['endpoint'] = 0;
        $this->coutner['foobar'] = 0;

        Soap::to('http://endpoint.com')->beforeRequesting(fn() => $this->counter['endpoint'] = 5)->Test();
        Soap::to('http://foobar.com')->beforeRequesting(fn() => $this->counter['foobar'] = 2)->Test();

        $this->assertEquals(5, $this->counter['endpoint']);
        $this->assertEquals(2, $this->counter['foobar']);
    }

    /** @test */
    public function it_can_perform_local_after_requesting_hooks()
    {
        Soap::fake(['http://endpoint.com' => Response::new(), 'http://foobar.com' => Response::new()]);

        $this->counter['endpoint'] = 0;
        $this->coutner['foobar'] = 0;

        Soap::to('http://endpoint.com')->afterRequesting(fn() => $this->counter['endpoint'] = 5)->Test();
        Soap::to('http://foobar.com')->afterRequesting(fn() => $this->counter['foobar'] = 2)->Test();

        $this->assertEquals(5, $this->counter['endpoint']);
        $this->assertEquals(2, $this->counter['foobar']);
    }

    /** @test */
    public function hooks_can_be_chained()
    {
        Soap::fake(['http://endpoint.com' => Response::new()]);

        $this->counter['before'] = 0;
        $this->counter['after'] = 0;

        Soap::to('http://endpoint.com')
            ->beforeRequesting(fn() => $this->counter['before'] = 5)
            ->afterRequesting(fn() => $this->counter['after'] = 2)
            ->Test();

        $this->assertEquals(5, $this->counter['before']);
        $this->assertEquals(2, $this->counter['after']);
    }

    /** @test */
    public function the_before_hook_has_access_to_the_request()
    {
        Soap::fake(['http://endpoint.com' => Response::new()]);

        Soap::to('http://endpoint.com')
            ->beforeRequesting(fn(Request $request) => $this->assertEquals('http://endpoint.com', $request->getEndpoint()))
            ->Test();
    }

    /** @test */
    public function the_after_hook_has_access_to_the_request_and_the_response()
    {
        Soap::fake(['http://endpoint.com' => Response::new(['foo' => 'bar'])]);

        Soap::to('http://endpoint.com')
            ->afterRequesting(fn(Request $request) => $this->assertEquals('http://endpoint.com', $request->getEndpoint()))
            ->afterRequesting(fn(Request $request, Response $response) => $this->assertEquals('bar', $response->foo))
            ->Test();
    }

    /** @test */
    public function the_beforeRequesting_hooks_can_transform_the_request_object()
    {
        Soap::fake();

        Soap::to('http://endpoint.com')
            ->beforeRequesting(fn(Request $request) => $request->set('hello.world', ['foo', 'bar']))
            ->beforeRequesting(fn(Request $request) => $request->set('hello.person', 'Richard'))
            ->Test();

        Soap::assertSent(fn($request) => $request->getBody()['hello'] === ['world' => ['foo', 'bar'], 'person' => 'Richard']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->counter = [];
    }
}
