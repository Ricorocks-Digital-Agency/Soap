<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap\Providers;

use Illuminate\Support\ServiceProvider;
use RicorocksDigitalAgency\Soap\Ray\SoapWatcher;

/**
 * @internal
 */
final class SoapRayServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (!class_exists('Spatie\\LaravelRay\\Ray')) {
            return;
        }

        $this->app->singleton(SoapWatcher::class);

        resolve(SoapWatcher::class)->register();
    }
}
