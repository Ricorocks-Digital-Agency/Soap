<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap\Support;

abstract class Scoped
{
    protected $endpoint;
    protected $method = null;

    public function for($endpoint, $method = null)
    {
        $this->endpoint = $endpoint;
        $this->method = $method;
    }

    public function matches(string $endpoint, $method = null)
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
