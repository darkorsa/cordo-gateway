<?php

declare(strict_types=1);

namespace Cordo\Gateway\Core\Support;

use Exception;
use GuzzleHttp\Client;
use Noodlehaus\Config;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cordo\Gateway\Core\UI\Http\Cache\CacheClient;
use Cordo\Gateway\Core\UI\Http\Cache\CacheClientFactory;

class ApiRequester
{
    private $apiUrl;

    private $config;

    private $client;

    private $cacheClient;

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

        return $this->getCacheClient()->sendRequest(
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

        return $this->getClient()->send($newRequest, $this->setRequestOptions($request, $options));
    }

    private function setRequestOptions(ServerRequestInterface $request, array $options): array
    {
        $options = array_merge([
            'http_errors' => false,
        ], $options);
        
        if (!in_array($request->getMethod(), ['POST', 'PUT'])) {
            return $options;
        }

        if (array_key_exists('json', $options)) {
            return $options;
        }

        if (!empty($request->getUploadedFiles())) {
            $multipart = [];
            foreach ($request->getUploadedFiles() as $name => $file) {
                $multipart[] = [
                    'name' => $name,
                    'contents' => $file->getStream(),
                    'filename' => $file->getClientFilename(),
                    'headers' => [
                        'Content-Type' => $file->getClientMediaType(),
                    ],
                ];
            }
            foreach ((array) $request->getParsedBody() as $name => $contents) {
                $multipart[] = [
                    'name' => $name,
                    'contents' => $contents,
                ];
            }
            $options['multipart'] = $multipart;
        } else {
            $options['form_params'] = $request->getParsedBody();
        }

        return $options;
    }

    /**
     * Lazy load cache client and prevent creating multiple instances
     *
     * @return CacheClient
     */
    private function getCacheClient(): CacheClient
    {
        if (!$this->cacheClient) {
            $this->cacheClient = CacheClientFactory::create($this->config->get('cache.cache_driver'));
        }

        return $this->cacheClient;
    }

    /**
     * Lazy load client and prevent creating multiple instances
     *
     * @return Client
     */
    private function getClient(): Client
    {
        if (!$this->client) {
            $this->client = new Client();
        }

        return $this->client;
    }

    private function buildUrl(ServerRequestInterface $request, string $endpoint)
    {
        $queryParams = $request->getQueryParams()
            ? '?' . http_build_query($request->getQueryParams())
            : '';

        return $this->apiUrl . $endpoint . $queryParams;
    }
}
