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
                ->pipe(function($stubs) use ($request) {
                    return $this->filterAndSortStubs($stubs, $request);
                })
                ->map
                ->getResponse($request)
                ->filter()
                ->first();
    }

    protected function filterAndSortStubs($stubs, Request $request)
    {
        return $stubs
                ->filter(function(Stub $stub) use ($request){
                    return $stub->isForEndpoint($request->getEndpoint());
                })
                ->pipe(function($stubs) use ($request) {
                    return $this->retrieveCorrectStubsForMethod($stubs, $request);
                });
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
                ->filter(function(Stub $stub) use ($request) {
                    return $stub->isForMethod($request->getMethod());
                })
                ->pipe(function($stubs) {
                    return $this->sortMethodStubs($stubs);
                });
    }

    protected function sortMethodStubs($stubs)
    {
        return $stubs->every->hasWildcardMethods()
                ? $stubs->sortByDesc('endpoint')
                : $stubs->sortByDesc('methods');
    }
}
