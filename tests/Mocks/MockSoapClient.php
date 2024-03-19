<?php

namespace RicorocksDigitalAgency\Soap\Tests\Mocks;

class MockSoapClient
{
    protected $shouldTrace = false;

    /* Methods */
    public function __construct($wsdl, $options = [])
    {
        if ($options['trace'] ?? false) {
            $this->shouldTrace = true;
        }
    }

    public function __call(string $function_name, array $arguments)
    {
    }

    public function __doRequest(string $request, string $location, string $action, int $version, $one_way = 0)
    {
    }

    public function __getCookies()
    {
    }

    public function __getFunctions()
    {
        return [
            'The mock client does not actually have functions!',
        ];
    }

    public function __getLastRequest()
    {
        if (!$this->shouldTrace) {
            return null;
        }

        return '<?xml version="1.0" encoding="UTF-8"?><FooBar><Hello>World</Hello></FooBar>';
    }

    public function __getLastRequestHeaders()
    {
        if (!$this->shouldTrace) {
            return null;
        }

        return 'Hello World';
    }

    public function __getLastResponse()
    {
        if (!$this->shouldTrace) {
            return null;
        }

        return '<?xml version="1.0" encoding="UTF-8"?><Status>Success!</Status>';
    }

    public function __getLastResponseHeaders()
    {
        if (!$this->shouldTrace) {
            return null;
        }

        return 'Foo Bar';
    }

    public function __getTypes()
    {
    }

    public function __setCookie(string $name, ?string $value = null)
    {
    }

    public function __setLocation(?string $new_location = null)
    {
    }

    public function __setSoapHeaders($soapheaders)
    {
    }

    public function __soapCall(
        string $function_name,
        array $arguments,
        array $options = [],
        $input_headers = [],
        &$output_headers = []
    ) {
    }

    public function SoapClient(mixed $wsdl, array $options = [])
    {
        return new static($wsdl, $options);
    }
}
