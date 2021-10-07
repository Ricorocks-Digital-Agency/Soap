<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap\Support;

use RicorocksDigitalAgency\Soap\Contracts\Client;
use RicorocksDigitalAgency\Soap\Contracts\Traceable;
use SoapClient;

final class DecoratedClient implements Client, Traceable
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

    public function __getLastRequest(): ?string
    {
        return $this->client->__getLastRequest();
    }

    public function __getLastResponse(): ?string
    {
        return $this->client->__getLastResponse();
    }

    public function __getLastRequestHeaders(): ?string
    {
        return $this->client->__getLastRequestHeaders();
    }

    public function __getLastResponseHeaders(): ?string
    {
        return $this->client->__getLastResponseHeaders();
    }
}
