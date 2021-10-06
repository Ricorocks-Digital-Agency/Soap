<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap\Support;

use RicorocksDigitalAgency\Soap\Contracts\Client;
use SoapClient;

final class DecoratedClient implements Client
{
    public function __construct(private SoapClient $client)
    {
    }

    public function setHeaders(array $headers): static
    {
        $this->client->__setSoapHeaders($headers);

        return $this;
    }

    public function call(string $method, mixed $body): mixed
    {
        return $this->client->{$method}($body);
    }

    public function getFunctions(): array
    {
        return $this->client->__getFunctions();
    }

    public function lastRequestAsXml(): ?string
    {
        return $this->client->__getLastRequest();
    }

    public function lastResponseAsXml(): ?string
    {
        return $this->client->__getLastResponse();
    }

    public function lastRequestHeaders(): ?string
    {
        return $this->client->__getLastRequestHeaders();
    }

    public function lastResponseHeaders(): ?string
    {
        return $this->client->__getLastResponseHeaders();
    }

    public function __get(string $name): mixed
    {
        return $this->client->$name;
    }

    /**
     * @param array<mixed> $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        return $this->client->$name(...$arguments);
    }
}
