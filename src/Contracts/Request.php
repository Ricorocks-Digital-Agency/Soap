<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap\Contracts;

use RicorocksDigitalAgency\Soap\Response\Response;
use RicorocksDigitalAgency\Soap\Support\Header;

interface Request
{
    public function to(string $endpoint): self;

    /**
     * @param array<mixed> $parameters
     */
    public function __call(string $name, array $parameters);

    /**
     * @return array<int, string>
     */
    public function functions(): array;

    /**
     * @param array<string, mixed>|Soapable $body
     */
    public function call(string $method, array|Soapable $body = []): Response;

    /**
     * @param callable(Request): mixed ...$closures
     */
    public function beforeRequesting(callable ...$closures): self;

    /**
     * @param callable(Request, Response): mixed ...$closures
     */
    public function afterRequesting(callable ...$closures): self;

    /**
     * @param callable(Request): Response|Response|null $response
     */
    public function fakeUsing(callable|Response|null $response): self;

    public function getEndpoint(): string;

    public function getMethod(): string;

    /**
     * @return array<string, mixed>|Soapable
     */
    public function getBody(): array|Soapable;

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array;

    public function set(string $key, mixed $value): self;

    public function trace(bool $shouldTrace = true): self;

    /**
     * @param array<string, mixed> $options
     */
    public function withOptions(array $options): self;

    public function withBasicAuth(string $login, string $password): self;

    public function withDigestAuth(string $login, string $password): self;

    public function withHeaders(Header ...$headers): self;

    /**
     * @return array<int, Header>
     */
    public function getHeaders(): array;
}
