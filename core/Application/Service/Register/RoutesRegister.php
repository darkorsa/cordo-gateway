<?php

declare(strict_types=1);

namespace Cordo\Gateway\Core\Application\Service\Register;

use Psr\Container\ContainerInterface;
use Cordo\Gateway\Core\UI\Http\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cordo\Gateway\Core\UI\Http\Cache\CacheClientFactory;

abstract class RoutesRegister
{
    protected const UUID_PATTERN = '{id:[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}}';

    protected $router;

    protected $container;

    public function __construct(Router $router, ContainerInterface $container)
    {
        $this->router = $router;
        $this->container = $container;
    }

    protected function cacheRequest(
        ServerRequestInterface $request,
        string $url,
        int $ttl,
        array $headers,
        array $options = []
    ): ResponseInterface {
        $options = array_merge([
            'default_ttl' => $ttl, // cache lifetime time in seconds
            'respect_response_cache_directives' => [],
        ], $options);

        return CacheClientFactory::create('redis')->sendRequest($request, $url, $headers, $options);
    }

    abstract public function register(): void;
}
