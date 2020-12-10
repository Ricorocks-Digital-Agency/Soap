<?php

namespace RicorocksDigitalAgency\Soap\Request;

use RicorocksDigitalAgency\Soap\Response\Response;

interface Request
{
    public function to(string $endpoint): self;

    public function __call($name, $arguments);

    public function functions(): array;

    /**
     * @param array $parameters
     */
    public function call($method, $parameters = []);

    public function beforeRequesting(...$closures): self;

    public function afterRequesting(...$closures): self;

    /**
     * @param callable|Response|null $response
     */
    public function fakeUsing($response): self;

    public function getEndpoint();

    public function getMethod();

    public function getBody();

    public function set($key, $value): self;

    public function trace($shouldTrace = true): self;
}
