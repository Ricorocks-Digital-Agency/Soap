<?php

namespace RicorocksDigitalAgency\Soap\Ray;

use RicorocksDigitalAgency\Soap\Facades\Soap;
use RicorocksDigitalAgency\Soap\Request\Request;
use RicorocksDigitalAgency\Soap\Response\Response;
use Spatie\LaravelRay\Ray;
use Spatie\LaravelRay\Watchers\Watcher;
use Spatie\Ray\Ray as SpatieRay;

class SoapWatcher extends Watcher
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

    protected function handleRequest(Request $request, Response $response)
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

    protected function headers(Request $request)
    {
        return $request->getHeaders()
            ? collect($request->getHeaders())->map->toArray()->toArray()
            : [];
    }
}
