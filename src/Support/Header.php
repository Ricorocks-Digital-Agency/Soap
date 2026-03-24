<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap\Support;

use Illuminate\Contracts\Support\Arrayable;
use SoapVar;

use function tap;

final class Header implements Arrayable
{
    /**
     * @param SoapVar|array<string, mixed>|null $data
     */
    public function __construct(
        public string $name = '',
        public string $namespace = '',
        public SoapVar|array|null $data = null,
        public bool $mustUnderstand = false,
        public string|int|null $actor = null
    ) {
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
    public function data(SoapVar|array|null $data = null): self
    {
        return tap($this, fn () => $this->data = $data);
    }

    public function actor(?string $actor = null): self
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
