<?php

use Noodlehaus\Config;
use Cordo\Gateway\Core\UI\Http\Router;
use Doctrine\DBAL\Connection;
use Psr\Container\ContainerInterface;
use Cordo\Gateway\Core\Application\Config\Parser;
use Psr\Http\Message\ServerRequestInterface;
use Cordo\Gateway\Core\Application\Error\ErrorReporterInterface;
use Cordo\Gateway\Core\Infractructure\Mailer\ZendMail\MailerInterface;

return [
    'config' => DI\factory(static function () {
        return new Config(config_path(), new Parser());
    }),
    'request' => DI\get(ServerRequestInterface::class),
    'router' => DI\get(Router::class),
    ServerRequestInterface::class => DI\factory('GuzzleHttp\Psr7\ServerRequest::fromGlobals'),
    ErrorReporterInterface::class => DI\get('error_reporter'),
    MailerInterface::class => DI\get('mailer'),
    Connection::class => DI\factory(static function (ContainerInterface $container) {
        return $container->get('entity_manager')->getConnection();
    }),
    Config::class => DI\get('config'),
];
