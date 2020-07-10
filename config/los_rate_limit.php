<?php

use LosMiddleware\RateLimit\RateLimitMiddleware;

return [
    'max_requests' => 1000,
    'reset_time' => 3600,
    'ip_max_requests' => 1000,
    'ip_reset_time' => 3600,
    'api_header' => 'X-Api-Key',
    'trust_forwarded' => false,
    'prefer_forwarded' => false,
    'forwarded_headers_allowed' => [
        'Client-Ip',
        'Forwarded',
        'Forwarded-For',
        'X-Cluster-Client-Ip',
        'X-Forwarded',
        'X-Forwarded-For',
    ],
    'forwarded_ip_index' => null,
    'headers' => [
        'limit' => RateLimitMiddleware::HEADER_LIMIT,
        'remaining' => RateLimitMiddleware::HEADER_REMAINING,
        'reset' => RateLimitMiddleware::HEADER_RESET,
    ],
    'keys' => [],
    'ips' => [],
];
