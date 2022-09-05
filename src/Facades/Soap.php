<?php

namespace RicorocksDigitalAgency\Soap\Facades;

use Illuminate\Support\Facades\Facade;
use RicorocksDigitalAgency\Soap\Header;
use RicorocksDigitalAgency\Soap\HeaderSet;
use RicorocksDigitalAgency\Soap\Inclusion;
use RicorocksDigitalAgency\Soap\OptionSet;
use RicorocksDigitalAgency\Soap\Parameters\Node;
use RicorocksDigitalAgency\Soap\Request\Request;

/**
 * Class Soap.
 *
 * @method static Request to(string $endpoint)
 * @method static Header header(?string $name = null, ?string $namespace = null, $data = null, bool $mustUnderstand = false, ?string $actor = null)
 * @method static HeaderSet headers(Header ...$headers)
 * @method static Node node(array $attributes = [])
 * @method static Inclusion include(array $parameters)
 * @method static OptionSet options(array $options)
 * @method static \RicorocksDigitalAgency\Soap\Soap beforeRequesting(callable $hook)
 * @method static \RicorocksDigitalAgency\Soap\Soap afterRequesting(callable $hook)
 * @method static \RicorocksDigitalAgency\Soap\Soap afterErroring(callable $hook)
 * @method static void fake(array|Closure $callable = null)
 * @method static void assertSentCount($count)
 * @method static void assertNothingSent()
 * @method static void assertSent(callable $callback)
 * @method static void assertNotSent(callable $callback)
 */
class Soap extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'soap';
    }
}
