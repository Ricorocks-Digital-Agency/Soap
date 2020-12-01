<?php


namespace RicorocksDigitalAgency\Soap;


use RicorocksDigitalAgency\Soap\Request\Request;
use SoapClient;

class SoapClientRequest implements Request
{
    protected SoapClient $client;

    public function __construct(string $endpoint)
    {
        $this->client = new SoapClient($endpoint);
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
        return $this->client->$method($parameters);
    }
}