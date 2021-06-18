<?php

namespace RicorocksDigitalAgency\Soap\Tests\Building;

use RicorocksDigitalAgency\Soap\Contracts\Soapable;
use RicorocksDigitalAgency\Soap\Facades\Soap;
use RicorocksDigitalAgency\Soap\Parameters\Builder;
use RicorocksDigitalAgency\Soap\Parameters\IntelligentBuilder;
use RicorocksDigitalAgency\Soap\Tests\TestCase;

class IntelligentBuilderTest extends TestCase
{
    protected Builder $builder;

    /** @test */
    public function itCanHandleAnArray()
    {
        $result = $this->builder->handle(['foo' => 'bar', 'baz' => 'huh?']);
        $this->assertEquals(['foo' => 'bar', 'baz' => 'huh?'], $result);
    }

    /** @test */
    public function itCanHandleANestedArray()
    {
        $result = $this->builder->handle(['foo' => ['bar' => ['foo', 'bar', 'baz']], 'baz' => 'huh?']);
        $this->assertEquals(['foo' => ['bar' => ['foo', 'bar', 'baz']], 'baz' => 'huh?'], $result);
    }

    /** @test */
    public function itCanHandleANode()
    {
        $result = $this->builder->handle(['foo' => Soap::node()->body(['bar' => 'baz']), 'baz' => 'huh?']);
        $this->assertEquals(['foo' => ['bar' => 'baz'], 'baz' => 'huh?'], $result);
    }

    /** @test */
    public function itCanHandleANodeWithOnlyAttributes()
    {
        $result = $this->builder->handle(['foo' => Soap::node(['foo' => 'bar', 'baz' => 'boom']), 'baz' => 'huh?']);
        $this->assertEquals(['foo' => ['_' => '', 'foo' => 'bar', 'baz' => 'boom'], 'baz' => 'huh?'], $result);
    }

    /** @test */
    public function itCanHandleANodeWithAttributesAndBody()
    {
        $result = $this->builder->handle(
            ['foo' => Soap::node(['foo' => 'bar', 'baz' => 'boom'])->body(['gee' => 'whiz']), 'baz' => 'huh?']
        );
        $this->assertEquals(['foo' => ['gee' => 'whiz', 'foo' => 'bar', 'baz' => 'boom'], 'baz' => 'huh?'], $result);
    }

    /** @test */
    public function itCanHandleNestedNodes()
    {
        $result = $this->builder->handle(
            [
                'foo' => Soap
                    ::node(['email' => 'hi@me.com'])
                    ->body(['bar' => Soap::node()->body(['hello' => 'world'])]),
            ]
        );
        $this->assertEquals(
            [
                'foo' => ['bar' => ['hello' => 'world'], 'email' => 'hi@me.com'],
            ],
            $result
        );
    }

    /** @test */
    public function itCanHandleASoapable()
    {
        $result = $this->builder->handle(new ExampleSoapable());

        $this->assertEquals(
            [
                'foo' => ['bar' => ['hello' => 'world'], 'email' => 'hi@me.com'],
                'bar' => ['baz', 'bang'],
            ],
            $result
        );
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = app(IntelligentBuilder::class);
    }
}

class ExampleSoapable implements Soapable
{
    public function toSoap()
    {
        return [
            'foo' => Soap
                ::node(['email' => 'hi@me.com'])
                ->body(['bar' => Soap::node()->body(['hello' => 'world'])]),
            'bar' => ['baz', 'bang'],
        ];
    }
}
