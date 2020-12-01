<?php

use RicorocksDigitalAgency\Soap\Facades\Soap;

if (!function_exists('soap_node')) {
    function soap_node($attributes = []) {
        return Soap::node($attributes);
    }
}