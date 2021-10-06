<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap\Support\Fakery;

use PHPUnit\Framework\Assert as PHPUnit;
use RicorocksDigitalAgency\Soap\Request\Request;
use RicorocksDigitalAgency\Soap\Response\Response;

final class Fakery
{
    private $shouldRecord = false;
    private $recordedRequests;
    private Stubs $stubs;

    public function __construct(Stubs $stubs)
    {
        $this->stubs = $stubs;
    }

    public function fake($callback = null)
    {
        $this->shouldRecord = true;

        if (is_null($callback)) {
            $this->stubs->new('*', fn () => Response::new());

            return;
        }

        if (is_array($callback)) {
            collect($callback)->each(fn ($callable, $url) => $this->stubs->new($url, $callable));

            return;
        }
    }

    public function mockResponseIfAvailable(Request $request)
    {
        return $this->stubs->getForRequest($request);
    }

    public function record(Request $request, Response $response)
    {
        if (!$this->shouldRecord) {
            return;
        }

        ($this->recordedRequests ??= collect())->push([$request, $response]);
    }

    public function assertSentCount($count)
    {
        PHPUnit::assertCount($count, $this->recordedRequests);
    }

    public function assertNothingSent()
    {
        PHPUnit::assertEmpty($this->recordedRequests, 'Requests were recorded');
    }

    public function assertSent(callable $callback)
    {
        PHPUnit::assertTrue($this->recorded($callback)->isNotEmpty());
    }

    public function assertNotSent(callable $callback)
    {
        PHPUnit::assertTrue($this->recorded($callback)->isEmpty());
    }

    private function recorded($callback)
    {
        if ($this->recordedRequests->isEmpty()) {
            return collect();
        }

        $callback = $callback ?: function () {
            return true;
        };

        return $this->recordedRequests->filter(fn ($pair) => $callback(...$pair));
    }
}
