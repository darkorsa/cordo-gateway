<?php

use DI\ContainerBuilder;
use Cordo\Gateway\Core\SharedKernel\Enum\Env;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(root_path() . '.env');

// Errors
$errorReporter = require __DIR__ . '/error.php';

/**
 * @var $container Psr\Container\ContainerInterface
 */
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(include root_path() . 'bootstrap/definitions.php');
$containerBuilder->useAutowiring(true);

if (env('APP_ENV') == Env::PRODUCTION()) {
    $containerBuilder->enableCompilation(storage_path() . 'cache');
}

$container = $containerBuilder->build();
$container->set('error_reporter', $errorReporter);

return $container;
