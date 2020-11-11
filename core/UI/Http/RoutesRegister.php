<?php

declare(strict_types=1);

namespace Cordo\Gateway\Core\UI\Http;

use Psr\Container\ContainerInterface;
use Cordo\Gateway\Core\UI\Http\Router;
use Cordo\Gateway\Core\Support\ApiRequester;

abstract class RoutesRegister
{
    protected const UUID_PATTERN = '{id:[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}}';

    protected $router;

    protected $namespace;

    protected $apiRequester;

    public function __construct(Router $router, ContainerInterface $container, string $apiUrl, string $namespace = '')
    {
        $this->router = $router;
        $this->namespace = $namespace;
        $this->apiRequester = new ApiRequester($apiUrl, $container->get('config'));
    }

    abstract public function register(): void;
}
