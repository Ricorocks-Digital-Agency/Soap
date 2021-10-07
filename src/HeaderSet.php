<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap;

use RicorocksDigitalAgency\Soap\Support\Scoped;

final class HeaderSet extends Scoped
{
    /**
     * @var non-empty-array<int, Header>
     */
    protected array $headers;

    /**
     * @param non-empty-array<int, Header> $headers
     */
    public function __construct(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * @return non-empty-array<int, Header>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}
