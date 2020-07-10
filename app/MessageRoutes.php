<?php

declare(strict_types=1);

namespace App;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cordo\Gateway\Core\Application\Service\Register\RoutesRegister;

class MessageRoutes extends RoutesRegister
{
    public function register(): void
    {
        $this->router->addRoute(
            'GET',
            "/",
            function (ServerRequestInterface $request, array $params): ResponseInterface {
                return $this->cacheRequest($request, 'http://cordo-dev.test/', 10, []);
                //return $this->sendRequest($request, 'http://cordo-dev.test/', []);
            }
        );
    }
}
