<?php
//Can't use strict types due to bug https://github.com/phpredis/phpredis/issues/1193
//Fix on master, but no release with it yet: https://github.com/phpredis/phpredis/pull/1244

namespace App\Redis;

use Psr\Log\LoggerInterface;
use RedisCluster;
use RedisClusterException;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class RedisClusterFactory
{
    /**
     * Instance a Redis Cluster, if the connection fails, it will return a NullAdapter instead and log the error,
     * this way if the Redis cluster is down it won't be used an the application won't fail as far as
     * the database can deal with the traffic.
     *
     * @param string[] $redisEndpoints
     * @param LoggerInterface $logger
     * @return AdapterInterface
     */
    public static function createRedisCluster(array $redisEndpoints, LoggerInterface $logger): AdapterInterface
    {
        try {
            // Create a cluster and specify timeout (float), 200ms should be enough,
            // more time mean something is wrong and we should stop waiting for redis
            $redisClusterInstance = new RedisCluster(null, $redisEndpoints, 0.2);
            // always distribute readonly commands between masters and slaves, at random
            $redisClusterInstance->setOption(
                RedisCluster::OPT_SLAVE_FAILOVER,
                RedisCluster::FAILOVER_DISTRIBUTE
            );
            $cacheAdapter = new RedisAdapter($redisClusterInstance);
        } catch (RedisClusterException $e) {
            // if redis cluster fail, log the error and use a null adapter as a cache provider
            $logger->error('Redis Cluster Error: ' . $e->getMessage() . '. Using NullAdapter as a fallback cache adapter');
            $cacheAdapter = new NullAdapter();
        }
        return $cacheAdapter;
    }
}
