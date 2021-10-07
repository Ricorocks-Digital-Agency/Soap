<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap\Request;

use RicorocksDigitalAgency\Soap\Contracts\Builder;
use RicorocksDigitalAgency\Soap\Contracts\PhpSoap\Client;
use RicorocksDigitalAgency\Soap\Contracts\Request;
use RicorocksDigitalAgency\Soap\Contracts\Soapable;
use RicorocksDigitalAgency\Soap\Header;
use RicorocksDigitalAgency\Soap\Response\Response;
use RicorocksDigitalAgency\Soap\Support\Tracing\Trace;
use SoapHeader;

final class SoapPhpRequest implements Request
{
    private Builder $builder;

    private Client $client;

    private string $endpoint;

    private string $method;

    /**
     * @var array<string, mixed>|Soapable
     */
    private $body = [];

    private Response $response;

    /**
     * @var array{beforeRequesting: array<int, callable(Request): mixed>, afterRequesting: array<int, callable(Request, Response): mixed>}
     */
    private array $hooks = [
        'beforeRequesting' => [],
        'afterRequesting' => [],
    ];

    /**
     * @var array<string, mixed>
     */
    private array $options = [];

    /**
     * @var array<int, Header>
     */
    private array $headers = [];

    public function __construct(Builder $builder, Client $client)
    {
        $this->builder = $builder;
        $this->client = $client;
    }

    public function to(string $endpoint): self
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * @param array<mixed> $parameters
     */
    public function __call(string $name, array $parameters): mixed
    {
        return $this->call($name, $parameters[0] ?? []);
    }

    /**
     * @param array<string, mixed>|Soapable $body
     */
    public function call(string $method, array|Soapable $body = []): Response
    {
        $this->method = $method;
        $this->body = $body;

        collect($this->hooks['beforeRequesting'])->each(fn ($callback) => $callback($this));
        $this->body = $this->builder->handle($this->body);

        $response = $this->getResponse();
        collect($this->hooks['afterRequesting'])->each(fn ($callback) => $callback($this, $response));

        return $response;
    }

    private function getResponse(): Response
    {
        return $this->response ??= $this->getRealResponse();
    }

    private function getRealResponse(): Response
    {
        return tap(
            Response::new($this->makeRequest()),
            fn ($response) => data_get($this->options, 'trace')
                ? $response->setTrace(Trace::client($this->client()))
                : $response
        );
    }

    private function makeRequest(): mixed
    {
        return $this->client()->call($this->getMethod(), $this->getBody());
    }

    private function client(): Client
    {
        return $this->client
            ->setEndpoint($this->endpoint)
            ->setOptions($this->options)
            ->setHeaders($this->constructHeaders());
    }

    /**
     * @return array<int, SoapHeader>
     */
    private function constructHeaders(): array
    {
        if (empty($this->headers)) {
            return [];
        }

        return array_map(fn ($header) => new SoapHeader(
            $header->namespace,
            $header->name,
            $header->data,
            $header->mustUnderstand,
            $header->actor ?? SOAP_ACTOR_NONE
        ), $this->headers);
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return array<string, mixed>|Soapable
     */
    public function getBody(): array|Soapable
    {
        return $this->body;
    }

    /**
     * @return array<int, string>
     */
    public function functions(): array
    {
        return $this->client()->getFunctions();
    }

    /**
     * @param callable(Request): mixed ...$closures
     */
    public function beforeRequesting(callable ...$closures): self
    {
        $this->hooks['beforeRequesting'] = array_merge($this->hooks['beforeRequesting'], $closures);

        return $this;
    }

    /**
     * @param callable(Request, Response): mixed ...$closures
     */
    public function afterRequesting(callable ...$closures): self
    {
        $this->hooks['afterRequesting'] = array_merge($this->hooks['afterRequesting'], $closures);

        return $this;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * @param callable(Request): Response|Response|null $response
     */
    public function fakeUsing(Response|callable|null $response): self
    {
        if (empty($response)) {
            return $this;
        }

        $this->response = $response instanceof Response ? $response : $response($this);

        return $this;
    }

    public function set(string $key, mixed $value): self
    {
        data_set($this->body, $key, $value);

        return $this;
    }

    public function trace(bool $shouldTrace = true): self
    {
        $this->options['trace'] = $shouldTrace;

        return $this;
    }

    public function withBasicAuth(string $login, string $password): self
    {
        $this->options['authentication'] = SOAP_AUTHENTICATION_BASIC;
        $this->options['login'] = $login;
        $this->options['password'] = $password;

        return $this;
    }

    public function withDigestAuth(string $login, string $password): Request
    {
        $this->options['authentication'] = SOAP_AUTHENTICATION_DIGEST;
        $this->options['login'] = $login;
        $this->options['password'] = $password;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array<string, mixed> $options
     */
    public function withOptions(array $options): Request
    {
        $this->options = array_merge($this->getOptions(), $options);

        return $this;
    }

    public function withHeaders(Header ...$headers): Request
    {
        $this->headers = array_merge($this->getHeaders(), $headers);

        return $this;
    }

    /**
     * @return array<int, Header>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}
