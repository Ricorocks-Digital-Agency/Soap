<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap\Contracts;

interface Builder
{
    /**
     * @param array<array<mixed>|Soapable>|Soapable $parameters
     *
     * @return array<string, mixed>
     */
    public function handle(array|Soapable $parameters): array;
}
