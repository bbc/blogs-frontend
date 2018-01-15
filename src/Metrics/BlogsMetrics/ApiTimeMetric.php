<?php
declare(strict_types = 1);

namespace App\Metrics\BlogsMetrics;

/**
    AWS metrics in namespaces: api_time and api_count
    +-----------+----------------+--------------------------+
    |     #     | dimension: api | dimension: response_type |
    +-----------+----------------+--------------------------+
    | #metric 1 | ORB            | ALL                      |
    | #metric 2 | Branding       | ALL                      |
    +-----------+----------------+--------------------------+
 */
class ApiTimeMetric implements BlogsMetricInterface
{
    /** @var string */
    private $apiName;

    /** @var int */
    private $responseTimeMs;

    /** @var int */
    private $count;

    public function __construct(string $apiName, int $responseTimeMs, int $count = 1)
    {
        $this->apiName = $apiName;
        $this->responseTimeMs = $responseTimeMs;
        $this->count = $count;
    }

    /**
     * This function returns the cache keys to set and the
     * values to set them to.
     */
    public function getCacheKeyValuePairs(): array
    {
        return [
            $this->cacheKey('count') => $this->count,
            $this->cacheKey('time') => $this->responseTimeMs,
        ];
    }

    /**
     * Accepts the format returned by getCacheKeyValuePairs, but sets
     * the values in the class from values returned from the cache.
     */
    public function setValuesFromCacheKeyValuePairs(array $keyValuePairs): void
    {
        $this->count = $keyValuePairs[$this->cacheKey('count')];
        $this->responseTimeMs = $keyValuePairs[$this->cacheKey('time')];
    }

    public function getMetricData(): array
    {
        $dimensions = [
            [ 'Name' => 'api', 'Value' => $this->apiName ],
            [ 'Name' => 'response_type', 'Value' => 'All'],
        ];
        $metricData = [];
        $metricData[] = [
            'MetricName' => 'api_count',
            'Dimensions' => $dimensions,
            'Value' => $this->count,
            'Unit' => 'Count',
        ];
        if ($this->count > 0) {
            // Do not return average response time if there are no responses
            $metricData[] = [
                'MetricName' => 'api_time',
                'Dimensions' => $dimensions,
                'Value' => $this->getAverageResponseTime(),
                'Unit' => 'Milliseconds',
            ];
        }
        return $metricData;
    }

    private function cacheKey(string $type)
    {
        return implode('#', ['api_time', $this->apiName, $type]);
    }

    private function getAverageResponseTime()
    {
        if ($this->count > 1) {
            return $this->responseTimeMs / $this->count;
        }
        return $this->responseTimeMs;
    }
}
