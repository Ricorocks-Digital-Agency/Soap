<?php


namespace RicorocksDigitalAgency\Soap;


use RicorocksDigitalAgency\Soap\Parameters\Builder;
use RicorocksDigitalAgency\Soap\Request\Request;
use SoapClient;

class SoapClientRequest implements Request
{
    protected SoapClient $client;
    protected Builder $builder;

    public function __construct(string $endpoint, Builder $builder)
    {
        $this->client = new SoapClient($endpoint);
        $this->builder = $builder;
    }

    public function __call($name, $parameters)
    {
        return $this->call($name, $parameters[0]);
    }

    public function functions(): array
    {
        return $this->client->__getFunctions();
    }

    public function call($method, $parameters = [])
    {
        return $this->client->$method($this->builder->handle($parameters));
    }
}