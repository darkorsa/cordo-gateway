<?php

namespace Cordo\Gateway\Core\UI\Http\Cache;

use Redis;
use Exception;
use Doctrine\Common\Cache\RedisCache;
use Cache\Adapter\Doctrine\DoctrineCachePool;

class CacheClientFactory
{
    public static function create(string $driver): CacheClient
    {
        switch ($driver) {
            case 'redis':
                return new CacheClient(self::redisDriver());
            default:
                throw new Exception('Unknown cache driver ' . $driver);
        }
    }

    private static function redisDriver(): DoctrineCachePool
    {
        $client = new Redis();
        $client->connect('127.0.0.1', (int) env('REDIS_PORT'));

        $cacheDriver = new RedisCache();
        $cacheDriver->setRedis($client);

        return new DoctrineCachePool($cacheDriver);
    }
}
