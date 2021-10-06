<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap\Parameters;

use RicorocksDigitalAgency\Soap\Contracts\Builder;
use RicorocksDigitalAgency\Soap\Contracts\Soapable;

final class IntelligentBuilder implements Builder
{
    public function handle($parameters)
    {
        return $this->handleParameter($parameters);
    }

    private function handleParameter($parameter)
    {
        if ($parameter instanceof Soapable) {
            $parameter = $parameter->toSoap();
        }

        if (is_array($parameter)) {
            $parameter = $this->walk($parameter);
        }

        return $parameter;
    }

    private function walk($parameters)
    {
        return collect($parameters)
            ->map(fn ($parameter) => $this->handleParameter($parameter))
            ->toArray();
    }
}
