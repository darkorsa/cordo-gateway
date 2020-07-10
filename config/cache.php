<?php

return [
    'cache_lifetime' => 86400 * 365, // one year
    // redis
    'redis_server' => env('REDIS_SERVER'),
    'redis_port' => env('REDIS_PORT'),
    // memcached
    'memcached_server' => env('MEMCACHED_SERVER'),
    'memcached_port' => env('MEMCACHED_PORT'),
];
