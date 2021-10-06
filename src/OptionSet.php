<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap;

use RicorocksDigitalAgency\Soap\Support\Scoped;

final class OptionSet extends Scoped
{
    protected $options = [];

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    public function getOptions()
    {
        return $this->options;
    }
}
