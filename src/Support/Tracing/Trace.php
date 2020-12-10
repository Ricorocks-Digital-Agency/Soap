<?php

namespace RicorocksDigitalAgency\Soap\Support\Tracing;

class Trace
{
    public $xmlRequest;
    public $xmlResponse;

    public static function thisXmlRequest($xml): self
    {
        return tap(new static, fn($instance) => $instance->xmlRequest = $xml);
    }

    public function thisXmlResponse($xml): self
    {
        return tap($this, fn($self) => $self->xmlResponse = $xml);
    }
}
