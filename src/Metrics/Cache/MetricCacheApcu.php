<?php
declare(strict_types = 1);

namespace App\Metrics\Cache;

use App\Metrics\BlogsMetrics\BlogsMetricInterface;

class MetricCacheApcu implements MetricCacheInterface
{
    /** apcu cache key prefix */
    public const PREFIX = 'PRF_METRICS';

    /** How often to send metrics */
    private const SEND_INTERVAL = 20;

    /** List of all buckets */
    private const ROTATING_BUCKET_PREFIXES = ['A', 'B', 'C'];

    /**
     * Increment supplied metrics in apcu
     */
    public function cacheMetrics(array $metrics): void
    {
        $bucket = $this->getCurrentBucketLetter();
        $apcKeyValues = [];
        /** @var BlogsMetricInterface $metric */
        foreach ($metrics as $metric) {
            $metricKeyValues = $metric->getCacheKeyValuePairs();
            $metricType = get_class($metric);
            foreach ($metricKeyValues as $metricKey => $metricValue) {
                $apcKey = $this->makeApcKey($metricType, $bucket, $metricKey);
                if (!isset($apcKeyValues[$apcKey])) {
                    $apcKeyValues[$apcKey] = 0;
                }
                $apcKeyValues[$apcKey] += $metricValue;
            }
        }

        foreach ($apcKeyValues as $key => $value) {
            apcu_inc($key, $value);
        }
    }

    /**
     * @param callable $getAllPossibleMetrics
     * @return BlogsMetricInterface[]
     */
    public function getAndClearReadyToSendMetrics(callable $getAllPossibleMetrics): array
    {
        $this->rotateBucketIfNeeded();
        foreach (self::ROTATING_BUCKET_PREFIXES as $bucket) {
            $bucketNoSendKey = $this->makeApcKey('internal', $bucket, 'bucket_no_send');
            if ($this->tryToLockOnKey($bucketNoSendKey, 0)) {
                return $this->fetchAndResetBucket($bucket, $getAllPossibleMetrics());
            }
        }
        return [];
    }

    /**
     * Fetch all keys for supplied metrics from supplied bucket, reset their values
     * to zero and return all the metric objects, with populated values where appropriate
     *
     * Must only be run once a lock has been obtained on that bucket's
     * "bucket_no_send" key
     *
     * @param string $bucket
     * @param BlogsMetricInterface[] $allMetrics
     * @return BlogsMetricInterface[]
     */
    private function fetchAndResetBucket(string $bucket, array $allMetrics) : array
    {
        $metrics = [];
        foreach ($allMetrics as $metric) {
            /** @var BlogsMetricInterface $metric */
            $keyValuePairs = $metric->getCacheKeyValuePairs();
            $found = false;
            $metricType = get_class($metric);
            foreach ($keyValuePairs as $key => $value) {
                $apcKey = $this->makeApcKey($metricType, $bucket, $key);
                $keyValuePairs[$key] = apcu_fetch($apcKey);
                if ($keyValuePairs[$key]) {
                    $found = true;
                    apcu_store($apcKey, 0);
                }
            }
            if ($found) {
                $metric->setValuesFromCacheKeyValuePairs($keyValuePairs);
            }
            $metrics[] = $metric;
        }
        return $metrics;
    }

    /**
     * Get the current bucket to write to.
     */
    private function getCurrentBucketLetter(): string
    {
        $currentBucketKey = $this->makeApcKey('internal', '', 'bucket_index');
        $bucket = apcu_fetch($currentBucketKey);
        if (!$bucket) {
            $bucket = 0;
        }
        return $this->getBucketLetterFromIndex($bucket);
    }

    private function getBucketLetterFromIndex(int $iterator)
    {
        $bucketsCount = count(self::ROTATING_BUCKET_PREFIXES);
        $currentKey = $iterator % $bucketsCount;
        return self::ROTATING_BUCKET_PREFIXES[$currentKey];
    }

    /**
     * Generate keys such as:
     * PRF_METRICS||internal|bucket_timeout 1
     * PRF_METRICS|B|App\Metrics\ProgrammesMetrics\RouteMetric|route#schedules_by_day#time
     */
    private function makeApcKey(string $type, string $bucket, string $key) : string
    {
        $pieces = [self::PREFIX, $bucket, $type, $key];
        return implode('|', $pieces);
    }

