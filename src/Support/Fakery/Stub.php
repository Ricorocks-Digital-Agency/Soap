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

        [$endpoint, $methods] = $this->extractEndpointAndMethods($endpoint);
        $this->endpoint = $endpoint;
        $this->methods = $methods;
    }

    protected function extractEndpointAndMethods($endpoint)
    {
        return [
            'endpoint' => Str::of($endpoint)->start('*')->replaceMatches("/:([\w\d]+$)/", ""),
            'methods' => Str::of($endpoint)->afterLast(".")->match("/:([\w\d]+$)/")->start("*")
        ];
    }
}
