<?php

use Middlewares\Reader;
use Middlewares\Writer;
use Middlewares\Utils\Dispatcher;
use GuzzleHttp\Psr7\ServerRequest;
use Middlewares\ImageManipulation;

require __DIR__ . '/../bootstrap/autoload.php';

//You need a signature key
$key = 'sdf6&-$<@#asf';

//Manipulated images directory
$cachePath = storage_path() . 'cache';

//Original images directory
$imagePath = storage_path();

dd(ServerRequest::fromGlobals());

$dispatcher = new Dispatcher([
    //read and returns the manipulated image if it's currently cached
    Reader::createFromDirectory($cachePath)->continueOnError(),

    //saves the manipulated images returned by the next middleware
    Writer::createFromDirectory($cachePath),

    //transform the image
    new ImageManipulation($key),

    //read and return a response with original image if exists
    Reader::createFromDirectory($imagePath)->continueOnError(),
]);

$response = $dispatcher->dispatch(ServerRequest::fromGlobals());

$metadata = $response->getBody()->getMetadata();
$pathParts = pathinfo($metadata['uri']);

header('Content-Type:' . "image/{$pathParts['extension']}");
header('Content-Length: ' . $response->getBody()->getSize());

echo (string) $response->getBody();
exit;
