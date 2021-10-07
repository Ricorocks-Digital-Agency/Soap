<?php

declare(strict_types=1);

use RicorocksDigitalAgency\Soap\Contracts\Builder;
use RicorocksDigitalAgency\Soap\Contracts\PhpSoap\Client;
use RicorocksDigitalAgency\Soap\Contracts\Request;
use RicorocksDigitalAgency\Soap\Contracts\Soapable;
use RicorocksDigitalAgency\Soap\Parameters\IntelligentBuilder;
use RicorocksDigitalAgency\Soap\Request\SoapPhpRequest;
use RicorocksDigitalAgency\Soap\Soap;
use RicorocksDigitalAgency\Soap\Support\Fakery\Fakery;
use RicorocksDigitalAgency\Soap\Support\Fakery\Stubs;
use RicorocksDigitalAgency\Soap\Support\SoapClients\DecoratedClient;
use RicorocksDigitalAgency\Soap\Tests;

include __DIR__ . '/../src/helpers.php';

const EXAMPLE_SOAP_ENDPOINT = 'http://www.dneonline.com/calculator.asmx?WSDL';

uses(Tests\Unit\TestCase::class)->in('Unit');
uses(Tests\Feature\TestCase::class)->in('Feature');

function soap(?Fakery $fakery = null, ?Request $request = null)
{
    return new Soap(
        $fakery ?? new Fakery(new Stubs()),
        $request ?? soapRequest(),
    );
}

function soapRequest(Builder $builder = null, Client $client = null): SoapPhpRequest
{
    return new SoapPhpRequest(
        $builder ?? new IntelligentBuilder(),
        $client ?? new DecoratedClient(),
    );
}

final class ExampleSoapable implements Soapable
{
    public function toSoap(): mixed
    {
        return [
            'intA' => 10,
            'intB' => 25,
            'foo' => soap()->node(['email' => 'hi@me.com'])->body(['bar' => soap()->node()->body(['hello' => 'world'])]),
            'bar' => ['baz', 'bang'],
        ];
    }
}
