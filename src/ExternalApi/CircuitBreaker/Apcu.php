<?php
declare(strict_types = 1);

namespace App\ExternalApi\CircuitBreaker;

/**
 * Class Apcu
 *
 * This wrapper class is a bit silly, but allows us to test Apcu code without having to go
 * for something like PSR6 or Doctrine caching, which don't support things like acpu_inc or apcu_entry
 */
class Apcu
{
    public function fetch($key, bool &$success = null)
    {
        return apcu_fetch($key, $success);
    }

    public function store($key, $var, int $ttl = 0)
    {
        return apcu_store($key, $var, $ttl);
    }

    public function inc(string $key, int $step = 1, bool &$success = null)
    {
        return apcu_inc($key, $step, $success);
    }

    public function exists($keys)
    {
        return apcu_exists($keys);
    }

    public function delete($key)
    {
        return apcu_delete($key);
    }

    public function entry(string $key, callable $generator, int $ttl = 0)
    {
        return apcu_entry($key, $generator, $ttl);
    }
}
