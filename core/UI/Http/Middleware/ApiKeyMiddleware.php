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
        $xApiKey = current($request->getHeader('X-Api-Key'));
        
        if (!in_array($xApiKey, [env('API_KEY') , env('DEV_API_KEY')])) {
            return new Response('401', [], 'Invalid Api Key');
        }

        // disable cache for dev env
        if ($xApiKey === env('DEV_API_KEY')) {
            $request = $request->withAttribute('cache', false);
        }

        return $handler->handle($request);
    }
}
