<?php

namespace RicorocksDigitalAgency\Soap\Request;

use RicorocksDigitalAgency\Soap\Parameters\Builder;
use RicorocksDigitalAgency\Soap\Response\Response;
use RicorocksDigitalAgency\Soap\Support\Tracing\Trace;
use SoapClient;

class SoapClientRequest implements Request
{
    protected string $endpoint;
    protected string $method;
    protected $body = [];
    protected SoapClient $client;
    protected Builder $builder;
    protected Response $response;
    protected $hooks = [];
    protected $shouldTrace = false;

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
            fn($response) => $this->shouldTrace ? $this->addTrace($response) : $response
        );
    }

    protected function addTrace($response)
    {
        return $response->setTrace(
            Trace::thisXmlRequest($this->client()->__getLastRequest())
                ->thisXmlResponse($this->client()->__getLastResponse())
        );
    }

    protected function makeRequest()
    {
        return $this->client()->{$this->getMethod()}($this->getBody());
    }

    protected function client()
    {
        return $this->client ??= new SoapClient($this->endpoint, ['trace' => $this->shouldTrace]);
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
        $this->shouldTrace = $shouldTrace;
        return $this;
    }
}
