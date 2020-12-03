<?php

namespace RicorocksDigitalAgency\Soap;

use RicorocksDigitalAgency\Soap\Parameters\Node;
use RicorocksDigitalAgency\Soap\Request\Request;
use RicorocksDigitalAgency\Soap\Support\Fakery\Fakery;

class Soap
{
    protected Fakery $fakery;
    protected $inclusions = [];

    public function __construct(Fakery $fakery)
    {
        $this->fakery = $fakery;
    }

    public function to(string $endpoint)
    {
        return app(Request::class)
            ->beforeRequesting(fn($request) => $this->fakery->returnMockResponseIfAvailableNew($request))
            ->afterRequesting(fn($request, $response) => $this->fakery->record($request, $response))
            ->to($endpoint);
    }

    public function node($attributes = []): Node
    {
        return new Node($attributes);
    }

    public function include($parameters)
    {
        $inclusion = new Inclusion($parameters);
        $this->inclusions[] = $inclusion;
        return $inclusion;
    }

    public function inclusionsFor(string $endpoint, $method = null)
    {
        return collect($this->inclusions)->filter->matches($endpoint, $method);
    }

    public function fake($callback = null)
    {
        $this->fakery->fake($callback);
        return app(self::class);
    }

    public function assertNothingSent()
    {
        $this->fakery->assertNothingSent();
        return app(self::class);
    }

    public function assertSent(callable $callback)
    {
        $this->fakery->assertSent($callback);
        return app(self::class);
    }

    public function assertNotSent(callable $callback)
    {
        $this->fakery->assertNotSent($callback);
        return app(self::class);
    }

    public function assertSentCount($count)
    {
        $this->fakery->assertSentCount($count);
        return app(self::class);
    }
}
