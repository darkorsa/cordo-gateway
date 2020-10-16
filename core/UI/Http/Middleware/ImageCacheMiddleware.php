<?php

namespace Cordo\Gateway\Core\UI\Http\Middleware;

use Exception;
use Middlewares\ImageManipulation;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Converts image file naming format from seo friendly, to format supported by Middlewares\ImageManipulation
 * Accepted format example: images/man-wearing-hat-cmVzaXplLDEwMHxzZWNyZXQsMmFjYzA1MmY.jpg
 */
class ImageCacheMiddleware implements MiddlewareInterface
{
    private $signatureKey;

    public static function getUri(string $path, string $transform, string $signatureKey): string
    {
        $hash = self::hash("{$transform}+{$signatureKey}");
        $encoded = trim(base64_encode("{$transform}||" . $hash), '=');

        $pathInfo = pathinfo($path);

        return $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '-' . $encoded . '.' . $pathInfo['extension'];
    }

    public function __construct(string $signatureKey)
    {
        $this->signatureKey = $signatureKey;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uri = $request->getUri();
        $path = $uri->getPath();

        [$encoded, $image] = $this->extractEncodedAndImage($path);
        [$transform, $secret] = $this->decode($encoded);

        $this->validateSecret($transform, $secret);

        $pathInfo = pathinfo($image);

        $newPath = $pathInfo['dirname'] . ImageManipulation::getUri(
            $pathInfo['basename'],
            $transform,
            $this->signatureKey
        );

        return $handler->handle($request->withUri($uri->withPath($newPath)));
    }

    private function extractEncodedAndImage(string $path): array
    {
        if (!$hash = strstr(substr(strrchr($path, '-'), 1), '.', true)) {
            throw new Exception('Invalid image path');
        }

        $image = str_replace('-' . $hash, '', $path);

        return [
            $hash,
            $image,
        ];
    }

    private function decode(string $encoded): array
    {
        return explode('||', base64_decode($encoded));
    }

    private static function hash(string $string): string
    {
        return substr(sha1($string), 0, 8);
    }

    private function validateSecret(string $transform, string $secret): void
    {
        if (self::hash("{$transform}+{$this->signatureKey}") !== $secret) {
            throw new Exception('Invalid image cache secret');
        }
    }
}
