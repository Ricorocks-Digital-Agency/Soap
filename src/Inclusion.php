<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap;

use RicorocksDigitalAgency\Soap\Support\Scoped;

final class Inclusion extends Scoped
{
    protected $parameters;

    public function __construct($parameters)
    {
        $this->parameters = $parameters;
    }

    public function getParameters()
    {
        return $this->parameters;
    }
}
