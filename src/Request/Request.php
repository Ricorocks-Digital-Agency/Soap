<?php


namespace RicorocksDigitalAgency\Soap\Request;


interface Request
{
    public function __call($name, $arguments);

    public function functions(): array;

    /**
     * @param array $parameters
     */
    public function call($method, $parameters = []);
}