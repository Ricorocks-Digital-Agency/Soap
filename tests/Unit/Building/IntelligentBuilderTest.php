<?php

use RicorocksDigitalAgency\Soap\Parameters\IntelligentBuilder;

it('can handle an array')
    ->expect((new IntelligentBuilder())->handle(['foo' => 'bar', 'baz' => 'huh?']))
    ->toEqual(['foo' => 'bar', 'baz' => 'huh?']);

it('can handle a nested array')
    ->expect((new IntelligentBuilder())->handle(['foo' => ['bar' => ['foo', 'bar', 'baz']], 'baz' => 'huh?']))
    ->toEqual(['foo' => ['bar' => ['foo', 'bar', 'baz']], 'baz' => 'huh?']);

it('can handle a node')
    ->expect((new IntelligentBuilder())->handle(['foo' => soap()->node()->body(['bar' => 'baz']), 'baz' => 'huh?']))
    ->toEqual(['foo' => ['bar' => 'baz'], 'baz' => 'huh?']);

it('can handle a node with only attributes')
    ->expect((new IntelligentBuilder())->handle(['foo' => soap()->node(['foo' => 'bar', 'baz' => 'boom']), 'baz' => 'huh?']))
    ->toEqual(['foo' => ['_' => '', 'foo' => 'bar', 'baz' => 'boom'], 'baz' => 'huh?']);

it('can handle a node with attributes and body')
    ->expect((new IntelligentBuilder())->handle(['foo' => soap()->node(['foo' => 'bar', 'baz' => 'boom'])->body(['gee' => 'whiz']), 'baz' => 'huh?']))
    ->toEqual(['foo' => ['gee' => 'whiz', 'foo' => 'bar', 'baz' => 'boom'], 'baz' => 'huh?']);

it('can handle nested nodes')
    ->expect((new IntelligentBuilder())->handle(['foo' => soap()->node(['email' => 'hi@me.com'])->body(['bar' => soap()->node()->body(['hello' => 'world'])])]))
    ->toEqual(['foo' => ['bar' => ['hello' => 'world'], 'email' => 'hi@me.com']]);

it('can handle a Soapable')
    ->expect((new IntelligentBuilder())->handle(new ExampleSoapable()))
    ->toMatchArray([
        'foo' => ['bar' => ['hello' => 'world'], 'email' => 'hi@me.com'],
        'bar' => ['baz', 'bang'],
    ]);

it('can handle an object')
    ->expect((new IntelligentBuilder())->handle(new class {
        public $foo = 'bar';
        public $baz = 'huh?';
    }))
    ->toBeObject();