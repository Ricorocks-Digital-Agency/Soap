<?php

namespace RicorocksDigitalAgency\Soap\Request;

use RicorocksDigitalAgency\Soap\Facades\Soap;
use RicorocksDigitalAgency\Soap\Parameters\Builder;
use RicorocksDigitalAgency\Soap\Response\Response;
use SoapClient;

class SoapClientRequest implements Request
{
    protected string $endpoint;
    protected string $method;
    protected $body;
    protected SoapClient $client;
    protected Builder $builder;
    protected $hooks = [];

    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    public function to(string $endpoint): Request
    {
        $this->endpoint = $endpoint;
        return $this;
    }

    public function __call($name, $parameters)
    {
        return $this->call($name, $parameters[0] ?? []);
    }

    public function call($method, $parameters = [])
    {
        $this->method = $method;
        $this->body = $this->builder->handle($this->mergeInclusions($method, $parameters));

        $response = $this->getResponse();
        $this->hooks['afterRequesting']->each(fn($callback) => $callback($this, $response));

        return $response;
    }

    protected function mergeInclusions($method, $parameters)
    {
        return Soap::inclusionsFor($this->endpoint, $method)
            ->flatMap
            ->getParameters()
            ->merge($parameters)
            ->toArray();
    }

    protected function getResponse()
    {
        return $this->runBeforeRequestingHooks()
            ?? Response::new($this->makeRequest())
                ->withXml($this->client()->__getLastRequest(), $this->client()->__getLastResponse());
    }

    /**
     * @return Response|void
     */
    protected function runBeforeRequestingHooks()
    {
        return $this->hooks['beforeRequesting']
            ->map(fn($callback) => $callback($this))
            ->filter(fn($result) => $result instanceof Response)
            ->first();
    }

    public function getMethod()
    {
        return $this->method;
    }

    protected function makeRequest()
    {
        return $this->client()->{$this->getMethod()}($this->getBody());
    }

    protected function client()
    {
        return $this->client ??= new SoapClient($this->endpoint, ['trace' => true]);
    }

    public function getBody()
    {
        return $this->body;
    }

    public function functions(): array
    {
        return $this->client()->__getFunctions();
    }

    public function beforeRequesting(...$closures): Request
    {
        ($this->hooks['beforeRequesting'] ??= collect())->push(...$closures);
        return $this;
    }

    public function afterRequesting(...$closures): Request
    {
        ($this->hooks['afterRequesting'] ??= collect())->push(...$closures);
        return $this;
    }

    public function getEndpoint()
    {
        return $this->endpoint;
    }
}
