<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap;

use Illuminate\Support\Collection;
use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Support\Traits\Macroable;
use RicorocksDigitalAgency\Soap\Contracts\Request;
use RicorocksDigitalAgency\Soap\Parameters\Node;
use RicorocksDigitalAgency\Soap\Response\Response;
use RicorocksDigitalAgency\Soap\Support\Fakery\Fakery;
use SoapVar;

/**
 * @mixin Fakery
 */
final class Soap
{
    use ForwardsCalls;
    use Macroable {
        __call as __macroableCall;
    }

    protected Fakery $fakery;

    protected Request $request;

    /**
     * @var array<int, HeaderSet>
     */
    protected array $headerSets = [];

    /**
     * @var array<int, Inclusion>
     */
    protected array $inclusions = [];

    /**
     * @var array<int, OptionSet>
     */
    protected array $optionsSets = [];

    /**
     * @var array{beforeRequesting: array<int, callable(Request): mixed>, afterRequesting: array<int, callable(Request, Response): mixed>}
     */
    protected array $globalHooks = [
        'beforeRequesting' => [],
        'afterRequesting' => [],
    ];

    public function __construct(Fakery $fakery, Request $request)
    {
        $this->fakery = $fakery;
        $this->request = $request;

        $this->beforeRequesting(fn ($requestInstance) => $requestInstance->fakeUsing($this->fakery->mockResponseIfAvailable($requestInstance)))
            ->beforeRequesting(fn ($requestInstance) => $this->mergeHeadersFor($requestInstance))
            ->beforeRequesting(fn ($requestInstance) => $this->mergeInclusionsFor($requestInstance))
            ->beforeRequesting(fn ($requestInstance) => $this->mergeOptionsFor($requestInstance))
            ->afterRequesting(fn ($requestInstance, $response) => $this->record($requestInstance, $response));
    }

    public function to(string $endpoint): Request
    {
        return (clone $this->request)
            ->beforeRequesting(...$this->globalHooks['beforeRequesting'])
            ->afterRequesting(...$this->globalHooks['afterRequesting'])
            ->to($endpoint);
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function node(array $attributes = []): Node
    {
        return new Node($attributes);
    }

    /**
     * @param array<string, mixed>|SoapVar|null $data
     */
    public function header(?string $name = null, ?string $namespace = null, array|SoapVar $data = null, bool $mustUnderstand = false, string $actor = null): Header
    {
        return new Header($name, $namespace, $data, $mustUnderstand, $actor);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function include(array $parameters): Inclusion
    {
        $inclusion = new Inclusion($parameters);
        $this->inclusions[] = $inclusion;

        return $inclusion;
    }

    /**
     * @param array<string, mixed> $options
     */
    public function options(array $options): OptionSet
    {
        $options = new OptionSet($options);
        $this->optionsSets[] = $options;

        return $options;
    }

    public function headers(Header ...$headers): HeaderSet
    {
        $headers = new HeaderSet(...$headers);
        $this->headerSets[] = $headers;

        return $headers;
    }

    protected function mergeHeadersFor(Request $request): void
    {
        collect($this->headerSets)
            ->filter(fn (HeaderSet $set) => $set->matches($request->getEndpoint(), $request->getMethod()))
            ->flatMap(fn (HeaderSet $set) => $set->getHeaders())
            ->pipe(fn (Collection $headers) => $request->withHeaders(...$headers));
    }

    protected function mergeInclusionsFor(Request $request): void
    {
        collect($this->inclusions)
            ->filter(fn (Inclusion $inclusion) => $inclusion->matches($request->getEndpoint(), $request->getMethod()))
            ->flatMap(fn (Inclusion $inclusion) => $inclusion->getParameters())
            ->each(fn (mixed $value, string $key) => $request->set($key, $value));
    }

    protected function mergeOptionsFor(Request $request): void
    {
        collect($this->optionsSets)
            ->filter(fn (OptionSet $set) => $set->matches($request->getEndpoint(), $request->getMethod()))
            ->map(fn (OptionSet $set) => $set->getOptions())
            ->each(fn (array $options) => $request->withOptions($options));
    }

    public function beforeRequesting(callable $hook): self
    {
        $this->globalHooks['beforeRequesting'][] = $hook;

        return $this;
    }

    public function afterRequesting(callable $hook): self
    {
        $this->globalHooks['afterRequesting'][] = $hook;

        return $this;
    }

    public function trace(bool $shouldTrace = true): self
    {
        $this->beforeRequesting(fn ($request) => $request->trace($shouldTrace));

        return $this;
    }

    /**
     * @param array<int, mixed> $parameters
     */
    public function __call(string $method, array $parameters): mixed
    {
        if (static::hasMacro($method)) {
            return $this->__macroableCall($method, $parameters);
        }

        return $this->forwardCallTo($this->fakery, $method, $parameters);
    }
}
