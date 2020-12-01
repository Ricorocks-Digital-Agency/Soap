<?php


namespace RicorocksDigitalAgency\Soap\Parameters;


use RicorocksDigitalAgency\Soap\Inclusion;

class IntelligentBuilder implements Builder
{

    public function handle($parameters)
    {
        return $this->walk($parameters);
    }

    protected function walk($parameters)
    {
        return collect($parameters)
            ->map(fn($parameter) => $this->handleParameter($parameter))
            ->toArray();
    }

    protected function handleParameter($parameter)
    {
        if ($parameter instanceof Node) {
            $parameter = $parameter->toArray();
        }

        if (is_array($parameter)) {
            $parameter = $this->walk($parameter);
        }

        return $parameter;
    }
}