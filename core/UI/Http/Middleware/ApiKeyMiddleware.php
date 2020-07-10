<?php

namespace Cordo\Gateway\Core\UI\Http\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ApiKeyMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (current($request->getHeader('X-Api-Key')) !== env('API_KEY')) {
            return new Response('401', [], 'Invalid Api Key');
        }

        return $handler->handle($request);
    }
}
