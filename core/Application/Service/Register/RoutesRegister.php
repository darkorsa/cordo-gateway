<?php

declare(strict_types=1);

namespace Cordo\Gateway\Core\Application\Service\Register;

use Psr\Container\ContainerInterface;
use Cordo\Gateway\Core\UI\Http\Router;
use Cordo\Gateway\Core\UI\Http\Controller;

abstract class RoutesRegister
{
    protected const UUID_PATTERN = '{id:[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}}';

    protected $router;

    protected $namespace;

    protected $controller;

    public function __construct(Router $router, ContainerInterface $container, string $apiUrl, string $namespace = '')
    {
        $this->router = $router;
        $this->namespace = $namespace;
        $this->controller = new Controller($apiUrl, $container->get('config'));
    }

    abstract public function register(): void;
}
