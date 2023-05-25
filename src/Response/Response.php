<?php

namespace RicorocksDigitalAgency\Soap\Response;

use RicorocksDigitalAgency\Soap\Support\Tracing\Trace;

class Response
{
    public $response;
    protected Trace $trace;

    public static function new($response = []): self
    {
        return tap(new static(), function($instance) use ($response) {
            return $instance->response = $response;
        });
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
        if(is_null($this->trace))
            return app(Trace::class);
        else
            return $this->trace;
    }

    public function set($key, $value): self
    {
        data_set($this->response, $key, $value);

        return $this;
    }
}
