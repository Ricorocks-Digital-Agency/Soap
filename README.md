# Soap

![PHP Composer](https://github.com/Ricorocks-Digital-Agency/Soap/workflows/PHP%20Composer/badge.svg)

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

## Configuration

Configuration of Soap is via the `Soap` facade in the `boot()` method in your service provider.

### Include

Parameters can be set to be included with specific endpoints. These can be `arrays` or [nodes](#nodes)

```php
Soap::include(['credentials' => soap_node(['user' => '...', 'password' => '...'])])->for('...');
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
