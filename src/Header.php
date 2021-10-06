<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap;

use Illuminate\Contracts\Support\Arrayable;

final class Header implements Arrayable
{
    public $name;
    public $namespace;
    public $data;
    public $actor;
    public $mustUnderstand;

    public function __construct(?string $name = null, ?string $namespace = null, $data = null, bool $mustUnderstand = false, $actor = null)
    {
        $this->name = $name;
        $this->namespace = $namespace;
        $this->data = $data;
        $this->mustUnderstand = $mustUnderstand;
        $this->actor = $actor;
    }

    public function name(string $name): self
    {
        return tap($this, fn () => $this->name = $name);
    }

    public function namespace(string $namespace): self
    {
        return tap($this, fn () => $this->namespace = $namespace);
    }

    public function data($data = null): self
    {
        return tap($this, fn () => $this->data = $data);
    }

    public function actor($actor = null): self
    {
        return tap($this, fn () => $this->actor = $actor);
    }

    public function mustUnderstand(bool $mustUnderstand = true): self
    {
        return tap($this, fn () => $this->mustUnderstand = $mustUnderstand);
    }

    public function toArray()
    {
        return [
            'name' => $this->name,
            'namespace' => $this->namespace,
            'data' => $this->data,
            'mustUnderstand' => $this->mustUnderstand,
            'actor' => $this->actor,
        ];
    }
}
