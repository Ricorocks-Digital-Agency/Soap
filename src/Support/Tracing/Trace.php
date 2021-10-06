<?php

namespace RicorocksDigitalAgency\Soap\Support\Tracing;

class Trace
{
    public $client;
    public $xmlRequest;
    public $xmlResponse;
    public $requestHeaders;
    public $responseHeaders;

    public static function client($client): self
    {
        $trace = new static();
        $trace->client = $client;
        $trace->xmlRequest = $client->__getLastRequest();
        $trace->xmlResponse = $client->__getLastResponse();
        $trace->requestHeaders = $client->__getLastRequestHeaders();
        $trace->responseHeaders = $client->__getLastResponseHeaders();

        return $trace;
    }
}
