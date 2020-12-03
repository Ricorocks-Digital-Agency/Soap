<?php

namespace RicorocksDigitalAgency\Soap\Support\Fakery;

use RicorocksDigitalAgency\Soap\Request\Request;

class Stubs
{
    protected $stubs;

    public function __construct()
    {
        $this->stubs = collect();
    }

    public function new($url, $callback)
    {
        $this->stubs->push(Stub::for($url)->respondWith($callback));
    }

    public function getForRequest(Request $request)
    {
        return $this->stubs
                ->pipe(fn($stubs) => $this->filterAndSortStubs($stubs, $request))
                ->map
                ->getResponse($request)
                ->filter()
                ->first();
    }

    protected function filterAndSortStubs($stubs, Request $request)
    {
        return $stubs
                ->filter(fn(Stub $stub) => $stub->isForEndpoint($request->getEndpoint()))
                ->pipe(fn($stubs) => $this->retrieveCorrectStubsForMethod($stubs, $request));
    }

    protected function retrieveCorrectStubsForMethod($stubs, Request $request)
    {
        return $request->getMethod()
                ? $this->getStubsForMethod($stubs, $request)
                : $stubs->sortByDesc('endpoint');
    }

    protected function getStubsForMethod($stubs, $request)
    {
        return $stubs
                ->filter(fn(Stub $stub) => $stub->isForMethod($request->getMethod()))
                ->pipe(fn($stubs) => $this->sortMethodStubs($stubs));
    }

    protected function sortMethodStubs($stubs)
    {
        return $stubs->every->hasWildcardMethods()
                ? $stubs->sortByDesc('endpoint')
                : $stubs->sortByDesc('methods');
    }
}
