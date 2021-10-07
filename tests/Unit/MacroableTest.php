<?php

declare(strict_types=1);

it('is macroable', function () {
    $this->expectExceptionObject(new Exception('You sucessfully called this!'));

    $soap = soap();

    $soap->macro('test', function () {
        throw new Exception('You sucessfully called this!');
    });

    $soap->test();

    $this->fail('An exception should have been thrown!');
});
