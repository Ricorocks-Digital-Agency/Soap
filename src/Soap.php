<?php

namespace RicorocksDigitalAgency\Soap;

use Illuminate\Support\Traits\ForwardsCalls;
use RicorocksDigitalAgency\Soap\Parameters\Node;
use RicorocksDigitalAgency\Soap\Request\Request;
use RicorocksDigitalAgency\Soap\Support\Fakery\Fakery;

class Soap
{
    use ForwardsCalls;

    protected Fakery $fakery;
    protected $inclusions = [];
    protected $globalHooks = [];

    public function __construct(Fakery $fakery)
    {
        $this->fakery = $fakery;
        $this->beforeRequesting(fn($request) => $this->fakery->returnMockResponseIfAvailable($request));
        $this->afterRequesting(fn($request, $response) => $this->record($request, $response));
    }

    public function to(string $endpoint)
    {
        return app(Request::class)
            ->beforeRequesting(...$this->globalHooks['beforeRequesting'])
            ->afterRequesting(...$this->globalHooks['afterRequesting'])
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

    public function beforeRequesting(callable $hook)
    {
        ($this->globalHooks['beforeRequesting'] ??= collect())->push($hook);
        return $this;
    }

    public function afterRequesting(callable $hook)
    {
        ($this->globalHooks['afterRequesting'] ??= collect())->push($hook);
        return $this;
    }

    public function __call($method, $parameters)
    {
        return $this->forwardCallTo($this->fakery, $method, $parameters);
    }
}
