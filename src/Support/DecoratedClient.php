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

    public function lastRequest(): ?string
    {
        return $this->client->__getLastRequest();
    }

    public function lastResponse(): ?string
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

    public function __get(string $name)
    {
        return $this->client->$name;
    }

    public function __call(string $name, array $arguments)
    {
        return $this->client->$name(...$arguments);
    }
}
