<?php

namespace RicorocksDigitalAgency\Soap\Providers;

use Illuminate\Support\ServiceProvider;
use RicorocksDigitalAgency\Soap\Soap;

class SoapServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton('soap', app(Soap::class));
    }

}