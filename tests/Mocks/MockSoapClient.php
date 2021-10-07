<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap\Tests\Mocks;

use RicorocksDigitalAgency\Soap\Contracts\Client;
use RicorocksDigitalAgency\Soap\Contracts\Traceable;

final class MockSoapClient implements Client, Traceable
{
    public array $headers = [];

    public function setHeaders(array $headers): static
    {
        $this->headers = $headers;

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

    public function __getLastRequest(): ?string
    {
        return '<?xml version="1.0" encoding="UTF-8"?><FooBar><Hello>World</Hello></FooBar>';
    }

    public function __getLastResponse(): ?string
    {
        return '<?xml version="1.0" encoding="UTF-8"?><Status>Success!</Status>';
    }

    public function __getLastRequestHeaders(): ?string
    {
        return 'Hello World';
    }

    public function __getLastResponseHeaders(): ?string
    {
        return 'Foo Bar';
    }
}
