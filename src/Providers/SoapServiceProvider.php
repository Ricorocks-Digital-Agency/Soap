<?php

declare(strict_types=1);

namespace RicorocksDigitalAgency\Soap\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use RicorocksDigitalAgency\Soap\Contracts\Builder;
use RicorocksDigitalAgency\Soap\Contracts\Request;
use RicorocksDigitalAgency\Soap\Parameters\IntelligentBuilder;
use RicorocksDigitalAgency\Soap\Request\SoapPhpRequest;
use RicorocksDigitalAgency\Soap\Soap;
use RicorocksDigitalAgency\Soap\Support\SoapClients\DecoratedClient;

final class SoapServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('soap', fn (Application $app) => $app->make(Soap::class));
        $this->app->bind(Builder::class, IntelligentBuilder::class);
        $this->app->bind(Request::class, fn (Application $app) => new SoapPhpRequest(
            $app->make(Builder::class),
            new DecoratedClient()
        ));
    }

    public function boot(): void
    {
        require_once __DIR__ . '/../helpers.php';
    }
}
