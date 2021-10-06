<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap\Parameters;

use Illuminate\Contracts\Support\Arrayable;
use RicorocksDigitalAgency\Soap\Contracts\Soapable;

final class Node implements Arrayable, Soapable
{
    private $name;
    private $attributes = [];
    private $body = [];

    public function __construct($attributes)
    {
        $this->attributes = $attributes;
    }

    public function body($content)
    {
        $this->body = $content;

        return $this;
    }

    public function toArray()
    {
        return empty($this->body)
            ? array_merge(['_' => ''], $this->attributes)
            : array_merge($this->body, $this->attributes);
    }

    public function toSoap()
    {
        return $this->toArray();
    }
}
