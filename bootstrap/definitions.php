<?php

use Noodlehaus\Config;
use GuzzleHttp\Psr7\Response;
use Psr\SimpleCache\CacheInterface;
use Cordo\Gateway\Core\UI\Http\Router;
use Psr\Http\Message\ResponseInterface;
use Doctrine\Common\Cache\MemcachedCache;
use Psr\Http\Message\ServerRequestInterface;
use Cache\Adapter\Doctrine\DoctrineCachePool;
use Cordo\Gateway\Core\Application\Config\Parser;
use Mezzio\ProblemDetails\ProblemDetailsResponseFactory;
use Cordo\Gateway\Core\Application\Error\ErrorReporterInterface;

return [
    'config' => DI\factory(static function () {
        $config = new Config(config_path(), new Parser());
        $config->merge(new Config(config_path() . 'los_rate_limit.php'));
        return $config;
    }),
    'request' => DI\get(ServerRequestInterface::class),
    'router' => DI\get(Router::class),
    ServerRequestInterface::class => DI\factory('GuzzleHttp\Psr7\ServerRequest::fromGlobals'),
    ErrorReporterInterface::class => DI\get('error_reporter'),
    Config::class => DI\get('config'),
    CacheInterface::class => DI\factory(static function () {
        $memcached = new Memcached();
        $memcached->addServer(env('MEMCACHED_SERVER'), (int) env('MEMCACHED_PORT'));

        $cacheDriver = new MemcachedCache();
        $cacheDriver->setMemcached($memcached);

        return new DoctrineCachePool($cacheDriver);
    }),
    ProblemDetailsResponseFactory::class => DI\factory(static function () {
        $callable = function (): ResponseInterface {
            return new Response();
        };
        return new ProblemDetailsResponseFactory($callable);
    }),
];
