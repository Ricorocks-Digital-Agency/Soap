<?php

use RicorocksDigitalAgency\Soap\Facades\Soap;

if (!function_exists('soapNode')) {
    function soapNode($attributes = []) {
        return Soap::node($attributes);
    }
}