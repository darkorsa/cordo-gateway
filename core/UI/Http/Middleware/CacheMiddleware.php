<?php

namespace Cordo\Gateway\Core\UI\Http\Middleware;

use GuzzleHttp\Psr7\Message;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Cache\Adapter\Doctrine\DoctrineCachePool;
use Cordo\Gateway\Core\Infractructure\Persistance\Doctrine\Cache\CachePoolFactory;

class CacheMiddleware implements MiddlewareInterface
{
    private DoctrineCachePool $cachePool;

    /**
     * Cache lifetime in seconds
     */
    private int $ttl;

    public function __construct(int $ttl)
    {
        $this->ttl = $ttl;
        $this->cachePool = CachePoolFactory::create('redis');
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getMethod() !== 'GET') {
            return $handler->handle($request);
        }

        $item = $this->cachePool->getItem($this->getCacheKey($request));

        if ($item->get()) {
            return Message::parseResponse($item->get());
        }

        $response = $handler->handle($request);

        $this->cachePool->save(
            $item
                ->set(Message::toString($response))
                ->expiresAfter($this->ttl)
        );

        return $response;
    }

    private function getCacheKey(ServerRequestInterface $request): string
    {
        return md5(serialize($request->getUri()));
    }
}
