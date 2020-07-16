# cordo-gateway

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Cordo API Gateway - protection, caching, analytics & monitoring.

## What is it? What is it for?

An API gateway is an API management tool that acts as reverse proxy and is located between a client and a backend service (or collection of services).

Most common use cases:
- protection from overuse and abuse
- analytics and monitoring
- gathering multiple API calls into one for optimization
- caching

## Requirements

- PHP 7.4.0 or newer
- Apache/Nginx
- PHP Memcached extension (used for rate limiting)
- PHP Redis extension (used for request caching)

## Install

First make sure that you have *Memcached* and *Redis* expansions installed. To check it run:

``` bash
$ php -i |grep redis
$ php -i |grep memcached
```
If any of them is missing you can install it from [PECL](https://pecl.php.net/) repository:

``` bash
$ sudo pecl install redis
$ sudo pecl install memcached

# restart PHP
$ sudo service php7.4-fpm restart
```

Create your new project folder and within this folder type:

``` bash
$ composer create-project darkorsa/cordo-gateway ./
```

Then copy `.env_example` file and rename it to `.env` and complete it with your configuration data.

## Usage

### Api Key

First set your `API_KEY` in `.env` file, that will extort sending `X-Api-Key` header with `API_KEY` value on each request to the gateway.

This can disabled by commenting `ApiKeyMiddleware` middleware in *public/index.php*.

``` php
$router->addMiddleware(new ApiKeyMiddleware()); // comment this line
```

> Note that X-Api-Key can be also used by rate limiting function.

### Defining routes

Since the API gateway is a proxy for the target API you will need to register routes for handling the upcoming requests. This can be done in *public/index.php*:

``` php
(new App\UsersRoutes($router, $container, 'https://apiurladdress.com'))->register();
```

Place you routes class somewhere in *app/* folder.

Example of routes class:

``` php
<?php

declare(strict_types=1);

namespace App;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cordo\Gateway\Core\Application\Service\Register\RoutesRegister;

class UsersRoutes extends RoutesRegister
{
    public function register(): void
    {
        $this->router->addRoute(
            'GET',
            "/users",
            function (ServerRequestInterface $request, array $params): ResponseInterface {
                return $this->cacheRequest($request, '/users', 3600, []);
            }
        );

        $this->router->addRoute(
            'POST',
            "/users",
            function (ServerRequestInterface $request, array $params): ResponseInterface {
                return $this->sendRequest($request, '/users', []);
            }
        );
    }
}
```
In the above example there are definitions of two endpoints. One for fetching users data (*GET*) and second for adding a new user (*POST*).

`CacheRequest` method will call target API and cache the result in memory (*Redis*) for the set amount of time. In this example for 1 hour (3600 seconds). Until cache invalidates no request will be made to the target API.

`SendRequest` method will simple call target API with all the query & form params as in the original request.

### Rate Limiting

You can limit requests that can be made to you API in given amount of time in order to prevent from request flooding / API scraping. For that set appropriate config settings in *config/los_rate_limit.php* file.

Rate limiting config settings are explained [here](https://github.com/Lansoweb/LosRateLimit-author).

## Still to come

- Simple request logger for analytics

## Security

If you discover any security related issues, please email dkorsak@gmail.com instead of using the issue tracker.

## Credits

- [Dariusz Korsak][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/darkorsa/cordo-gateway.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/darkorsa/cordo-gateway/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/darkorsa/cordo-gateway.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/darkorsa/cordo-gateway.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/darkorsa/cordo-gateway.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/darkorsa/cordo-gateway
[link-travis]: https://travis-ci.org/darkorsa/cordo-gateway
[link-scrutinizer]: https://scrutinizer-ci.com/g/darkorsa/cordo-gateway/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/darkorsa/cordo-gateway
[link-downloads]: https://packagist.org/packages/darkorsa/cordo-gateway
[link-author]: https://github.com/darkorsa
[link-contributors]: ../../contributors
