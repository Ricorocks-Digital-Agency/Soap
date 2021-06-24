<?php

namespace RicorocksDigitalAgency\Soap\Tests\Fake;

use RicorocksDigitalAgency\Soap\Facades\Soap;
use RicorocksDigitalAgency\Soap\Response\Response;
use RicorocksDigitalAgency\Soap\Tests\TestCase;

class MethodFakeTest extends TestCase
{
    /** @test */
    public function it_can_fake_specific_methods()
    {
        Soap::fake(['http://foobar.com' => Response::new(['baz' => 'boom'])]);
        Soap::fake(['http://foobar.com:Add' => Response::new(['foo' => 'bar'])]);

        Soap::to('http://foobar.com')->call('Add', ['intA' => 10, 'intB' => 20]);

        Soap::assertSent(
            fn ($request, $response) => $request->getMethod() == 'Add' && $response->response == ['foo' => 'bar']
        );
    }

    /** @test */
    public function it_can_fake_multiple_methods_declared_by_a_pipe_operator()
    {
        Soap::fake(['http://foobar.com:Multiply|Divide' => Response::new(['baz' => 'boom'])]);
        Soap::fake(['http://foobar.com:Add|Subtract' => Response::new(['foo' => 'bar'])]);

        Soap::to('http://foobar.com')->call('Add', ['intA' => 10, 'intB' => 20]);
        Soap::to('http://foobar.com')->Subtract(['intA' => 10, 'intB' => 20]);

        Soap::to('http://foobar.com')->Multiply(['intA' => 10, 'intB' => 20]);
        Soap::to('http://foobar.com')->Divide(['intA' => 10, 'intB' => 20]);

        Soap::assertSent(
            fn ($request, $response) => $request->getMethod() == 'Add' && $response->response == ['foo' => 'bar']
        );
        Soap::assertSent(
            fn ($request, $response) => $request->getMethod() == 'Subtract' && $response->response == ['foo' => 'bar']
        );
        Soap::assertSent(
            fn ($request, $response) => $request->getMethod() == 'Multiply' && $response->response == ['baz' => 'boom']
        );
        Soap::assertSent(
            fn ($request, $response) => $request->getMethod() == 'Divide' && $response->response == ['baz' => 'boom']
        );
    }

    /** @test */
    public function a_method_fake_will_take_precedence_over_other_fakes()
    {
        Soap::fake(['*' => Response::new(['wild' => 'card'])]);
        Soap::fake(['http://foobar.com' => Response::new(['baz' => 'boom'])]);
        Soap::fake(['http://foobar.com:Add' => Response::new(['foo' => 'bar'])]);
        Soap::fake(['http://foobar.com*' => Response::new(['gee' => 'whizz'])]);

        Soap::to('http://foobar.com')->call('Add', ['intA' => 10, 'intB' => 20]);

        Soap::assertSent(
            fn ($request, $response) => $request->getMethod() == 'Add' && $response->response == ['foo' => 'bar']
        );
    }
}
