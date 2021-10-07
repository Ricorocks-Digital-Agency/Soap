<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap\Support\Tracing;

use RicorocksDigitalAgency\Soap\Contracts\PhpSoap\Client;

final class Trace
{
    public Client $client;
    public ?string $xmlRequest;
    public ?string $xmlResponse;
    public ?string $requestHeaders;
    public ?string $responseHeaders;

    public static function client(Client $client): self
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
