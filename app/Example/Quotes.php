<?php

declare(strict_types=1);

namespace App\Example;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cordo\Gateway\Core\Application\Service\Register\RoutesRegister;

class Quotes extends RoutesRegister
{
    private const CACHE_TTL = 30; // 30 seconds

    public function register(): void
    {
        $this->router->addRoute(
            'GET',
            "/{$this->namespace}/quote",
            function (ServerRequestInterface $request, array $params): ResponseInterface {
                return $this->controller->cacheRequest($request, '/qod', self::CACHE_TTL, []);
            }
        );
    }
}
