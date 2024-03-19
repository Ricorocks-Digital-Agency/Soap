<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap\Tests\Mocks;

use RicorocksDigitalAgency\Soap\Contracts\PhpSoap\Client;

final class MockSoapClient implements Client
{
    public array $headers = [];
    public string $endpoint = '';
    public array $options = [];

    public function setHeaders(array $headers): static
    {
        $this->headers = $headers;

        return $this;
    }

    public function setEndpoint(string $endpoint): static
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    public function setOptions(array $options): static
    {
        $this->options = $options;

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

    public function __getTypes()
    {
    }

    public function __setCookie(string $name, ?string $value = null)
    {
    }

    public function __setLocation(?string $new_location = null)
    {
    }

    public function __setSoapHeaders($soapheaders)
    {
    }

    public function __soapCall(
        string $function_name,
        array $arguments,
        array $options = [],
        $input_headers = [],
        &$output_headers = []
    ) {
    }

    public function SoapClient(mixed $wsdl, array $options = [])
    {
        return new static($wsdl, $options);
    }
}
