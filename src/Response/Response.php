<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap\Response;

use RicorocksDigitalAgency\Soap\Support\Tracing\Trace;
use stdClass;

final class Response
{
    /**
     * @var array<string, mixed>|stdClass
     */
    public array|stdClass $response;

    private Trace $trace;

    /**
     * @param array<string, mixed>|stdClass $response
     */
    public static function new(array|stdClass $response = []): self
    {
        return tap(new self(), fn (Response $instance) => $instance->response = $response);
    }

    public function __get(string $name): mixed
    {
        return data_get($this->response, $name);
    }

    public function setTrace(Trace $trace): self
    {
        $this->trace = $trace;

        return $this;
    }

    public function trace(): Trace
    {
        return $this->trace ??= app(Trace::class);
    }

    public function set(string $key, mixed $value): self
    {
        data_set($this->response, $key, $value);

        return $this;
    }
}
