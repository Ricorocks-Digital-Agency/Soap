# Soap

![Tests](https://github.com/Ricorocks-Digital-Agency/Soap/workflows/Tests/badge.svg)

A Laravel SOAP client that provides a clean interface for handling requests and responses.

## Docs

- [Installation](#installation)
- [Using Soap](#using-soap)
- [Features/API](#features/api)
    * [To](#to)
    * [Functions](#functions)
    * [Call](#call)
        * [Parameters](#parameters)
            * [Nodes](#nodes)
- [Hooks](#hooks)
- [Faking](#faking)            
- [Configuration](#configuration)
    * [Include](#include)



## Installation

You can install the package via composer

```bash
composer require ricorocks-digital-agency/soap
```

## Using Soap

Soap can be accessed through the provided Facade

```php
use RicorocksDigitalAgency\Soap\Facades\Soap

Soap::to()
```

## Features/API

### To

The endpoint to be accessed

```php
Soap::to('github.com/api')
```

### Functions

Retrieve the functions the endpoint provides

```php
Soap::to('github.com/api')->functions()
```

This is a wrapper for the PHP SoapClient `_getFunctions()` method.


### Call

Call the method at the endpoint.

```php
Soap::to('github.com/api')->call('merge')
```

The method can also be called as a Magic Method.

```php
Soap::to('github.com/api')->merge()
```

#### Parameters

The [Call](#call) method of course accepts parameters.
The parameters passed can be an array

```php
Soap::to('github.com/api')->call('merge', ['branch' => 'staging', 'credentials' => ['password' => '...'])
```
##### Nodes

To simplify dealing with SOAP XML in your requests, Soap provides a method to fluently construct the nodes in the request.

For example, say the following node was desired in the XML request. Note it has no body.

```xml
<PullRequest branch="dev" target="main">
</PullRequest>
```

The `array` to pass to the underlying php `SoapClient` to construct this node would be as follows

```php
'PullRequest' => [
    '_' => '',
    'branch' => 'dev',
    'target' => 'main'
]
```
The `_` is required to set the information not as the body, but as the attributes for the node.

However, this is not required if the XML node has a body.

```xml
<PullRequest branch="dev" target="main">
    <Author>Ricorocoks</Author>
</PullRequest>
```
Now, the `array` would be as follows

```php
'PullRequest' => [
    'Author' => 'Ricorocks'
    'branch' => 'dev',
    'target' => 'main'
]
```
So, to prevent confusion, the `Soap::node()` will allow for intelligent construction of the php `array` to be passed to `SoapClient`.

Imagine we are accessing the `information` method to see details about Pull Requests

```php
Soap::to('...')
    ->information('PullRequest' => soap_node(['branch' => 'dev', 'target' => 'main']))

'PullRequest' => [
    '_' => ''
    'branch' => 'dev',
    'target' => 'main'
]

Soap::to('...')
    ->information('PullRequest' => soap_node(['branch' => 'dev', 'target' => 'main'])->body(['Author' => 'Ricorocks']))

'PullRequest' => [
    'Author' => 'Ricorocks'
    'branch' => 'dev',
    'target' => 'main'
]
```
Now, just by adding or removing a body to the `soap_node()` the outputted array is intelligently constructed.

A node can be made with either the Facade `Soap::node()` or the helper method `soap_node()`.

## Hooks

Hooks allow you to perform actions before and after Soap makes a request.
These hooks can be local (per request), or global (applied to every request).

You can make changes to the `Request` object in `beforeRequesting` hooks if you wish. These changes will be reflected in the actual request. In fact, this is how the Soap `include` functionality works.

### Local

To create a local hook, chain `beforeRequesting` or `afterRequesting` to a `Request` object:

```php
Soap::to('http://example.com')
	->beforeRequesting(fn() => Log::info('Request going in!'))
	->afterRequesting(fn() => Log::info('Request coming out!'))
	->call('Action', []);
```

Any before requesting hooks will receive the request as a parameter
and after requesting hooks will receive the request and response
as a parameter.

### Global

To create a global hook, use the `Soap::beforeRequesting` and `Soap::afterRequesting` methods.

```php
Soap::beforeRequesting(fn() => Log::info('Request going in!'));
Soap::afterRequesting(fn() => Log::info('Request coming out!'));
```

Any before requesting hooks will receive the request as a parameter
and after requesting hooks will receive the request and response
as a parameter.

## Faking

Soap includes full support for faking endpoints and actions, as well as
inspecting requests and responses.

To fake all SOAP requests, call `Soap:fake()`. This will return an empty
response for every request. It is likely that you will want to be more
specific, so you can pass the `fake` method an array of endpoints as keys
and response objects as values:

```php
Soap::fake(['http://endpoint.com' => Response(['foo' => 'bar'])]);
```

In the above example, any SOAP request made to `http://endpoint.com` will
be faked, and a `Response` object with a body of `['foo' => 'bar']` will
be returned instead.

What if you want to specify the SOAP action too? Easy! Just add `:{ActionName}` after your endpoint, like so:

```php
Soap::fake(['http://endpoint.com:Details' => Response(['foo' => 'bar'])]);
```
Now, only SOAP requests to the `Details` actions will be mocked.

You can also specify multiple actions with the `|` operator:
```php
Soap::fake(['http://endpoint.com:Details|Information|Overview' => Response(['foo' => 'bar'])]);
```
Now, only SOAP requests to the `Details`, `Information` and `Overview` actions will be mocked.


### Inspecting requests

If you've made a call to `Soap::fake()`, Soap will record all requests made. You can then inspect these requests as you see fit.

#### `Soap::assertSentCount($count)`

If you just want to assert that `n` amount of SOAP requests were sent,
you can use this method, passing in the desired count as a parameter.

#### `Soap::assertSent(callable $callback)`

You can dive a little deeper and test that a particular request was 
actually sent, and that it returned the expected response. You should
pass a closure into this method, which receives the `$request` and `$response` as parameters, and return `true` if they match your
expectations.

#### `Soap::assertNotSent(callable $callback)`

This is the opposite of `Soap::assertSent`. You can make sure that a 
particular request wasn't made. Again, returning `true` from the
closure will cause it to pass.

#### `Soap::assertNothingSent()`

If you just want to make sure that absolutely nothing was sent out, you 
can call this. It does what it says on the tin.

## Configuration

Configuration of Soap is via the `Soap` facade in the `boot()` method in your service provider.

### Include

Parameters can be set to be included with specific endpoints. These can be `arrays` or [nodes](#nodes)

```php
Soap::include(['credentials' => soap_node(['user' => '...', 'password' => '...'])])->for('...');
```

You can even use dot syntax on your array keys to permeate deeper into the request body.

```php
Soap::include(['login.credentials' => soap_node(['user' => '...', 'password' => '...'])])->for('...');
``` 


### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email hello@ricorocks.agency instead of using the issue tracker.

## Credits

- [Ricorocks Digital Agency](https://github.com/ricorocks-digital-agency)
- [Luke Downing](https://github.com/lukeraymonddowning)
- [Sam Rowden](https://github.com/nedwors)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
