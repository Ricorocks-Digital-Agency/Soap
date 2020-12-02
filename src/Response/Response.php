<?php


namespace RicorocksDigitalAgency\Soap\Response;


class Response
{
    public $response;

    public function __construct($response = [])
    {
        $this->response = $response;
    }

}