<?php

namespace RicorocksDigitalAgency\Soap;

use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Support\Traits\Macroable;
use RicorocksDigitalAgency\Soap\Parameters\Node;
use RicorocksDigitalAgency\Soap\Request\Request;
use RicorocksDigitalAgency\Soap\Support\Fakery\Fakery;

/**
 * @mixin Fakery
 */
class Soap
{
    use ForwardsCalls;
    use Macroable {
        __call as __macroableCall;
    }

    protected Fakery $fakery;
    protected Request $request;

    protected $headerSets = [];
    protected $inclusions = [];
    protected $optionsSets = [];
    protected $globalHooks = [];

    public function __construct(Fakery $fakery, Request $request)
    {
        $this->fakery = $fakery;
        $this->request = $request;

        $this->beforeRequesting(fn ($requestInstance) => $requestInstance->fakeUsing($this->fakery->mockResponseIfAvailable($requestInstance)))
            ->beforeRequesting(fn ($requestInstance) => $this->mergeHeadersFor($requestInstance))
            ->beforeRequesting(fn ($requestInstance) => $this->mergeInclusionsFor($requestInstance))
            ->beforeRequesting(fn ($requestInstance) => $this->mergeOptionsFor($requestInstance))
            ->afterRequesting(fn ($requestInstance, $response) => $this->record($requestInstance, $response));
    }

    public function to(string $endpoint)
    {
        return (clone $this->request)
            ->beforeRequesting(...$this->globalHooks['beforeRequesting'])
            ->afterRequesting(...$this->globalHooks['afterRequesting'])
            ->to($endpoint);
    }

    public function node($attributes = []): Node
    {
        return new Node($attributes);
    }

    public function header(?string $name = null, ?string $namespace = null, $data = null, bool $mustUnderstand = false, $actor = null): Header
    {
        return new Header($name, $namespace, $data, $mustUnderstand, $actor);
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

    public function headers(Header ...$headers)
    {
        $headers = new HeaderSet(...$headers);
        $this->headerSets[] = $headers;

        return $headers;
    }

    protected function mergeHeadersFor(Request $request)
    {
        collect($this->headerSets)
            ->filter
            ->matches($request->getEndpoint(), $request->getMethod())
            ->flatMap
            ->getHeaders()
            ->pipe(fn ($headers) => $request->withHeaders(...$headers));
    }

    protected function mergeInclusionsFor(Request $request)
    {
        collect($this->inclusions)
            ->filter
            ->matches($request->getEndpoint(), $request->getMethod())
            ->flatMap
            ->getParameters()
            ->each(fn ($value, $key) => $request->set($key, $value));
    }

    protected function mergeOptionsFor(Request $request)
    {
        collect($this->optionsSets)
            ->filter
            ->matches($request->getEndpoint(), $request->getMethod())
            ->map
            ->getOptions()
            ->each(fn ($options) => $request->withOptions($options));
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
        $this->beforeRequesting(fn ($request) => $request->trace($shouldTrace));

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
