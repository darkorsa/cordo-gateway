<?php

function env(string $index, ?string $default = null): ?string
{
    if ($default !== null) {
        return $default;
    }

    if (array_key_exists($index, $_ENV)) {
        return $_ENV[$index];
    }

    return null;
}

function root_path(): string
{
    return __DIR__ . '/../';
}

function app_path(): string
{
    return __DIR__ . '/../app/';
}

function config_path(): string
{
    return __DIR__ . '/../config/';
}

function storage_path(): string
{
    return __DIR__ . '/../storage/';
}

function vendor_path(): string
{
    return __DIR__ . '/../vendor/';
}
