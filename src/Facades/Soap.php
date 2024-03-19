<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap\Facades;

use Illuminate\Support\Facades\Facade;
use RicorocksDigitalAgency\Soap\Contracts\Request;
use RicorocksDigitalAgency\Soap\Parameters\Node;
use RicorocksDigitalAgency\Soap\Response\Response;
use RicorocksDigitalAgency\Soap\Support\Header;
use RicorocksDigitalAgency\Soap\Support\Scopes\HeaderSet;
use RicorocksDigitalAgency\Soap\Support\Scopes\Inclusion;
use RicorocksDigitalAgency\Soap\Support\Scopes\OptionSet;
use SoapVar;

/**
 * Class Soap.
 *
 * @method static Request                           to(string $endpoint)
 * @method static Header                            header(string $name = '', string $namespace = '', array|SoapVar $data = null, bool $mustUnderstand = false, string|int|null $actor = null)
 * @method static HeaderSet                         headers(Header ...$headers)
 * @method static Node                              node(array $attributes = [])
 * @method static Inclusion                         include(array $parameters)
 * @method static OptionSet                         options(array $options)
 * @method static \RicorocksDigitalAgency\Soap\Soap beforeRequesting(callable $hook)
 * @method static \RicorocksDigitalAgency\Soap\Soap afterRequesting(callable $hook)
 * @method static void fake(array|Closure $callable = null)
 * @method static void assertSentCount($count)
 * @method static void assertNothingSent()
 * @method static void assertSent(callable $callback)
 * @method static void assertNotSent(callable $callback)
 * @method static Response response(array|stdClass $response)
 */
final class Soap extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'soap';
    }
}
