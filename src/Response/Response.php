<?php

namespace RicorocksDigitalAgency\Soap\Response;

class Response
{
    public $response;
    public $xmlRequest = null;
    public $xmlResponse = null;

    public static function new($response = []): self
    {
        return tap(new static, fn($instance) => $instance->response = $response);
    }

    public function __get($name)
    {
        return data_get($this->response, $name);
    }

    public function withXml($xmlRequest, $xmlResponse)
    {
        $this->xmlRequest = $xmlRequest;
        $this->xmlResponse = $xmlResponse;
        return $this;
    }
}
