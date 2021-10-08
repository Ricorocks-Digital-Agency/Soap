<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap\Support\Scopes;

/**
 * @internal
 */
final class OptionSet
{
    use Scopeable;

    /**
     * @var array<string, mixed>
     */
    protected array $options = [];

    /**
     * @param array<string, mixed> $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
