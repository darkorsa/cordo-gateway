<?php

declare(strict_types=1);

namespace Cordo\Gateway\Core\Application\Error\Handler;

use Throwable;
use Whoops\Run;
use Cordo\Gateway\Core\Application\Error\ErrorHandlerInterface;

class PrettyErrorHandler implements ErrorHandlerInterface
{
    protected $whoops;

    public function __construct(Run $whoops)
    {
        $this->whoops = $whoops;
    }

    public function handle(Throwable $exception): void
    {
        $this->whoops->handleException($exception);
    }
}
