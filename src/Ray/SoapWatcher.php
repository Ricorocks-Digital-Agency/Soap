<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap\Ray;

use RicorocksDigitalAgency\Soap\Contracts\Request;
use RicorocksDigitalAgency\Soap\Facades\Soap;
use RicorocksDigitalAgency\Soap\Response\Response;
use RicorocksDigitalAgency\Soap\Support\Header;
use Spatie\LaravelRay\Ray;
use Spatie\LaravelRay\Watchers\Watcher;
use Spatie\Ray\Ray as SpatieRay;

/**
 * @internal
 */
final class SoapWatcher extends Watcher
{
    public function register(): void
    {
        SpatieRay::macro('showSoapRequests', fn () => app(SoapWatcher::class)->enable());
        SpatieRay::macro('stopShowingSoapRequests', fn () => app(SoapWatcher::class)->disable());

        Soap::afterRequesting(
            function ($request, $response) {
                if (!app(SoapWatcher::class)->enabled()) {
                    return;
                }

                $this->handleRequest($request, $response);
            }
        );
    }

    protected function handleRequest(Request $request, Response $response): void
    {
        app(Ray::class)->table(
            [
                'Endpoint' => $request->getEndpoint(),
                'Method' => $request->getMethod(),
                'Headers' => $this->headers($request),
                'Request' => $request->getBody(),
                'Response' => $response->response,
            ],
            'SOAP'
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function headers(Request $request): array
    {
        return collect($request->getHeaders())->map(fn (Header $header) => $header->toArray())->toArray();
    }
}
