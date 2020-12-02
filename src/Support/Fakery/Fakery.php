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
            $this->stubCallbacks = array_merge([fn() => new Response()], $this->stubCallbacks);
            return;
        }

        if (is_array($callback)) {
            foreach ($callback as $url => $callable) {
                $this->stubCallbacks[] = $this->stubEndpoint($url, $callable);
            }
        }
    }

    protected function stubEndpoint($url, $callable)
    {
        return function (Request $request) use ($url, $callable) {
            $pieces = [
                Str::of($url)->start('*')->replaceMatches("/:([\w\d]+$)/", ""),
                Str::of($url)->afterLast(".")->match("/:([\w\d]+$)/")->start("*")
            ];

            if (!Str::is($pieces[0]->__toString(), $request->getEndpoint())) {
                return;
            }

            if ($pieces[1]->isNotEmpty() && !Str::is($pieces[1]->__toString(), $request->getMethod())) {
                return;
            }

            return $callable instanceof Closure ? $callable($request) : $callable;
        };
    }

    protected function checkForMock(Request $request)
    {
        return collect($this->stubCallbacks)
            ->reverse()
            ->map
            ->__invoke($request)
            ->filter()
            ->first();
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
