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
    protected $hooks = [
        'beforeRequesting' => [],
        'afterRequesting' => []
    ];

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
        return $this->call($name, $parameters[0]);
    }

    public function call($method, $parameters = [])
    {
        $this->method = $method;
        $this->body = $this->builder->handle($this->mergeInclusions($method, $parameters));

        $response = $this->getResponse();
        collect($this->hooks['afterRequesting'])->each(fn($callback) => $callback($this, $response));

        return $this->getResponse();
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
        return $this->runBeforeRequestingHooks($this->getMethod()) ?? new Response($this->makeRequest());
    }

    /**
     * @return Response|void
     */
    protected function runBeforeRequestingHooks($method)
    {
        $results = collect($this->hooks['beforeRequesting'])->map(fn($callback) => $callback($this));
        if ($response = $results->filter(fn($result) => $result instanceof Response)->first()) {
            return $response;
        }
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
        return $this->client ??= new SoapClient($this->endpoint);
    }

    public function getBody()
    {
        return $this->body;
    }

    public function functions(): array
    {
        return $this->client()->__getFunctions();
    }

    public function beforeRequesting(callable $closure): Request
    {
        $this->hooks['beforeRequesting'][] = $closure;
        return $this;
    }

    public function afterRequesting(callable $closure): Request
    {
        $this->hooks['afterRequesting'][] = $closure;
        return $this;
    }

    public function getEndpoint()
    {
        return $this->endpoint;
    }
}