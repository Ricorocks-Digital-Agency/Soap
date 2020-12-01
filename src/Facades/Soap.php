<?php


namespace RicorocksDigitalAgency\Soap\Facades;


use Illuminate\Support\Facades\Facade;

class Soap extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'soap';
    }

}