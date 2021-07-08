<?php

namespace RicorocksDigitalAgency\Soap\Request;

use GuzzleHttp\Client;
use Http\Client\Common\PluginClient;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Psr\Http\Client\ClientInterface;
use RicorocksDigitalAgency\Soap\Header;
use RicorocksDigitalAgency\Soap\Parameters\Builder;
use RicorocksDigitalAgency\Soap\Response\Response;
use RicorocksDigitalAgency\Soap\Support\Tracing\Trace;
use Soap\Engine\Engine;
use Soap\Engine\Metadata\Collection\MethodCollection;
use Soap\Engine\Metadata\Collection\TypeCollection;
use Soap\Engine\SimpleEngine;
use Soap\Engine\Transport;
use Soap\ExtSoapEngine\AbusedClient;
use Soap\ExtSoapEngine\ExtSoapDriver;
use Soap\ExtSoapEngine\ExtSoapOptionsResolverFactory;
use Soap\ExtSoapEngine\Transport\TraceableTransport;
use Soap\ExtSoapEngine\Wsdl\PassThroughWsdlProvider;
use Soap\Psr18Transport\Psr18Transport;
use SoapHeader;
use Symfony\Component\OptionsResolver\Exception\ExceptionInterface;

class SoapClientRequest implements Request
{
    protected string $endpoint;
    protected string $method;
    protected $body = [];
    protected Builder $builder;
    protected Response $response;
    protected ?Engine $engine = null;
    protected ?Transport $transport = null;
    protected $hooks = [];
    protected $options = [];
    protected $headers = [];

    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    public function to(string $endpoint): Request
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    public function __call($name, $parameters)
    {
        return $this->call($name, $parameters[0] ?? []);
    }

    public function call($method, $parameters = [])
    {
        $this->method = $method;
        $this->body = $parameters;

        $this->hooks['beforeRequesting']->each(fn ($callback) => $callback($this));
        $this->body = $this->builder->handle($this->body);

        $response = $this->getResponse();
        $this->hooks['afterRequesting']->each(fn ($callback) => $callback($this, $response));

        return $response;
    }

    protected function getResponse()
    {
        return $this->response ??= $this->getRealResponse();
    }

    protected function getRealResponse()
    {
        return tap(
            Response::new($this->makeRequest()),
            fn ($response) => data_get($this->options, 'trace')
                ? $response->setTrace(Trace::transport($this->transport))
                : $response
        );
    }

    protected function makeRequest()
    {
        return $this->engine()->request($this->getMethod(), $this->getBody());
    }

    protected function engine(): Engine
    {
        return $this->engine ??= $this->constructEngine();
    }

    /**
     * @throws ExceptionInterface on invalid SOAP options.
     */
    protected function constructEngine(): Engine
    {
        // This wsdl provider is currently just a proxy, but could be made configurable in the request interface
        // It allows you to e.g. use a HTTP client to fetch WSDLs instead of the internal logic in the SoapClient.
        $wsdlProvider = (new PassThroughWsdlProvider());
        $wsdl = $wsdlProvider($this->endpoint);

        $options = ExtSoapOptionsResolverFactory::createForWsdl($wsdl)->resolve($this->options);
        $client = resolve(AbusedClient::class, [
            'wsdl' => $wsdl,
            'options' => $options,
        ]);
        $client->__setSoapHeaders($this->constructHeaders());

        // Not sure how the resolve() method works in laravel.
        // This driver does some parsing *once* when calling __getFunctions() or __getTypes().
        // It is not that slow, but is also not something you want to do multiple times.
        // Is there some way to load a callback through resolve() ?
        // Or doesn't that return a single instance for the provided arguments either?
        $driver = ExtSoapDriver::createFromClient($client);

        return new SimpleEngine(
            $driver,
            $this->transport ??= $this->constructTransport($client)
        );
    }

    protected function constructTransport(AbusedClient $client): Transport
    {
        $transport = Psr18Transport::createForClient($this->constructHttpClient());

        // Besides the Psr18Transport, there is also the regular soap-client transport
        // @see \Soap\ExtSoapEngine\Transport\ExtSoapClientTransport
        // You could conditionally swap these 2 if you prefer that approach


        return data_get($this->options, 'trace')
            ? new TraceableTransport($client, $transport)
            : $transport;

        // FYI : Currently you'll only see the body of the trace for psr-18 transports.
        // The idea for that is that in HTTP clients, you most of the times have better alternatives for inspecting request / responses than using trace().
        // In our old implementation, we provided a HTTP middleware that collected the last trace:
        // @link https://github.com/phpro/soap-client/blob/master/src/Phpro/SoapClient/Middleware/CollectLastRequestInfoMiddleware.php
        // We could add something like that to the client construction part as well.
    }

