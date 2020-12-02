<?php


namespace RicorocksDigitalAgency\Soap\Response;


class Response
{
    public $response;

    public function __construct($response = [])
    {
        $this->response = $response;
    }

    public function __get($name)
    {
        return data_get($this->response, $name);
    }

}