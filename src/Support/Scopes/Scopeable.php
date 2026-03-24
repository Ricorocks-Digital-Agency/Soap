<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap\Support\Scopes;

/**
 * @internal
 */
trait Scopeable
{
    protected string $endpoint;
    protected ?string $method = null;

    public function for(string $endpoint, ?string $method = null): void
    {
        $this->endpoint = $endpoint;
        $this->method = $method;
    }

    public function matches(string $endpoint, ?string $method = null): bool
    {
        if (empty($this->endpoint)) {
            return true;
        }

        if ($this->endpoint !== $endpoint) {
            return false;
        }

        return empty($this->method) || $this->method === $method;
    }
}
