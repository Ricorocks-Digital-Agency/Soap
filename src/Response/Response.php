<?php


namespace RicorocksDigitalAgency\Soap\Response;


class Response
{
    protected $response;

    public function __construct($response = [])
    {
        $this->response = $response;
    }

}