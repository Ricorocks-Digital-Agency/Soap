<?php


namespace RicorocksDigitalAgency\Soap\Facades;


use Illuminate\Support\Facades\Facade;
use RicorocksDigitalAgency\Soap\Request\Request;

/**
 * Class Soap
 * @package RicorocksDigitalAgency\Soap\Facades
 *
 * @method Request to(string $endpoint)
 */
class Soap extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'soap';
    }

}