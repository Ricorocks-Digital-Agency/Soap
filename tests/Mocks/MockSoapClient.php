<?php

namespace RicorocksDigitalAgency\Soap\Tests\Mocks;

use RicorocksDigitalAgency\Soap\Contracts\Client;

class MockSoapClient implements Client
{
    protected $shouldTrace = false;

    /**
     * @param array<string, mixed> $options
     */
    public function __construct(string $endpoint, array $options)
    {
        if ($options['trace'] ?? false) {
            $this->shouldTrace = true;
        }
    }

    public function setHeaders(array $headers): static
    {
        return $this;
    }

    public function call(string $method, mixed $body): mixed
    {
        return [];
    }

    public function getFunctions(): array
    {
        return [
            'The mock client does not actually have functions!',
        ];
    }

    public function lastRequest(): ?string
    {
        if (!$this->shouldTrace) {
            return null;
        }

        return '<?xml version="1.0" encoding="UTF-8"?><FooBar><Hello>World</Hello></FooBar>';
    }

    public function lastResponse(): ?string
    {
        if (!$this->shouldTrace) {
            return null;
        }

        return '<?xml version="1.0" encoding="UTF-8"?><Status>Success!</Status>';
    }

    public function lastRequestHeaders(): ?string
    {
        if (!$this->shouldTrace) {
            return null;
        }

        return 'Hello World';
    }

    public function lastResponseHeaders(): ?string
    {
        if (!$this->shouldTrace) {
            return null;
        }

        return 'Foo Bar';
    }
}
