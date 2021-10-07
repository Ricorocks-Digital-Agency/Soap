<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap;

use Illuminate\Contracts\Support\Arrayable;
use SoapVar;

final class Header implements Arrayable
{
    public string $name;

    public string $namespace;

    /**
     * @var SoapVar|array<string, mixed>|null
     */
    public SoapVar|array|null $data;

    public ?string $actor;

    public bool $mustUnderstand;

    /**
     * @param SoapVar|array<string, mixed>|null $data
     */
    public function __construct(string $name = '', string $namespace = '', SoapVar|array $data = null, bool $mustUnderstand = false, string $actor = null)
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

    /**
     * @param SoapVar|array<string, mixed>|null $data
     */
    public function data(SoapVar|array $data = null): self
    {
        return tap($this, fn () => $this->data = $data);
    }

    public function actor(string $actor = null): self
    {
        return tap($this, fn () => $this->actor = $actor);
    }

    public function mustUnderstand(bool $mustUnderstand = true): self
    {
        return tap($this, fn () => $this->mustUnderstand = $mustUnderstand);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
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
