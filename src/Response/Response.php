<?php

namespace RicorocksDigitalAgency\Soap\Response;

use RicorocksDigitalAgency\Soap\Support\Tracing\Trace;

class Response
{
    public $response;
    protected Trace $trace;

    public static function new($response = []): self
    {
        return tap(new static, fn($instance) => $instance->response = $response);
    }

    public function __get($name)
    {
        return data_get($this->response, $name);
    }

    public function setTrace(Trace $trace)
    {
        $this->trace = $trace;
        return $this;
    }

    public function trace()
    {
        return $this->trace ??= app(Trace::class);
    }

    public function set($key, $value): self
    {
        data_set($this->response, $key, $value);
        return $this;
    }
}
