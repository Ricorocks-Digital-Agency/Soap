<?php

declare(strict_types=1);

use RicorocksDigitalAgency\Soap\Facades\Soap;
use RicorocksDigitalAgency\Soap\Parameters\Node;
use RicorocksDigitalAgency\Soap\Support\Header;

if (!function_exists('soap_node')) {
    /**
     * @param array<string, mixed> $attributes
     */
    function soap_node(array $attributes = []): Node
    {
        return Soap::node($attributes);
    }
}

if (!function_exists('soap_header')) {
    /**
     * @param array<string, mixed>|null $data
     */
    function soap_header(string $name = '', string $namespace = '', array $data = null, bool $mustUnderstand = false, string|int $actor = null): Header
    {
        return Soap::header($name, $namespace, $data, $mustUnderstand, $actor);
    }
}
