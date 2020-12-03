<?php

namespace RicorocksDigitalAgency\Soap\Support\Fakery;

use Closure;
use Illuminate\Support\Str;
use PHPUnit\Framework\Assert as PHPUnit;
use RicorocksDigitalAgency\Soap\Request\Request;
use RicorocksDigitalAgency\Soap\Response\Response;

class Fakery
{
    protected $shouldRecord = false;
    protected $recordedRequests = [];
    protected $stubCallbacks = [];

    public function fake($callback = null)
    {
        $this->shouldRecord = true;

        if (is_null($callback)) {
            $this->stubCallbacks = array_merge([Stub::for('*')->respondWith(fn() => new Response())], $this->stubCallbacks);
            return;
        }

        if (is_array($callback)) {
            foreach ($callback as $url => $callable) {
                $this->stubCallbacks[] = Stub::for($url)->respondWith($callable);
            }
        }
    }

    protected function stubEndpoint($url, $callable)
    {
        return function (Request $request) use ($url, $callable) {
            $urlDetails = $this->extractEndpointAndMethod($url);

            if (!$this->requestIsForUrl($request, $urlDetails['url'])) {
                return;
            }

            if (!$this->requestIsForMethod($request, $urlDetails['method'])) {
                return;
            }

            return $callable instanceof Closure ? $callable($request) : $callable;
        };
    }

    protected function extractEndpointAndMethod($url)
    {
        return [
            'url' => Str::of($url)->start('*')->replaceMatches("/:([\w\d]+$)/", ""),
            'method' => Str::of($url)->afterLast(".")->match("/:([\w\d]+$)/")->start("*")
        ];
    }

    protected function requestIsForUrl($request, $url)
    {
        return Str::is($url->__toString(), $request->getEndpoint());
    }

    protected function requestIsForMethod($request, $method)
    {
        return $method->isNotEmpty() && Str::is($method->__toString(), $request->getMethod());
    }

    public function returnMockResponseIfAvailable(Request $request)
    {
        return collect($this->stubCallbacks)
            ->reverse()
            ->map
            ->__invoke($request)
            ->filter()
            ->first();
    }

    public function returnMockResponseIfAvailableNew(Request $request)
    {
        return collect($this->stubCallbacks)
            ->filter(fn(Stub $stub) => $stub->isForEndpoint($request->getEndpoint()))
            ->when($request->getMethod(), fn($stubs) => $this->getStubsForMethod($stubs, $request->getMethod()))
            ->sortByDesc('endpoint')
            ->map
            ->generateResponse($request)
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

        $this->recordedRequests[] = [$request, $response];
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
        if (empty($this->recordedRequests)) {
            return collect();
        }

        $callback = $callback ?: function () {
            return true;
        };

        return collect($this->recordedRequests)->filter(fn($pair) => $callback(...$pair));
    }
}
