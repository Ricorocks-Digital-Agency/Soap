<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap\Response;

use RicorocksDigitalAgency\Soap\Support\Tracing\Trace;

final class Response
{
    /**
     * @var array<string, mixed>
     */
    public array $response;

    private Trace $trace;

    /**
     * @param array<string, mixed> $response
     */
    public static function new(array $response = []): self
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
