<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap;

use RicorocksDigitalAgency\Soap\Support\Scoped;

final class HeaderSet extends Scoped
{
    protected $headers;

    public function __construct(Header ...$headers)
    {
        $this->headers = $headers;
    }

    public function getHeaders()
    {
        return $this->headers;
    }
}
