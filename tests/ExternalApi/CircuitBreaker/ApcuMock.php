<?php
declare(strict_types = 1);

namespace Tests\App\ExternalApi\CircuitBreaker;

use App\ExternalApi\CircuitBreaker\Apcu;
use Cake\Chronos\Chronos;

/**
 * Class ApcuMock
 *
 * Mocks the behavior of certain APCU functions using an array.
 *
 * This is kind of a lot of work to test an APCU class. I'm starting to regret it.
 * Maybe I can finally test the code in ApcuCache though
 */
class ApcuMock extends Apcu
{
    /** @var array */
    private $cache = [];

    /** @var Chronos */
    private $clock;

    public function __construct(Chronos $clock)
    {
        $this->clock = $clock;
    }

    public function clockForward(int $seconds)
    {
        $this->clock = $this->clock->addSeconds($seconds);
    }

    public function fetch($key, bool &$success = null)
    {
        return $this->get($key, $success);
    }

    public function store($key, $var, int $ttl = 0)
    {
        $this->set($key, $var, $this->ttlToExpiry($ttl));
    }

    public function inc(string $key, int $step = 1, bool &$success = null)
    {
        $getSuccess = false;
        $value = $this->get($key, $getSuccess);
        if ($getSuccess && !is_int($value)) {
            $success = false;
            return false;
        }
        if (!is_int($value)) {
            $value = 0;
        }
        $value += $step;
        $expires = false;
        if (isset($this->cache[$key]['expires'])) {
            $expires = $this->cache[$key]['expires'];
        }
        $this->set($key, $value, $expires);
        $success = true;
    }

    public function exists($keys)
    {
        $this->get($keys, $success);
        return $success;
    }

    public function delete($key)
    {
        unset($this->cache[$key]);
    }

    public function entry(string $key, callable $generator, int $ttl = 0)
    {
        $value = $this->get($key, $success);
        if ($success) {
            return $value;
        }
        $value = $generator();
        $this->set($key, $value, $this->ttlToExpiry($ttl));
        return $value;
    }

    private function get(string $key, bool &$success = null)
    {
        if (isset($this->cache[$key])) {
            if (!$this->cache[$key]['expires'] || $this->cache[$key]['expires'] >= $this->clock) {
                $success = true;
                return $this->cache[$key]['value'];
            }
        }
        $success = false;
        return false;
    }

    private function set($key, $var, $expires)
    {
        if ($expires !== false && !$expires instanceof Chronos) {
            throw new \InvalidArgumentException("$expires is not a valid value");
        }
        $this->cache[$key] = ['value' => $var, 'expires' => $expires];
    }

    private function ttlToExpiry(int $ttl)
    {
        if ($ttl) {
            return $this->clock->addSeconds($ttl);
        }
        return false;
    }
}
