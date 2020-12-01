<?php


namespace RicorocksDigitalAgency\Soap\Request;


use RicorocksDigitalAgency\Soap\Facades\Soap;
use RicorocksDigitalAgency\Soap\Parameters\Builder;
use SoapClient;

class SoapClientRequest implements Request
{
    protected string $endpoint;
    protected SoapClient $client;
    protected Builder $builder;

    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    public function to(string $endpoint): Request
    {
        $this->endpoint = $endpoint;
        $this->client = new SoapClient($this->endpoint);
        return $this;
    }

    public function __call($name, $parameters)
    {
        return $this->call($name, $parameters[0]);
    }

    public function call($method, $parameters = [])
    {
        return $this->client->$method($this->builder->handle($this->mergeInclusions($method, $parameters)));
    }

    protected function mergeInclusions($method, $parameters)
    {
        return Soap::inclusionsFor($this->endpoint, $method)
            ->flatMap
            ->getParameters()
            ->merge($parameters)
            ->toArray();
    }

    public function functions(): array
    {
        return $this->client->__getFunctions();
    }
}