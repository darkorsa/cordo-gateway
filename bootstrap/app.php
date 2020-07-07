<?php

use App\Register;
use DI\ContainerBuilder;
use Cordo\Core\SharedKernel\Enum\Env;
use Symfony\Component\Dotenv\Dotenv;
use Cordo\Core\Infractructure\Mailer\ZendMail\MailerFactory;

$dotenv = new Dotenv();
$dotenv->load(root_path() . '.env');

// Errors
$errorReporter = require __DIR__ . '/error.php';

/**
 * @var $container Psr\Container\ContainerInterface
 */
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(Register::registerDefinitions());
$containerBuilder->useAutowiring(true);

if (env('APP_ENV') == Env::PRODUCTION()) {
    $containerBuilder->enableCompilation(storage_path() . 'cache');
}

$container = $containerBuilder->build();
$container->set('error_reporter', $errorReporter);

// Configs
Register::registerConfigs($container->get('config'));

// Mailer
$mailer = MailerFactory::factory($container->get('config')->get('mail'));
$container->set('mailer', $mailer);

return $container;
