<?php

declare(strict_types=1);

namespace Cordo\Gateway\Core\Application\Service\Register;

use Cordo\Gateway\Core\UI\Http\Router;
use Psr\Container\ContainerInterface;

abstract class RoutesRegister
{
    protected const UUID_PATTERN = '{id:[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}}';

    protected $router;

    protected $container;

    public function __construct(Router $router, ContainerInterface $container)
    {
        $this->router = $router;
        $this->container = $container;
    }

    abstract public function register(): void;
}
