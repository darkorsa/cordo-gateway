<?php

declare(strict_types=1);

namespace Cordo\Gateway\Core\Application\Service\Register;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
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

    protected $config;

    public function __construct(Router $router, ContainerInterface $container)
    {
        $this->router = $router;
        $this->container = $container;
        $this->config = $container->get('config');
    }

    protected function cacheRequest(
        ServerRequestInterface $request,
        string $endpoint,
        int $ttl,
        array $headers,
        array $options = []
    ): ResponseInterface {
        if (!in_array($request->getMethod(), ['GET', 'HEAD'])) {
            throw new Exception('You can only cache GET or HEAD requests');
        }

        // options
        $options = array_merge([
            'default_ttl' => $ttl, // cache ttl time in seconds
            'respect_response_cache_directives' => [],
            'cache_lifetime' => $this->config->get('cache.cache_lifetime'),
            'methods' => ['GET', 'HEAD'],
        ], $options);

        return CacheClientFactory::create('redis')->sendRequest(
            $request,
            $this->buildUrl($request, $endpoint),
            $headers,
            $options
        );
    }

    protected function sendRequest(
        ServerRequestInterface $request,
        string $endpoint,
        array $headers,
        array $options = []
    ) {
        $newRequest = new Request($request->getMethod(), $this->buildUrl($request, $endpoint), $headers);

        if (in_array($request->getMethod(), ['POST', 'PUT'])) {
            $options['form_params'] = $request->getParsedBody();
        }

        // options
        $options = array_merge([
            'http_errors' => false,
        ], $options);

        return (new Client())->send($newRequest, $options);
    }

    private function buildUrl(ServerRequestInterface $request, string $endpoint)
    {
        $queryParams = $request->getQueryParams()
            ? '?' . http_build_query($request->getQueryParams())
            : '';

        return $endpoint . $queryParams;
    }

    abstract public function register(): void;
}
