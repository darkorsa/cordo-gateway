<?php

declare(strict_types=1);

namespace Cordo\Gateway\Core\UI\Http\Response;

use Psr\Http\Message\ResponseInterface;

class ImageResponse
{
    private $response;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public function __invoke(int $size, string $contentType, int $ttl = null): void
    {
        http_response_code($this->response->getStatusCode());

        header('Content-Type:' . "{$contentType}");
        header('Content-Length: ' . $size);

        if ($ttl && $this->response->getStatusCode() == 200) {
            header("Cache-Control: public, max-age={$ttl}");
        }

        echo (string) $this->response->getBody();
        exit;
    }
}
