<?php

use Monolog\Logger;
use Middlewares\ClientIp;
use GuzzleHttp\Psr7\Response;
use Monolog\Handler\StreamHandler;
use Tuupola\Middleware\CorsMiddleware;
use Cordo\Gateway\Core\UI\Http\Dispatcher;
use Cordo\Gateway\Core\UI\Http\Response\JsonResponse;
use LosMiddleware\RateLimit\RateLimitMiddlewareFactory;
use Cordo\Gateway\Core\UI\Http\Middleware\ParsePutRequest;
use Cordo\Gateway\Core\UI\Http\Middleware\ApiKeyMiddleware;
use Cordo\Gateway\Core\UI\Http\Middleware\MessageLogMiddleware;

require __DIR__ . '/../bootstrap/autoload.php';

// bootstapping
$container = require __DIR__ . '/../bootstrap/app.php';

// router
$router = $container->get('router');
$router->addMiddleware(new CorsMiddleware($container->get('config')->get('cors')));
$router->addMiddleware(new ClientIp());
$router->addMiddleware(new ApiKeyMiddleware());
$router->addMiddleware((new RateLimitMiddlewareFactory())($container));
$router->addMiddleware(new ParsePutRequest());
$router->addMiddleware(
    new MessageLogMiddleware(
        env('APP_DEBUG'),
        (new Logger('requests'))->pushHandler(new StreamHandler(storage_path('logs/request.log'), Logger::INFO))
    )
);
$router->addRoute(
    'OPTIONS',
    "/{endpoint:.+}",
    function () {
        return new Response();
    }
);

// register routes here
(new App\Example\Quotes($router, $container, 'https://quotes.rest', 'example'))->register();

// dispatch request
$dispatcher = new Dispatcher(FastRoute\simpleDispatcher($router->routes()), $container);

$response = new JsonResponse($dispatcher->dispatch($container->get('request')));
$response();
