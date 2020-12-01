<?php

namespace RicorocksDigitalAgency\Soap\Providers;

use Illuminate\Support\ServiceProvider;
use RicorocksDigitalAgency\Soap\Request\Request;
use RicorocksDigitalAgency\Soap\Soap;
use RicorocksDigitalAgency\Soap\SoapClientRequest;

class SoapServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton('soap', fn() => app(Soap::class));
        $this->app->bind(Request::class, SoapClientRequest::class);
    }

}