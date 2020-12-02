<?php


namespace RicorocksDigitalAgency\Soap;


use PHPUnit\Framework\Assert as PHPUnit;
use RicorocksDigitalAgency\Soap\Parameters\Node;
use RicorocksDigitalAgency\Soap\Request\Request;
use RicorocksDigitalAgency\Soap\Response\Response;

class Soap
{
    protected $inclusions = [];
    protected $recordRequests = false;
    protected $recordedRequests = [];
    protected $stubCallbacks = [];

    public function to(string $endpoint)
    {
        return app(Request::class)
            ->beforeRequesting(fn($request) => $this->checkForMock($request))
            ->afterRequesting(fn($request, $response) => $this->record($request, $response))
            ->to($endpoint);
    }

    protected function checkForMock(Request $request)
    {
        return collect($this->stubCallbacks)
            ->reverse()
            ->map
            ->__invoke($request)
            ->filter()
            ->first();
    }

    public function record(Request $request, Response $response)
    {
        if (!$this->recordRequests) {
            return;
        }

        $this->recordedRequests[] = [$request, $response];
    }

    public function node($attributes = []): Node
    {
        return new Node($attributes);
    }

    public function include($parameters)
    {
        $inclusion = new Inclusion($parameters);
        $this->inclusions[] = $inclusion;
        return $inclusion;
    }

    public function inclusionsFor(string $endpoint, $method = null)
    {
        return collect($this->inclusions)->filter->matches($endpoint, $method);
    }

    public function fake($callback = null)
    {
        $this->recordRequests = true;

        if (is_null($callback)) {
            $this->stubCallbacks = array_merge([fn() => new Response()], $this->stubCallbacks);
        }
    }

    public function assertNothingSent()
    {
        PHPUnit::assertEmpty($this->recordedRequests, "Requests were recorded");
        return new static;
    }

    public function assertSentCount($count)
    {
        PHPUnit::assertCount($count, $this->recordedRequests);
        return new static;
    }
}