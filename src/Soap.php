<?php


namespace RicorocksDigitalAgency\Soap;


use RicorocksDigitalAgency\Soap\Request\Request;

class Soap
{
    public function to(string $endpoint)
    {
        return app(Request::class, ['endpoint' => $endpoint]);
    }

}