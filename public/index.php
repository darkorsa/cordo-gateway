<?php

/**
 * Handling HTTP request
 */
use Cordo\Gateway\Core\UI\Http\Dispatcher;
use Cordo\Gateway\Core\UI\Http\Response\JsonResponse;
use Cordo\Gateway\Core\UI\Http\Middleware\ParsePutRequest;

require __DIR__ . '/../bootstrap/autoload.php';

// bootstapping
$container = require_once __DIR__ . '/../bootstrap/app.php';

// router
$router = $container->get('router');
$router->addMiddleware(new ParsePutRequest());

// dispatch request
$dispatcher = new Dispatcher(FastRoute\simpleDispatcher($router->routes()), $container);

$response = new JsonResponse($dispatcher->dispatch($container->get('request')));
$response->send();
