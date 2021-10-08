<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap\Parameters;

use RicorocksDigitalAgency\Soap\Contracts\Builder;
use RicorocksDigitalAgency\Soap\Contracts\Soapable;

/**
 * @internal
 */
final class IntelligentBuilder implements Builder
{
    /**
     * @param array<array<mixed>|Soapable>|Soapable $parameters
     *
     * @return array<string, mixed>
     */
    public function handle(array|Soapable $parameters): array
    {
        return $this->handleParameter($parameters);
    }

    private function handleParameter(mixed $parameter): mixed
    {
        if ($parameter instanceof Soapable) {
            $parameter = $parameter->toSoap();
        }

        if (is_array($parameter)) {
            $parameter = $this->walk($parameter);
        }

        return $parameter;
    }

    /**
     * @param array<mixed> $parameters
     *
     * @return array<mixed>
     */
    private function walk(array $parameters): array
    {
        return collect($parameters)
            ->map(fn ($parameter) => $this->handleParameter($parameter))
            ->toArray();
    }
}
