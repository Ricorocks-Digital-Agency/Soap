<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap\Contracts;

interface Traceable
{
    public function __getLastRequest(): ?string;

    public function __getLastResponse(): ?string;

    public function __getLastRequestHeaders(): ?string;

    public function __getLastResponseHeaders(): ?string;
}
