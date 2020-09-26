<?php

namespace Cordo\Gateway\Core\UI\Http\Cache;

use Exception;
use Cordo\Gateway\Core\Infractructure\Persistance\Doctrine\Cache\CachePoolFactory;

class CacheClientFactory
{
    public static function create(string $driver): CacheClient
    {
        switch ($driver) {
            case 'redis':
                return new CacheClient(CachePoolFactory::create('redis'));
            default:
                throw new Exception('Unknown cache driver ' . $driver);
        }
    }
}
