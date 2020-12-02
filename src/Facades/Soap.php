<?php


namespace RicorocksDigitalAgency\Soap\Facades;


use Closure;
use Illuminate\Support\Facades\Facade;
use RicorocksDigitalAgency\Soap\Parameters\Node;
use RicorocksDigitalAgency\Soap\Request\Request;

/**
 * Class Soap
 * @package RicorocksDigitalAgency\Soap\Facades
 *
 * @method static Request to(string $endpoint)
 * @method static Node node(array $attributes = [])
 * @method static include(array $parameters)
 * @method static fake(array|Closure $callable = null)
 */
class Soap extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'soap';
    }

}