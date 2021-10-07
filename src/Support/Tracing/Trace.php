<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap\Support\Tracing;

use InvalidArgumentException;
use RicorocksDigitalAgency\Soap\Contracts\Client;
use RicorocksDigitalAgency\Soap\Contracts\Traceable;

final class Trace
{
    public Client $client;
    public ?string $xmlRequest;
    public ?string $xmlResponse;
    public ?string $requestHeaders;
    public ?string $responseHeaders;

    /**
     * @throws InvalidArgumentException when the provided client does not implement the Traceable contract
     */
    public static function client(Client $client): self
    {
        if (!$client instanceof Traceable) {
            throw new InvalidArgumentException('A client must implement the Traceable contract before being traced.');
        }

        $trace = new static();

        $trace->client = $client;

        $trace->xmlRequest = $client->__getLastRequest();
        $trace->xmlResponse = $client->__getLastResponse();
        $trace->requestHeaders = $client->__getLastRequestHeaders();
        $trace->responseHeaders = $client->__getLastResponseHeaders();

        return $trace;
    }
}
