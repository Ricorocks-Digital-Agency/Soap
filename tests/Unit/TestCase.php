<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap\Tests\Unit;

use Closure;
use Mockery as m;
use Pest\PendingObjects\TestCall;
use RicorocksDigitalAgency\Soap\Contracts\Request;
use RicorocksDigitalAgency\Soap\Soap;
use RicorocksDigitalAgency\Soap\Support\Fakery\Fakery;
use RicorocksDigitalAgency\Soap\Tests\Mocks\MockSoapClient;

/**
 * @mixin TestCall
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected ?Soap $soap;

    public function soap(?Fakery $fakery = null, ?Request $request = null): Soap
    {
        return $this->soap ??= soap($fakery, $request);
    }

    public function traceableSoap()
    {
        return soap(null, soapRequest(null, new MockSoapClient()));
    }

    public function fake($callback = null)
    {
        $this->soap()->fake($callback);

        return $this;
    }

    public function assertSent(callable $callable)
    {
        $this->soap()->assertSent(Closure::bind($callable, $this));

        return $this;
    }

    public function assertNotSent(callable $callable)
    {
        $this->soap()->assertNotSent(Closure::bind($callable, $this));

        return $this;
    }

    public function assertSentCount(int $count)
    {
        $this->soap()->assertSentCount($count);

        return $this;
    }

    public function assertNothingSent()
    {
        $this->soap()->assertNothingSent();

        return $this;
    }

    protected function tearDown(): void
    {
        m::close();
        parent::tearDown();
    }
}
