<?php


namespace RicorocksDigitalAgency\Soap;


use RicorocksDigitalAgency\Soap\Parameters\Node;
use RicorocksDigitalAgency\Soap\Request\Request;

class Soap
{
    protected $inclusions = [];

    public function to(string $endpoint)
    {
        return app(Request::class)->to($endpoint);
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
}