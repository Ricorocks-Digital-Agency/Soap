<?php

namespace RicorocksDigitalAgency\Soap\Support\Tracing;

use Soap\ExtSoapEngine\Transport\TraceableTransport;

class Trace
{
    public $xmlRequest;
    public $xmlResponse;
    public $requestHeaders;
    public $responseHeaders;

    public static function transport(TraceableTransport $transport): self
    {
        $lastRequest = $transport->collectLastRequestInfo();

        $trace = new static();
        $trace->xmlRequest = $lastRequest->getLastRequest();
        $trace->xmlResponse = $lastRequest->getLastResponse();
        $trace->requestHeaders = $lastRequest->getLastRequestHeaders();
        $trace->responseHeaders = $lastRequest->getLastResponseHeaders();

        return $trace;
    }
}
