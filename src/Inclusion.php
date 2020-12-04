<?php

namespace RicorocksDigitalAgency\Soap;

class Inclusion
{
    protected $parameters;
    protected $endpoint;
    protected $method = null;

    public function __construct($parameters)
    {
        $this->parameters = $parameters;
    }

    public function for($endpoint, $method = null)
    {
        $this->endpoint = $endpoint;
        $this->method = $method;
    }

    public function matches(string $endpoint, $method = null)
    {
        if ($this->endpoint !== $endpoint) {
            return false;
        }

        return empty($this->method) || $this->method === $method;
    }

    public function getParameters()
    {
        return $this->parameters;
    }
}
