<?php

declare(strict_types=1);

namespace Cordo\Gateway\Core\UI\Http\Log;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PhpMiddleware\LogHttpMessages\Formatter\FormattedMessage;
use PhpMiddleware\LogHttpMessages\Formatter\ResponseFormatter;
use PhpMiddleware\LogHttpMessages\Formatter\ServerRequestFormatter;

/**
 * @codeCoverageIgnore
 */
final class MessageFormatter implements ServerRequestFormatter, ResponseFormatter
{
    public function formatServerRequest(ServerRequestInterface $request): FormattedMessage
    {
        $query = ($request->getUri()->getQuery())
            ? '?' . $request->getUri()->getQuery()
            : '';

        return FormattedMessage::fromString($request->getUri()->getPath() . $query);
    }

    public function formatResponse(ResponseInterface $response): FormattedMessage
    {
        return FormattedMessage::fromString((string) $response->getStatusCode());
    }
}
