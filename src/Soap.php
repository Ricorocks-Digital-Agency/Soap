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

        $this->beforeRequesting(function($requestInstance){return $requestInstance->fakeUsing($this->fakery->mockResponseIfAvailable($requestInstance));})
            ->beforeRequesting(function($requestInstance){return $this->mergeHeadersFor($requestInstance);})
            ->beforeRequesting(function($requestInstance){return $this->mergeInclusionsFor($requestInstance);})
            ->beforeRequesting(function($requestInstance){return $this->mergeOptionsFor($requestInstance);})
            ->afterRequesting(function($requestInstance,$response){return $this->record($requestInstance, $response);});
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
            ->pipe(function($headers) use ($request){
                return $request->withHeaders(...$headers);
            });
    }

    protected function mergeInclusionsFor(Request $request)
    {
        collect($this->inclusions)
            ->filter
            ->matches($request->getEndpoint(), $request->getMethod())
            ->flatMap
            ->getParameters()
            ->each(function($value, $key) use ($request) {
                return $request->set($key, $value);
            });
    }

    protected function mergeOptionsFor(Request $request)
    {
        collect($this->optionsSets)
            ->filter
            ->matches($request->getEndpoint(), $request->getMethod())
            ->map
            ->getOptions()
            ->each(function($options) use ($request) {
                return $request->withOptions($options);
            });
    }

    public function beforeRequesting(callable $hook)
    {
        if(is_null($this->globalHooks['beforeRequesting']))
            (collect())->push($hook);
        else
        ($this->globalHooks['beforeRequesting'])->push($hook);

        return $this;
    }

    public function afterRequesting(callable $hook)
    {
        if(is_null($this->globalHooks['afterRequesting']))
            (collect())->push($hook);
        else
            ($this->globalHooks['afterRequesting'])->push($hook);

        return $this;
    }

    public function trace($shouldTrace = true)
    {
        $this->beforeRequesting(function($request) use ($shouldTrace) {
            return $request->trace($shouldTrace);
        });

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
