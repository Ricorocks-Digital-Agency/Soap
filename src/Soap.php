<?php

namespace RicorocksDigitalAgency\Soap;

use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Support\Traits\Macroable;
use RicorocksDigitalAgency\Soap\Parameters\Node;
use RicorocksDigitalAgency\Soap\Request\Request;
use RicorocksDigitalAgency\Soap\Support\Fakery\Fakery;

class Soap
{
    use ForwardsCalls;
    use Macroable {
        __call as __macroableCall;
    }

    protected Fakery $fakery;
    protected $inclusions = [];
    protected $optionsSets = [];
    protected $globalHooks = [];

    public function __construct(Fakery $fakery)
    {
        $this->fakery = $fakery;
        $this->beforeRequesting(fn($request) => $request->fakeUsing($this->fakery->mockResponseIfAvailable($request)));
        $this->beforeRequesting(fn($request) => $this->mergeInclusionsFor($request));
        $this->beforeRequesting(fn($request) => $this->mergeOptionsFor($request));
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

    public function header(?string $name = null, ?string $namespace = null, $data = null): Header
    {
        return new Header($name, $namespace, $data);
    }

    public function include($parameters)
    {
        $inclusion = new Inclusion($parameters);
        $this->inclusions[] = $inclusion;
        return $inclusion;
    }

    public function options(array $options)
    {
        $options = new OptionSet($options);
        $this->optionsSets[] = $options;
        return $options;
    }

    protected function mergeInclusionsFor(Request $request)
    {
        collect($this->inclusions)
            ->filter
            ->matches($request->getEndpoint(), $request->getMethod())
            ->flatMap
            ->getParameters()
            ->each(fn($value, $key) => $request->set($key, $value));
    }

    protected function mergeOptionsFor(Request $request)
    {
        collect($this->optionsSets)
            ->filter
            ->matches($request->getEndpoint(), $request->getMethod())
            ->map
            ->getOptions()
            ->each(fn($options) => $request->withOptions($options));
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

    public function trace($shouldTrace = true)
    {
        $this->beforeRequesting(fn($request) => $request->trace($shouldTrace));
        return $this;
    }

    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->__macroableCall($method, $parameters);
        }

        return $this->forwardCallTo($this->fakery, $method, $parameters);
    }
}
