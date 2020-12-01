<?php


namespace RicorocksDigitalAgency\Soap;


use RicorocksDigitalAgency\Soap\Parameters\Node;
use RicorocksDigitalAgency\Soap\Request\Request;

class Soap
{
    public function to(string $endpoint)
    {
        return app(Request::class, ['endpoint' => $endpoint]);
    }

    public function node($attributes = []): Node
    {
        return new Node($attributes);
    }

}