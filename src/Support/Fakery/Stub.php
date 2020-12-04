<?php

namespace RicorocksDigitalAgency\Soap\Support\Fakery;

use Closure;
use Illuminate\Support\Str;
use RicorocksDigitalAgency\Soap\Request\Request;

class Stub
{
    const REGEX_PATTERN = "/:([\w\d|]+$)/";

    public $endpoint;
    public $methods;
    public $callback;

    public static function for($endpoint): self
    {
        return tap(new static, fn($instance) => $instance->register($endpoint));
    }

    public function respondWith($callback): self
    {
        $this->callback = $callback;

        return $this;
    }

    public function getResponse(Request $request)
    {
        return $this->callback instanceof Closure
            ? call_user_func($this->callback, $request)
            : $this->callback;
    }

    protected function register($endpoint)
    {
        $this->endpoint = Str::of($endpoint)->replaceMatches(self::REGEX_PATTERN, "")->start('*')->__toString();
        $this->methods = Str::of($endpoint)->afterLast(".")->match(self::REGEX_PATTERN)->start('*')->__toString();
    }

    public function isForEndpoint($endpoint)
    {
        return Str::is($this->endpoint, $endpoint);
    }

    public function isForMethod($method)
    {
        return Str::of($this->methods)
                ->explode('|')
                ->map(fn($availableMethod) => Str::start($availableMethod, '*'))
                ->contains(fn($availableMethod) => Str::is($availableMethod, $method));
    }

    public function hasWildcardMethods()
    {
        return Str::is($this->methods, '*');
    }
}
