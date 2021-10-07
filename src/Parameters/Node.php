<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap\Parameters;

use Illuminate\Contracts\Support\Arrayable;
use RicorocksDigitalAgency\Soap\Contracts\Soapable;

final class Node implements Arrayable, Soapable
{
    /**
     * @var array<string, mixed>
     */
    private array $attributes = [];

    /**
     * @var array<string, mixed>
     */
    private array $body = [];

    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @param array<string, mixed> $content
     */
    public function body(array $content): self
    {
        $this->body = $content;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return empty($this->body)
            ? array_merge(['_' => ''], $this->attributes)
            : array_merge($this->body, $this->attributes);
    }

    /**
     * @return array<string, mixed>
     */
    public function toSoap(): array
    {
        return $this->toArray();
    }
}
