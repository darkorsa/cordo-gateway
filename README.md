# cordo-gateway

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Cordo API Gateway - protection, caching, analytics & monitoring.

## What is it? What it's for?

An API gateway is an API management tool that acts as reverse proxy and is located between a client and a backend service (or collection of services).

Most common use cases:
- protection from overuse and abuse
- analytics and monitoring
- gathering multiple API calls into one
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

Next create your new project folder and within this folder type:

``` bash
$ composer create-project darkorsa/cordo-gateway ./
```

Next copy `.env_example` file and rename it to `.env`. Then complete it with your configuration data.

## Usage



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
