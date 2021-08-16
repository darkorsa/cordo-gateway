<?php

declare(strict_types=1);

namespace App\Example;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cordo\Gateway\Core\UI\Http\RoutesRegister;
use Cordo\Gateway\Core\UI\Http\Middleware\CacheMiddleware;

class Quotes extends RoutesRegister
{
    private const CACHE_TTL = 30; // 30 seconds

    public function register(): void
    {
        $this->router->addRoute(
            'GET',
            "/{$this->namespace}/quote",
            function (ServerRequestInterface $request, array $params): ResponseInterface {
                return $this->apiRequester->sendRequest($request, '/qod', []);
            },
            [new CacheMiddleware(self::CACHE_TTL)]
        );
    }
}
