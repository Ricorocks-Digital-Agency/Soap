<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap;

use RicorocksDigitalAgency\Soap\Support\Scoped;

final class Inclusion extends Scoped
{
    /**
     * @var non-empty-array<string, mixed>
     */
    protected array $parameters;

    /**
     * @param non-empty-array<string, mixed> $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return non-empty-array<string, mixed>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
