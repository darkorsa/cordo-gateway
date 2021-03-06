<?php

namespace Cordo\Gateway\Core\UI\Http\Cache;

use GuzzleHttp\Psr7\Request;
use Http\Client\Common\PluginClient;
use Http\Factory\Guzzle\StreamFactory;
use Http\Discovery\HttpClientDiscovery;
use Psr\Http\Message\ResponseInterface;
use Http\Client\Common\Plugin\CachePlugin;
use Psr\Http\Message\ServerRequestInterface;
use Cache\Adapter\Doctrine\DoctrineCachePool;

class CacheClient
{
    private $cacheDriver;

    public function __construct(DoctrineCachePool $cacheDriver)
    {
        $this->cacheDriver = $cacheDriver;
    }

    public function sendRequest(
        ServerRequestInterface $request,
        string $url,
        array $headers,
        array $options
    ): ResponseInterface {
        $cachePlugin = CachePlugin::serverCache($this->cacheDriver, new StreamFactory(), $options);

        $pluginClient = new PluginClient(
            HttpClientDiscovery::find(),
            [$cachePlugin]
        );

        return $pluginClient->sendRequest($this->createRequest($request, $url, $headers));
    }

    private function createRequest(ServerRequestInterface $request, string $url, array $headers): Request
    {
        return new Request($request->getMethod(), $url, $headers);
    }
}
