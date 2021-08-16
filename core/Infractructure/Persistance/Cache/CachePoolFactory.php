<?php

namespace Cordo\Gateway\Core\Infractructure\Persistance\Cache;

use Redis;
use Exception;
use Psr\Cache\CacheItemPoolInterface;
use Cache\Adapter\Redis\RedisCachePool;

class CachePoolFactory
{
    public static function create(string $driver): CacheItemPoolInterface
    {
        switch ($driver) {
            case 'redis':
                return self::redisDriver();
            default:
                throw new Exception('Unknown cache driver ' . $driver);
        }
    }

    private static function redisDriver(): CacheItemPoolInterface
    {
        $client = new \Redis();
        $client->connect(env('REDIS_SERVER'), (int) env('REDIS_PORT'));

        if (env('REDIS_KEY')) {
            $client->auth(env('REDIS_KEY'));
        }

        return new RedisCachePool($client);
    }
}
