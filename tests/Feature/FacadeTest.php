<?php

declare(strict_types=1);

use RicorocksDigitalAgency\Soap\Facades\Soap;

it('can create a new fake response', function () {
    Soap::fake(['*' => Soap::response(['foo' => 'bar'])]);

    $response = Soap::to(EXAMPLE_SOAP_ENDPOINT)->call('hello');

    expect($response->response)->toEqual(['foo' => 'bar']);
});
