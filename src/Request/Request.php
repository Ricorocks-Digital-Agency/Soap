<?php

namespace RicorocksDigitalAgency\Soap\Request;

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

    public function getEndpoint();

    public function getMethod();

    public function getBody();
}
