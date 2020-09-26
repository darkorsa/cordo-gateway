<?php

declare(strict_types=1);

namespace Cordo\Gateway\Core\UI\Http\Response;

use Psr\Http\Message\ResponseInterface;

class JsonResponse implements \Cordo\Gateway\Core\UI\ResponseInterface
{
    private $response;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public function send(): void
    {
        http_response_code($this->response->getStatusCode());

        // additional headers
        foreach ($this->filterHeaders($this->response->getHeaders()) as $key => $val) {
            header("$key: " . current($val) . "");
        }

        $body = (string) $this->response->getBody();

        echo $this->isJson($body) ? $body : json_encode($body);
        exit;
    }

    private function filterHeaders(array $headers): array
    {
        $blockList = [
            'server',
            'set-cookie',
            'date',
            'content-encoding',
            'cache-control',
            'x-powered-by',
            'connection',
            'transfer-encoding',
        ];

        return array_filter($headers, static function ($key) use ($blockList) {
            return !in_array(strtolower($key), $blockList);
        }, ARRAY_FILTER_USE_KEY);
    }

    private function isJson(string $string): bool
    {
        json_decode($string);

        return (json_last_error() == JSON_ERROR_NONE);
    }
}
