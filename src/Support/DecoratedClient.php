<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap\Support;

use RicorocksDigitalAgency\Soap\Contracts\PhpSoap\Client;
use SoapClient;
use SoapHeader;

final class DecoratedClient implements Client
{
    private SoapClient $client;

    private string $endpoint = '';

    /**
     * @var array<string, mixed>
     */
    private array $options = [];

    /**
     * @param array<int, SoapHeader> $headers
     */
    public function setHeaders(array $headers): static
    {
        $this->client()->__setSoapHeaders($headers);

        return $this;
    }

    public function setEndpoint(string $endpoint): static
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * @param array<string, mixed> $options
     */
    public function setOptions(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function call(string $method, mixed $body): mixed
    {
        return $this->client()->{$method}($body);
    }

    public function getFunctions(): array
    {
        return $this->client()->__getFunctions();
    }

    public function __get(string $name): mixed
    {
        return $this->client()->$name;
    }

    /**
     * @param array<mixed> $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        return $this->client()->$name(...$arguments);
    }

    public function __getLastRequest(): ?string
    {
        return $this->client()->__getLastRequest();
    }

    public function __getLastResponse(): ?string
    {
        return $this->client()->__getLastResponse();
    }

    public function __getLastRequestHeaders(): ?string
    {
        return $this->client()->__getLastRequestHeaders();
    }

    public function __getLastResponseHeaders(): ?string
    {
        return $this->client()->__getLastResponseHeaders();
    }

    private function client(): SoapClient
    {
        return $this->client ??= new SoapClient($this->endpoint, $this->options);
    }
}
