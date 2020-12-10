<?php

namespace RicorocksDigitalAgency\Soap\Support\Tracing;

class Trace
{
    public $xmlRequest;
    public $xmlResponse;

    public static function xmlRequest($xml): self
    {
        return tap(new static, fn($instance) => $instance->xmlRequest = $xml);
    }

    public function xmlResponse($xml): self
    {
        return tap($this, fn($self) => $self->xmlResponse = $xml);
    }
}
