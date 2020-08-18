<?php

namespace Cordo\Gateway\Core\UI\Http;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cordo\Gateway\Core\UI\Http\Cache\CacheClientFactory;
use Noodlehaus\Config;

class Controller
{
    private $apiUrl;

    private $config;
    
    public function __construct(string $apiUrl, Config $config)
    {
        $this->apiUrl = $apiUrl;
        $this->config = $config;
    }
    
    public function cacheRequest(
        ServerRequestInterface $request,
        string $endpoint,
        int $ttl,
        array $headers,
        array $options = []
    ): ResponseInterface {
        if (!in_array($request->getMethod(), ['GET', 'HEAD'])) {
            throw new Exception('You can only cache GET or HEAD requests');
        }

        if ($request->getAttribute('cache') === false) {
            return $this->sendRequest($request, $endpoint, $headers, $options);
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

    public function sendRequest(
        ServerRequestInterface $request,
        string $endpoint,
        array $headers,
        array $options = []
    ): ResponseInterface {
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

        return $this->apiUrl . $endpoint . $queryParams;
    }
}
