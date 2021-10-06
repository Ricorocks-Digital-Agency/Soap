<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap\Support\Fakery;

use Closure;
use Illuminate\Support\Collection;
use RicorocksDigitalAgency\Soap\Request\Request;
use RicorocksDigitalAgency\Soap\Response\Response;

final class Stubs
{
    /**
     * @var Collection<int, Stub>
     */
    private Collection $stubs;

    public function __construct()
    {
        $this->stubs = collect();
    }

    /**
     * @param Closure(Request): Response|Response $callback
     */
    public function new(string $url, Closure|Response $callback): void
    {
        $this->stubs->push(Stub::for($url)->respondWith($callback));
    }

    public function getForRequest(Request $request): ?Response
    {
        return $this->stubs
                ->pipe(fn ($stubs) => $this->filterAndSortStubs($stubs, $request))
                ->map
                ->getResponse($request)
                ->filter()
                ->first();
    }

    /**
     * @param Collection<int, Stub> $stubs
     *
     * @return Collection<int, Stub>
     */
    private function filterAndSortStubs(Collection $stubs, Request $request): Collection
    {
        return $stubs
                ->filter(fn (Stub $stub) => $stub->isForEndpoint($request->getEndpoint()))
                ->pipe(fn ($stubs) => $this->retrieveCorrectStubsForMethod($stubs, $request));
    }

    /**
     * @param Collection<int, Stub> $stubs
     *
     * @return Collection<int, Stub>
     */
    private function retrieveCorrectStubsForMethod(Collection $stubs, Request $request): Collection
    {
        return $request->getMethod()
                ? $this->getStubsForMethod($stubs, $request)
                : $stubs->sortByDesc('endpoint');
    }

    /**
     * @param Collection<int, Stub> $stubs
     *
     * @return Collection<int, Stub>
     */
    private function getStubsForMethod(Collection $stubs, Request $request): Collection
    {
        return $stubs
                ->filter(fn (Stub $stub) => $stub->isForMethod($request->getMethod()))
                ->pipe(fn ($stubs) => $this->sortMethodStubs($stubs));
    }

    /**
     * @param Collection<int, Stub> $stubs
     *
     * @return Collection<int, Stub>
     */
    private function sortMethodStubs(Collection $stubs): Collection
    {
        return $stubs->every(fn (Stub $stub) => $stub->hasWildcardMethods())
                ? $stubs->sortByDesc('endpoint')
                : $stubs->sortByDesc('methods');
    }
}
