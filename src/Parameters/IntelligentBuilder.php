<?php

namespace RicorocksDigitalAgency\Soap\Parameters;

use RicorocksDigitalAgency\Soap\Contracts\Soapable;

class IntelligentBuilder implements Builder
{
    public function handle($parameters)
    {
        return $this->handleParameter($parameters);
    }

    protected function handleParameter($parameter)
    {
        if ($parameter instanceof Soapable) {
            $parameter = $parameter->toSoap();
        }

        if (is_array($parameter)) {
            $parameter = $this->walk($parameter);
        }

        return $parameter;
    }

    protected function walk($parameters)
    {
        return collect($parameters)
            ->map(fn ($parameter) => $this->handleParameter($parameter))
            ->toArray();
    }
}
