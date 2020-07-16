<?php

use Cordo\Gateway\Core\UI\Http\Dispatcher;
use Cordo\Gateway\Core\UI\Http\Middleware\ApiKeyMiddleware;
use Cordo\Gateway\Core\UI\Http\Response\JsonResponse;
use LosMiddleware\RateLimit\RateLimitMiddlewareFactory;
use Cordo\Gateway\Core\UI\Http\Middleware\ParsePutRequest;

require __DIR__ . '/../bootstrap/autoload.php';

// bootstapping
$container = require __DIR__ . '/../bootstrap/app.php';

// router
$router = $container->get('router');
$router->addMiddleware(new ApiKeyMiddleware());
$router->addMiddleware((new RateLimitMiddlewareFactory())($container));
$router->addMiddleware(new ParsePutRequest());

// register routes here
// ...

// dispatch request
$dispatcher = new Dispatcher(FastRoute\simpleDispatcher($router->routes()), $container);

$response = new JsonResponse($dispatcher->dispatch($container->get('request')));
$response->send();
