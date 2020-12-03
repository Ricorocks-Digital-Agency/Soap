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
        $callback = $this->callback;

        return $callback instanceof Closure ? $callback($request) : $callback;
    }

    protected function register($endpoint)
    {
        if ($endpoint == '*') {
            $this->endpoint = '*';
            $this->methods = '*';
            return;
        }

        $details = $this->extractEndpointAndMethods($endpoint);
        $this->endpoint = $details['endpoint'];
        $this->methods = $details['methods'];
    }

    protected function extractEndpointAndMethods($endpoint)
    {
        return [
            'endpoint' => (string) Str::of($endpoint)->start('*')->replaceMatches("/:([\w\d]+$)/", ""),
            'methods' => (string) Str::of($endpoint)->afterLast(".")->match("/:([\w\d]+$)/")->start("*")
        ];
    }

    public function isForEndpoint($endpoint)
    {
        return Str::is($this->endpoint, '*') || Str::is($this->endpoint, $endpoint);
    }

    public function isForMethod($method)
    {
        return $this->methods && Str::is($this->methods, $method);
    }
}
