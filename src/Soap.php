<?php


namespace RicorocksDigitalAgency\Soap;


use Closure;
use Illuminate\Support\Str;
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
    protected $globalHooks = [
        'beforeRequesting' => [],
        'afterRequesting' => [],
    ];

    public function __construct()
    {
        $this->beforeRequesting(fn($request) => $this->checkForMock($request));
        $this->afterRequesting(fn($request, $response) => $this->record($request, $response));
    }

    public function to(string $endpoint)
    {
        return app(Request::class)
            ->beforeRequesting(...$this->globalHooks['beforeRequesting'])
            ->afterRequesting(...$this->globalHooks['afterRequesting'])
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
            return new static;
        }

        if (is_array($callback)) {
            foreach ($callback as $url => $callable) {
                $this->stubCallbacks[] = $this->stubEndpoint($url, $callable);
            }
        }
    }

    protected function stubEndpoint($url, $callable)
    {
        return function (Request $request) use ($url, $callable) {
            $pieces = [
                Str::of($url)->start('*')->replaceMatches("/:([\w\d]+$)/", ""),
                Str::of($url)->afterLast(".")->match("/:([\w\d]+$)/")->start("*")
            ];

            if (!Str::is($pieces[0]->__toString(), $request->getEndpoint())) {
                return;
            }

            if ($pieces[1]->isNotEmpty() && !Str::is($pieces[1]->__toString(), $request->getMethod())) {
                return;
            }

            return $callable instanceof Closure ? $callable($request) : $callable;
        };
    }

    public function beforeRequesting(callable $hook)
    {
        $this->globalHooks['beforeRequesting'][] = $hook;
        return $this;
    }

    public function afterRequesting(callable $hook)
    {
        $this->globalHooks['afterRequesting'][] = $hook;
        return $this;
    }

    public function assertNothingSent()
    {
        PHPUnit::assertEmpty($this->recordedRequests, "Requests were recorded");
        return new static;
    }

    public function assertSent(callable $callback)
    {
        PHPUnit::assertTrue($this->recorded($callback)->isNotEmpty());
    }

    protected function recorded($callback)
    {
        if (empty($this->recordedRequests)) {
            return collect();
        }

        $callback = $callback ?: function () {
            return true;
        };

        return collect($this->recordedRequests)->filter(fn($pair) => $callback(...$pair));
    }

    public function assertNotSent(callable $callback)
    {
        PHPUnit::assertTrue($this->recorded($callback)->isEmpty());
    }

    public function assertSentCount($count)
    {
        PHPUnit::assertCount($count, $this->recordedRequests);
        return new static;
    }
}