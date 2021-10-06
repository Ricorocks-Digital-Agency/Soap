<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap\Support\Fakery;

use Closure;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Assert as PHPUnit;
use RicorocksDigitalAgency\Soap\Request\Request;
use RicorocksDigitalAgency\Soap\Response\Response;

final class Fakery
{
    private bool $shouldRecord = false;

    /**
     * @var Collection<int, array{0: Request, 1: Response}>
     */
    private Collection $recordedRequests;

    private Stubs $stubs;

    public function __construct(Stubs $stubs)
    {
        $this->stubs = $stubs;
        $this->recordedRequests = collect();
    }

    /**
     * @param array<string, Closure(Request): Response|Response>|null $callback
     */
    public function fake(array $callback = null): void
    {
        $this->shouldRecord = true;

        match (true) {
            is_null($callback) => $this->stubs->new('*', fn () => Response::new()),
            is_array($callback) => collect($callback)->each(fn ($callable, $url) => $this->stubs->new($url, $callable)),
        };
    }

    public function mockResponseIfAvailable(Request $request): ?Response
    {
        return $this->stubs->getForRequest($request);
    }

    public function record(Request $request, Response $response): void
    {
        if (!$this->shouldRecord) {
            return;
        }

        $this->recordedRequests->push([$request, $response]);
    }

    public function assertSentCount(int $count): void
    {
        PHPUnit::assertCount($count, $this->recordedRequests);
    }

    public function assertNothingSent(): void
    {
        PHPUnit::assertEmpty($this->recordedRequests, 'Requests were recorded');
    }

    /**
     * @param Closure(Request, Response): bool $callback
     */
    public function assertSent(Closure $callback): void
    {
        PHPUnit::assertTrue($this->recorded($callback)->isNotEmpty());
    }

    /**
     * @param Closure(Request, Response): bool $callback
     */
    public function assertNotSent(Closure $callback): void
    {
        PHPUnit::assertTrue($this->recorded($callback)->isEmpty());
    }

    /**
     * @param Closure(Request, Response): bool $callback
     *
     * @return Collection<int, array{0: Request, 1:Response}>
     */
    private function recorded(Closure $callback): Collection
    {
        if ($this->recordedRequests->isEmpty()) {
            return collect();
        }

        return $this->recordedRequests->filter(fn ($pair) => $callback(...$pair));
    }
}
