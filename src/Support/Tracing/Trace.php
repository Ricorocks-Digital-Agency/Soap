<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap\Support\Tracing;

use RicorocksDigitalAgency\Soap\Contracts\Client;

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
        $trace->xmlRequest = $client->lastRequestAsXml();
        $trace->xmlResponse = $client->lastResponseAsXml();
        $trace->requestHeaders = $client->lastRequestHeaders();
        $trace->responseHeaders = $client->lastResponseHeaders();

        return $trace;
    }
}
