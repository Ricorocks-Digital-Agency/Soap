<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap\Support\Fakery;

use Closure;
use Illuminate\Support\Str;
use RicorocksDigitalAgency\Soap\Contracts\Request;
use RicorocksDigitalAgency\Soap\Response\Response;

final class Stub
{
    public const REGEX_PATTERN = "/:([\w\d|]+$)/";

    public string $endpoint;

    public string $methods;

    /**
     * @var Closure(Request): Response|Response
     */
    public Closure|Response $callback;

    public static function for(string $endpoint): self
    {
        return tap(new static(), fn (Stub $instance) => $instance->register($endpoint));
    }

    /**
     * @param Closure(Request): Response|Response $callback
     */
    public function respondWith(Closure|Response $callback): self
    {
        $this->callback = $callback;

        return $this;
    }

    public function getResponse(Request $request): Response
    {
        return $this->callback instanceof Closure
            ? call_user_func($this->callback, $request)
            : $this->callback;
    }

    private function register(string $endpoint): void
    {
        $this->endpoint = Str::of($endpoint)->replaceMatches(self::REGEX_PATTERN, '')->start('*')->__toString();
        $this->methods = Str::of($endpoint)->afterLast('.')->match(self::REGEX_PATTERN)->start('*')->__toString();
    }

    public function isForEndpoint(string $endpoint): bool
    {
        return Str::is($this->endpoint, $endpoint);
    }

    public function isForMethod(string $method): bool
    {
        return Str::of($this->methods)
                ->explode('|')
                ->map(fn ($availableMethod) => Str::start($availableMethod, '*'))
                ->contains(fn ($availableMethod) => Str::is($availableMethod, $method));
    }

    public function hasWildcardMethods(): bool
    {
        return Str::is($this->methods, '*');
    }
}
