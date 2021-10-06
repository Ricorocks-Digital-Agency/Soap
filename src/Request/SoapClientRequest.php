<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap\Request;

use Closure;
use RicorocksDigitalAgency\Soap\Contracts\Builder;
use RicorocksDigitalAgency\Soap\Contracts\Client;
use RicorocksDigitalAgency\Soap\Header;
use RicorocksDigitalAgency\Soap\Response\Response;
use RicorocksDigitalAgency\Soap\Support\DecoratedClient;
use RicorocksDigitalAgency\Soap\Support\Tracing\Trace;
use SoapClient;
use SoapHeader;

final class SoapClientRequest implements Request
{
    private Builder $builder;

    /**
     * @var Closure(string, array<string, mixed> $options): Client
     */
    private Closure $clientResolver;

    private Client $client;

    private string $endpoint;
    private string $method;
    private $body = [];
    private Response $response;
    private $hooks = [];
    private $options = [];
    private $headers = [];

    /**
     * @param Closure(string $endpoint, array<string, mixed> $options): Client|null $client
     */
    public function __construct(Builder $builder, Closure $clientResolver = null)
    {
        $this->builder = $builder;
        $this->clientResolver = $clientResolver ?? fn (string $endpoint, array $options) => new DecoratedClient(new SoapClient($endpoint, $options));
    }

    public function to(string $endpoint): Request
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    public function __call($name, $parameters)
    {
        return $this->call($name, $parameters[0] ?? []);
    }

    public function call($method, $parameters = [])
    {
        $this->method = $method;
        $this->body = $parameters;

        $this->hooks['beforeRequesting']->each(fn ($callback) => $callback($this));
        $this->body = $this->builder->handle($this->body);

        $response = $this->getResponse();
        $this->hooks['afterRequesting']->each(fn ($callback) => $callback($this, $response));

        return $response;
    }

    private function getResponse()
    {
        return $this->response ??= $this->getRealResponse();
    }

    private function getRealResponse()
    {
        return tap(
            Response::new($this->makeRequest()),
            fn ($response) => data_get($this->options, 'trace')
                ? $response->setTrace(Trace::client($this->client()))
                : $response
        );
    }

    private function makeRequest()
    {
        return $this->client()->call($this->getMethod(), $this->getBody());
    }

    private function client(): Client
    {
        return $this->client ??= call_user_func($this->clientResolver, $this->endpoint, $this->options)->setHeaders($this->constructHeaders());
    }

    /**
     * @return array<string, SoapHeader>
     */
    private function constructHeaders(): array
    {
        if (empty($this->headers)) {
            return [];
        }

        return array_map(
            fn ($header) => resolve(SoapHeader::class, [
                'namespace' => $header->namespace,
                'name' => $header->name,
                'data' => $header->data,
                'mustunderstand' => $header->mustUnderstand,
                'actor' => $header->actor ?? SOAP_ACTOR_NONE,
            ]),
            $this->headers
        );
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function functions(): array
    {
        return $this->client()->getFunctions();
    }

    public function beforeRequesting(...$closures): Request
    {
        ($this->hooks['beforeRequesting'] ??= collect())->push(...$closures);

        return $this;
    }

    public function afterRequesting(...$closures): Request
    {
        ($this->hooks['afterRequesting'] ??= collect())->push(...$closures);

        return $this;
    }

    public function getEndpoint()
    {
        return $this->endpoint;
    }

    public function fakeUsing($response): Request
    {
        if (empty($response)) {
            return $this;
        }

        $this->response = $response instanceof Response ? $response : $response($this);

        return $this;
    }

    public function set($key, $value): Request
    {
        data_set($this->body, $key, $value);

        return $this;
    }

    public function trace($shouldTrace = true): Request
    {
        $this->options['trace'] = $shouldTrace;

        return $this;
    }

    public function withBasicAuth($login, $password): Request
    {
        $this->options['authentication'] = SOAP_AUTHENTICATION_BASIC;
        $this->options['login'] = $login;
        $this->options['password'] = $password;

        return $this;
    }

    public function withDigestAuth($login, $password): Request
    {
        $this->options['authentication'] = SOAP_AUTHENTICATION_DIGEST;
        $this->options['login'] = $login;
        $this->options['password'] = $password;

        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

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

    public function getHeaders(): array
    {
        return $this->headers;
    }
}
