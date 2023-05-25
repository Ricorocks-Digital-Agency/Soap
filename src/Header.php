<?php

namespace RicorocksDigitalAgency\Soap;

use Illuminate\Contracts\Support\Arrayable;

class Header implements Arrayable
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
        return tap($this, function () use($name) {
           return $this->name = $name;
        });
    }

    public function namespace(string $namespace): self
    {
        return tap($this, function () use($namespace) {
            return $this->namespace = $namespace;
        });
    }

    public function data($data = null): self
    {
        return tap($this, function () use($data) {
            return $this->data = $data;
        });
    }

    public function actor($actor = null): self
    {
        return tap($this, function () use($actor) {
            return $this->actor = $actor;
        });
    }

    public function mustUnderstand(bool $mustUnderstand = true): self
    {
        return tap($this, function () use($mustUnderstand) {
            return $this->mustUnderstand = $mustUnderstand;
        });
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
