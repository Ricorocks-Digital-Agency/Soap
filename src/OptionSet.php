<?php

namespace RicorocksDigitalAgency\Soap;

use RicorocksDigitalAgency\Soap\Support\Scoped;

class OptionSet extends Scoped
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
