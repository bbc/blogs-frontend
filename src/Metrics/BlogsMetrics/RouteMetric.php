<?php
declare(strict_types = 1);

namespace App\Metrics\BlogsMetrics;

/**
    AWS metrics in namespaces: route_count and route_time
    +-----------+-----------------------+
    |     #     | dimension: controller |
    +-----------+-----------------------+
    | #metric 1 | findByPid             |
    | #metric 2 | collapsedBroadcast    |
    | #...      | ....                  |
    +-----------+-----------------------+

 */
class RouteMetric implements BlogsMetricInterface
{
    /** @var string */
    private $controllerName;

    /** @var int */
    private $responseTimeMs;

    /** @var int */
    private $count;

    public function __construct(string $controllerName, int $responseTimeMs, int $count = 1)
    {
        $this->controllerName = $controllerName;
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
            [ 'Name' => 'controller', 'Value' => $this->controllerName ],
        ];
        $metricData = [];
        $metricData[] = [
            'MetricName' => 'route_count',
            'Dimensions' => $dimensions,
            'Value' => $this->count,
            'Unit' => 'Count',
        ];
        if ($this->count > 0) {
            // Do not return average response time if there are no responses
            $metricData[] = [
                'MetricName' => 'route_time',
                'Dimensions' => $dimensions,
                'Value' => $this->getAverageResponseTime(),
                'Unit' => 'Milliseconds',
            ];
        }
        return $metricData;
    }

    private function cacheKey(string $type)
    {
        return implode('#', ['route', $this->controllerName, $type]);
    }

    private function getAverageResponseTime()
    {
        if ($this->count > 1) {
            return $this->responseTimeMs / $this->count;
        }
        return $this->responseTimeMs;
    }
}