    /**
     * Oh boy...
     * Because the only way apcu provides of obtaining a real lock is via apcu_entry,
     * which only works on unset cache keys, we need a way to ensure that while
     * process A is sending metrics, process B is not updating the same metrics, thus
     * invalidating our monitoring.
     * The solution here is "buckets". For self::SEND_INTERVAL seconds every metric
     * is put into bucket "A". Once the "bucket_timeout" expires, this function
     * tells every other process to write to bucket "B" by updating "bucket_index".
     * At the same time it sets a "bucket_no_send" key (which prevents fetchAndResetBucket
     * from getting and clearing the given bucket) on bucket "A" with a timeout
     * of 1 second. This allows any other processes currently writing to "A" to finish.
     *
     * Once bucket_no_send expires, the contents of bucket "A" will be retrieved,
     * cleared, and eventually sent to the backend.
     */
    private function rotateBucketIfNeeded(): void
    {
        $currentBucketKey = $this->makeApcKey('internal', '', 'bucket_index');
        $bucketTimeoutKey = $this->makeApcKey('internal', '', 'bucket_timeout');

        if (!$this->tryToLockOnKey($bucketTimeoutKey, self::SEND_INTERVAL)) {
            return;
        }

        // If we are here we own the lock on $bucketTimeoutKey for self::SEND_INTERVAL
        // seconds. No other process will execute this code at the same time.
        $currentBucketIndex = apcu_fetch($currentBucketKey);
        if ($currentBucketIndex === false) {
            // First run. Set up and use a new bucket. Make sure our buckets are initialised
            // not to be sent until they're filled and rotated
            foreach (self::ROTATING_BUCKET_PREFIXES as $bucket) {
                apcu_store(
                    $this->makeApcKey('internal', $bucket, 'bucket_no_send'),
                    1,
                    0
                );
            }
            $newBucketIndex = 0;
        } else {
            // Normal operation. Set up the next bucket and set "bucket_no_send"
            // with a timeout of 1s on the current bucket. Preventing its contents
            // from being sent for 1s.
            $currentBucketLetter = $this->getBucketLetterFromIndex($currentBucketIndex);
            apcu_store(
                $this->makeApcKey('internal', $currentBucketLetter, 'bucket_no_send'),
                1,
                1
            );
            $bucketCount = count(self::ROTATING_BUCKET_PREFIXES);
            $newBucketIndex = ($currentBucketIndex + 1) % $bucketCount;
            // Set up a new bucket
            $newBucketLetter = $this->getBucketLetterFromIndex($newBucketIndex);

            apcu_store(
                $this->makeApcKey('internal', $newBucketLetter, 'bucket_no_send'),
                1,
                0
            );
        }
        // Make fully set-up new bucket into current bucket
        apcu_store($currentBucketKey, $newBucketIndex);
    }

    /**
     * Okay. This one is kind of complicated. We want our class to do a specific thing
     * at most once every $timeout seconds.
     *
     * This function sets an Apcu key with an expiry after $timeout seconds.
     * Once that key expires, apcu_fetch will return false and we use the locking
     * function apcu_entry to ensure that only once process (no matter how many
     * concurrent processes are running) can acquire $lock. The callback
     * in apcu_entry is ONLY executed when $key is UNSET (i.e. it has expired).
     * It simultaneously sets the value for $key to 1, so until the timeout
     * expires, only one process can get this lock.
     *
     * @param string $key
     * @param int $timeout
     * @return bool
     */
    private function tryToLockOnKey(string $key, int $timeout) : bool // 0 timeout = unlimited
    {
        $lock = false;
        if (!apcu_fetch($key)) {
            /**
             * apcu_entry is broken (horribly) in APCu >= 5.1.8 . That actually supports locking.
             * This doesn't really. There's a slight risk of more than one request getting hold of this lock.
             * however, i've tested this with high concurrency levels and not seen it in practice.
             * Once apcu_entry is fixed ( https://github.com/krakjoe/apcu/issues/246 ) consider reverting this.
             */
            $lock = apcu_add($key, 1, $timeout);
        }
        return $lock;
    }
}
