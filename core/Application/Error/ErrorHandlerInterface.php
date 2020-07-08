<?php

declare(strict_types=1);

namespace Cordo\Gateway\Core\Application\Error;

use Throwable;

interface ErrorHandlerInterface
{
    public function handle(Throwable $exception): void;
}
