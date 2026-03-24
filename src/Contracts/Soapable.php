<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap\Contracts;

interface Soapable
{
    public function toSoap(): mixed;
}
