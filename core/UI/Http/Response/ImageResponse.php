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

    public function __invoke(int $size, string $extension): void
    {
        http_response_code($this->response->getStatusCode());

        header('Content-Type:' . "image/{$extension}");
        header('Content-Length: ' . $size);
        
        echo (string) $this->response->getBody();
        exit;
    }
}
