<?php
declare(strict_types = 1);

namespace App\Metrics\BlogsMetrics;

class ApiBreakerMetric implements BlogsMetricInterface
{
    /** @var string */
    private $apiName;

    /** @var bool */
    private $isOpen;

    public function __construct(string $apiName, bool $isOpen = false)
    {
        $this->apiName = $apiName;
        $this->isOpen = $isOpen;
    }

    /**
     * This function returns the cache keys to set and the
     * values to set them to.
     */
    public function getCacheKeyValuePairs(): array
    {
        return [
            $this->cacheKey() => $this->isOpen ? 1 : 0,
        ];
    }

    /**
     * Accepts the format returned by getCacheKeyValuePairs, but sets
     * the values in the class from values returned from the cache.
     */
    public function setValuesFromCacheKeyValuePairs(array $keyValuePairs): void
    {
        $this->isOpen = ($keyValuePairs[$this->cacheKey()] > 0);
    }

    public function getMetricData(): array
    {
        return [
            [
                'MetricName' => 'api_circuit_breaker_open',
                'Dimensions' => [
                    [ 'Name' => 'api', 'Value' => $this->apiName ],
                ],
                'Value' => $this->isOpen ? 1 : 0,
                'Unit' => 'Count',
            ],
        ];
    }

    private function cacheKey()
    {
        return implode('#', ['api_breaker_state', $this->apiName]);
    }
}
