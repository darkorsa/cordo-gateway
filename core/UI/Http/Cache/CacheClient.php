<?php

namespace Cordo\Gateway\Core\UI\Http\Cache;

use GuzzleHttp\Psr7\Request;
use Http\Client\Common\PluginClient;
use Psr\Cache\CacheItemPoolInterface;
use Http\Factory\Guzzle\StreamFactory;
use Http\Discovery\HttpClientDiscovery;
use Psr\Http\Message\ResponseInterface;
use Cache\Taggable\TaggablePSR6PoolAdapter;
use Psr\Http\Message\ServerRequestInterface;

class CacheClient
{
    private $cacheDriver;

    public function __construct(CacheItemPoolInterface $cacheDriver)
    {
        $this->cacheDriver = TaggablePSR6PoolAdapter::makeTaggable($cacheDriver);
    }

    public function sendRequest(
        ServerRequestInterface $request,
        string $url,
        array $headers,
        array $options
    ): ResponseInterface {
        $cachePlugin = TaggableCachePlugin::serverCache($this->cacheDriver, new StreamFactory(), $options);

        /** @phpstan-ignore-next-line */ 
        $pluginClient = new PluginClient(HttpClientDiscovery::find(), [$cachePlugin]);

        return $pluginClient->sendRequest($this->createRequest($request, $url, $headers));
    }

    private function createRequest(ServerRequestInterface $request, string $url, array $headers): Request
    {
        return new Request($request->getMethod(), $url, $headers);
    }
}
