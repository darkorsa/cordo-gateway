<?php

namespace Cordo\Gateway\Core\UI\Http\Middleware;

use Psr\Log\LoggerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PhpMiddleware\LogHttpMessages\LogMiddleware;
use Cordo\Gateway\Core\UI\Http\Log\MessageFormatter;

class MessageLogMiddleware implements MiddlewareInterface
{
    private $debug;

    private $middleware;

    public function __construct(bool $debug, LoggerInterface $logger)
    {
        $this->debug = $debug;
        $this->middleware = new LogMiddleware(
            new MessageFormatter(),
            new MessageFormatter(),
            $logger
        );
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->debug) {
            return $this->middleware->process($request, $handler);
        }

        return $handler->handle($request);
    }
}
