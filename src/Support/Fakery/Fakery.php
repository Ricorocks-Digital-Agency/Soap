<?php

namespace RicorocksDigitalAgency\Soap\Support\Fakery;

use PHPUnit\Framework\Assert as PHPUnit;
use RicorocksDigitalAgency\Soap\Request\Request;
use RicorocksDigitalAgency\Soap\Response\Response;

class Fakery
{
    protected $shouldRecord = false;
    protected $recordedRequests;
    protected $stubCallbacks;

    public function __construct()
    {
        $this->stubCallbacks = collect();
    }

    public function fake($callback = null)
    {
        $this->shouldRecord = true;

        if (is_null($callback)) {
            $this->newStub('*', fn() => Response::new());
            return;
        }

        if (is_array($callback)) {
            collect($callback)->each(fn($callable, $url) => $this->newStub($url, $callable));
            return;
        }
    }

    protected function newStub($url, $callback)
    {
        $this->stubCallbacks->push(Stub::for($url)->respondWith($callback));
    }

    public function returnMockResponseIfAvailable(Request $request)
    {
        return $this->stubCallbacks
            ->filter(fn(Stub $stub) => $stub->isForEndpoint($request->getEndpoint()))
            ->when($request->getMethod(), fn($stubs) => $this->getStubsForMethod($stubs, $request->getMethod()))
            ->sortByDesc('endpoint')
            ->map
            ->getResponse($request)
            ->filter()
            ->first();
    }

    protected function getStubsForMethod($stubs, $method)
    {
        return $stubs
                ->filter(fn(Stub $stub) => $stub->isForMethod($method))
                ->sortByDesc('methods');
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
        PHPUnit::assertEmpty($this->recordedRequests, "Requests were recorded");
    }

    public function assertSent(callable $callback)
    {
        PHPUnit::assertTrue($this->recorded($callback)->isNotEmpty());
    }

    public function assertNotSent(callable $callback)
    {
        PHPUnit::assertTrue($this->recorded($callback)->isEmpty());
    }

    protected function recorded($callback)
    {
        if ($this->recordedRequests->isEmpty()) {
            return collect();
        }

        $callback = $callback ?: function () {
            return true;
        };

        return $this->recordedRequests->filter(fn($pair) => $callback(...$pair));
    }
}
