<?php

namespace RicorocksDigitalAgency\Soap\Tests\Unit;

use Mockery as m;
use RicorocksDigitalAgency\Soap\Request\Request;
use RicorocksDigitalAgency\Soap\Response\Response;
use RicorocksDigitalAgency\Soap\Soap;
use RicorocksDigitalAgency\Soap\Support\Fakery\Fakery;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected ?Soap $soap;

    public function soap(?Fakery $fakery = null, ?Request $request = null): Soap
    {
        return $this->soap ??= soap($fakery, $request);
    }

    public function fake($callback = null)
    {
        $this->soap()->fake($callback);

        return $this;
    }

    public function assertSent(callable $callable)
    {
        $this->soap()->assertSent($callable);

        return $this;
    }

    public function assertNotSent(callable $callable)
    {
        $this->soap()->assertNotSent($callable);

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

    /**
     * Execute the given callable, which is passed the TestCase, and return it.
     */
    public function defer(callable $callable)
    {
        return $callable($this);
    }

    /**
     * Execute the given callable, which is passed the TestCase, then return the TestCase.
     */
    public function tap(callable $callable)
    {
        $callable($this);

        return $this;
    }

    public function fakeRequest($expectation)
    {
        $this->soap()->fake(['*' => Response::new(['AddResult' => $expectation])]);
    }

    protected function tearDown(): void
    {
        m::close();
        parent::tearDown();
    }
}
