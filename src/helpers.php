<?php

use RicorocksDigitalAgency\Soap\Facades\Soap;

if (!function_exists('soap_node')) {
    function soap_node($attributes = [])
    {
        return Soap::node($attributes);
    }
}

if (!function_exists('soap_header')) {
    function soap_header(?string $name = null, ?string $namespace = null, $data = null, bool $mustUnderstand = false, ?string $actor = null)
    {
        return Soap::header($name, $namespace, $data, $mustUnderstand, $actor);
    }
}
