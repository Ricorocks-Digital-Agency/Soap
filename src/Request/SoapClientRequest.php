<?php

namespace RicorocksDigitalAgency\Soap\Request;

use RicorocksDigitalAgency\Soap\Header;
use RicorocksDigitalAgency\Soap\Parameters\Builder;
use RicorocksDigitalAgency\Soap\Response\Response;
use RicorocksDigitalAgency\Soap\Support\Tracing\Trace;
use SoapClient;
use SoapHeader;

class SoapClientRequest implements Request
{
    protected string $endpoint;
    protected string $method;
    protected $body = [];
    protected $client;
    protected Builder $builder;
    protected Response $response;
    protected $hooks = [];
    protected $options = [];
    protected $headers = [];

    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
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

        $this->hooks['beforeRequesting']->each(fn($callback) => $callback($this));
        $this->body = $this->builder->handle($this->body);

        $response = $this->getResponse();
        $this->hooks['afterRequesting']->each(fn($callback) => $callback($this, $response));

        return $response;
    }

    protected function getResponse()
    {
        return $this->response ??= $this->getRealResponse();
    }

    protected function getRealResponse()
    {
        return tap(
            Response::new($this->makeRequest()),
            fn($response) => data_get($this->options, 'trace')
                ? $response->setTrace(Trace::client($this->client()))
                : $response
        );
    }

    protected function makeRequest()
    {
        return $this->client()->{$this->getMethod()}($this->getBody());
    }

    protected function client()
    {
        return $this->client ??= $this->constructClient();
    }

    protected function constructClient()
    {
        $client = resolve(SoapClient::class, [
            'wsdl' => $this->endpoint,
            'options' => $this->options
        ]);

        $client->__setSoapHeaders($this->constructHeaders());

        return $client;
    }

    protected function constructHeaders()
    {
        if (empty($this->headers)) {
            return;
        }

        return array_map(
            fn ($header) => resolve(SoapHeader::class, [
                'namespace' => $header->namespace,
                'name' => $header->name,
                'data' => $header->data,
                'mustUnderstand' => $header->mustUnderstand,
                'actor' => $header->actor
            ]), $this->headers
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
        return $this->client()->__getFunctions();
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
