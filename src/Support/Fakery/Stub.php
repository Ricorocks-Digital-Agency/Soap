<?php

namespace RicorocksDigitalAgency\Soap\Support\Fakery;

use Closure;
use Illuminate\Support\Str;
use RicorocksDigitalAgency\Soap\Request\Request;

class Stub
{
    public $endpoint;
    public $methods;
    public $callback;

    public static function for($endpoint): self
    {
        $instance = app(self::class);
        $instance->register($endpoint);

        return $instance;
    }

    public function respondWith($callback): self
    {
        $this->callback = $callback;

        return $this;
    }

    public function generateResponse(Request $request)
    {
        return $this->callback instanceof Closure ? call_user_func($this->callback, $request) : $this->callback;
    }

    protected function register($endpoint)
    {
        if ($endpoint == '*') {
            $this->endpoint = '*';
            $this->methods = '*';
            return;
        }

        $this->extractEndpointAndMethods($endpoint);
    }

    protected function extractEndpointAndMethods($endpoint)
    {
        $this->endpoint = (string) Str::of($endpoint)->start('*')->replaceMatches("/:([\w\d]+$)/", "");
        $this->methods = (string) Str::of($endpoint)->afterLast(".")->match("/:([\w\d]+$)/")->start("*");
    }

    public function isForEndpoint($endpoint)
    {
        return Str::is($this->endpoint, '*') || Str::is($this->endpoint, $endpoint);
    }

    public function isForMethod($method)
    {
        return Str::is($this->methods, $method);
    }
}
