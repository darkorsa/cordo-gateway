<?php

namespace Cordo\Gateway\Core\Infractructure\Persistance\Doctrine\Cache;

use Redis;
use Exception;
use Doctrine\Common\Cache\RedisCache;
use Cache\Adapter\Doctrine\DoctrineCachePool;

class CachePoolFactory
{
    public static function create(string $driver): DoctrineCachePool
    {
        switch ($driver) {
            case 'redis':
                return self::redisDriver();
            default:
                throw new Exception('Unknown cache driver ' . $driver);
        }
    }

    private static function redisDriver(): DoctrineCachePool
    {
        $client = new Redis();
        $client->pconnect(env('REDIS_SERVER'), (int) env('REDIS_PORT'));

        if (env('REDIS_KEY')) {
            $client->auth(env('REDIS_KEY'));
        }

        $cacheDriver = new RedisCache();
        $cacheDriver->setRedis($client);

        return new DoctrineCachePool($cacheDriver);
    }
}
