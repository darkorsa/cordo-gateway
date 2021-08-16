<?php

use Monolog\Logger;
use Noodlehaus\Config;
use Psr\Log\LoggerInterface;
use GuzzleHttp\Psr7\Response;
use Monolog\Handler\StreamHandler;
use Psr\SimpleCache\CacheInterface;
use Cordo\Gateway\Core\UI\Http\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cordo\Gateway\Core\Application\Config\Parser;
use Mezzio\ProblemDetails\ProblemDetailsResponseFactory;
use Cordo\Gateway\Core\Application\Error\ErrorReporterInterface;
use Cordo\Gateway\Core\Infractructure\Persistance\Cache\CachePoolFactory;

return [
    'config' => DI\factory(static function () {
        $config = new Config(config_path(), new Parser());
        $config->merge(new Config(config_path() . 'los_rate_limit.php'));
        return $config;
    }),
    'request' => DI\get(ServerRequestInterface::class),
    'router' => DI\get(Router::class),
    'logger' => DI\factory(static function () {
        return (new Logger('debug'))->pushHandler(new StreamHandler(storage_path('logs/debug.log'), Logger::DEBUG));
    }),
    ServerRequestInterface::class => DI\factory('GuzzleHttp\Psr7\ServerRequest::fromGlobals'),
    ErrorReporterInterface::class => DI\get('error_reporter'),
    Config::class => DI\get('config'),
    CacheInterface::class => DI\factory(static function () {
        return CachePoolFactory::create('redis');
    }),
    LoggerInterface::class => DI\get('logger'),
    ProblemDetailsResponseFactory::class => DI\factory(static function () {
        $callable = function (): ResponseInterface {
            return new Response();
        };
        return new ProblemDetailsResponseFactory($callable);
    }),
];