    protected function constructHttpClient(): ClientInterface
    {
        // I am not sure that using the Http facade has any big advantages over configuring guzzle directly.
        // That's open for discussion. Here is it through facades in any case:

        /** @var Factory $factory */
        $factory = Http::getFacadeRoot();

        // The soap-options could be used to detect basic / digest auth and other HTTP related things.
        // You could add other PendingRequest specific configuration in the request interface.
        // Example:

        /** @var PendingRequest $pendingRequest */
        $pendingRequest = tap(
            $factory
                ->withUserAgent('laravel::soap')  // You could add a user-agent to the request interface.
                ->withOptions([]), // You could add guzzle options to the request interface.
                // ->withMiddleware() // You ge the point right :-)
            function (PendingRequest $request) {
                $user = data_get($this->options, 'login');
                $pass = data_get($this->options, 'password');

                match (data_get($this->options, 'authentication')) {
                    SOAP_AUTHENTICATION_BASIC => $request->withBasicAuth($user, $pass),
                    SOAP_AUTHENTICATION_DIGEST => $request->withDigestAuth($user, $pass),
                    default => $request,
                };
            }
        );

        // Here the choice should be made : do you want to convert all possible soap options to their http alternative?
        // ssl, certificates, encoding, timeouts, keepalive, ...
        // Those are things you would rather configure on the http client IMO.


        // Since we are not really using pending requests and the buildClient() method does not include the options:
        $guzzleClient = new Client(
            $pendingRequest->mergeOptions([
                'handler' => $pendingRequest->buildHandlerStack(),
                'cookies' => true,
                'laravel_data' => [],
            ])
        );

        // On top of guzzle middleware,
        // You could use httplug plugins
        // @link https://docs.php-http.org/en/latest/plugins/
        // The WSSE plugin package in php-soap is e.g. based on httplug plugins as well
        // These plugins could be configurable on request level as well.

        return new PluginClient(
            $guzzleClient,
            plugins: []
        );
    }

    protected function constructHeaders()
    {
        if (empty($this->headers)) {
            return;
        }

        return array_map(
            fn ($header) => resolve(SoapHeader::class, [
                'namespace' => $header->namespace,
                'name' => $header->name,
                'data' => $header->data,
                'mustunderstand' => $header->mustUnderstand,
                'actor' => $header->actor ?? SOAP_ACTOR_NONE,
            ]),
            $this->headers
        );
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function types(): TypeCollection
    {
        return $this->engine()->getMetadata()->getTypes();
    }

    public function functions(): MethodCollection
    {
        return $this->engine()->getMetadata()->getMethods();
    }

    public function beforeRequesting(...$closures): Request
    {
        ($this->hooks['beforeRequesting'] ??= collect())->push(...$closures);

        return $this;
    }

    public function afterRequesting(...$closures): Request
    {
        ($this->hooks['afterRequesting'] ??= collect())->push(...$closures);

        return $this;
    }

    public function getEndpoint()
    {
        return $this->endpoint;
    }

    public function fakeUsing($response): Request
    {
        if (empty($response)) {
            return $this;
        }

        $this->response = $response instanceof Response ? $response : $response($this);

        return $this;
    }

    public function set($key, $value): Request
    {
        data_set($this->body, $key, $value);

        return $this;
    }

    public function trace($shouldTrace = true): Request
    {
        $this->options['trace'] = $shouldTrace;

        return $this;
    }

    public function withBasicAuth($login, $password): Request
    {
        $this->options['authentication'] = SOAP_AUTHENTICATION_BASIC;
        $this->options['login'] = $login;
        $this->options['password'] = $password;

        return $this;
    }

    public function withDigestAuth($login, $password): Request
    {
        $this->options['authentication'] = SOAP_AUTHENTICATION_DIGEST;
        $this->options['login'] = $login;
        $this->options['password'] = $password;

        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function withOptions(array $options): Request
    {
        $this->options = array_merge($this->getOptions(), $options);

        return $this;
    }

    public function withHeaders(Header ...$headers): Request
    {
        $this->headers = array_merge($this->getHeaders(), $headers);

        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}
