<?php

use Cordo\Gateway\Core\UI\Http\Middleware\ImageCacheMiddleware;
use Middlewares\Reader;
use Middlewares\Writer;
use Middlewares\Utils\Dispatcher;
use GuzzleHttp\Psr7\ServerRequest;
use Middlewares\ImageManipulation;
use Symfony\Component\Dotenv\Dotenv;
use Cordo\Gateway\Core\UI\Http\Response\ImageResponse;

require __DIR__ . '/../bootstrap/autoload.php';

$dotenv = new Dotenv();
$dotenv->load(root_path() . '.env');

// Signature key
$key = env('IMAGE_CACHE_SECRET');

// Manipulated images directory
$cachePath = storage_path() . 'cache';

// Original images directory
$imagePath = storage_path();

$dispatcher = new Dispatcher([
    // convert seo friendly image naming convention to format accepted by ImageManipulation class
    new ImageCacheMiddleware($key),

    // read and returns the manipulated image if it's currently cached
    Reader::createFromDirectory($cachePath)->continueOnError(),

    // saves the manipulated images returned by the next middleware
    Writer::createFromDirectory($cachePath),

    // transform the image
    new ImageManipulation($key),

    // read and return a response with original image if exists
    Reader::createFromDirectory($imagePath)->continueOnError(false),
]);

$request = ServerRequest::fromGlobals();
$response = $dispatcher->dispatch($request);

$pathParts = pathinfo($request->getUri()->getPath());

$imageResponse = new ImageResponse($response);
$imageResponse((int) $response->getBody()->getSize(), $pathParts['extension'], 60 * 60 * 24);
