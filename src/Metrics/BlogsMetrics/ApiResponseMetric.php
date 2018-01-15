<?php
declare(strict_types = 1);

namespace App\Metrics\BlogsMetrics;

/**
    AWS metrics in namespace: api_count
    +-----------+----------------+--------------------------+
    |     #     | dimension: api | dimension: response_type |
    +-----------+----------------+--------------------------+
    | #metric 1 | ORB            |                    ERROR |
    | #metric 2 | Branding       |                    ERROR |
    +-----------+----------------+--------------------------+
 */
class ApiResponseMetric implements BlogsMetricInterface
{
    /** @var string */
    private $apiName;

    /** @var int */
    private $count;

    /** @var string */
    private $responseType;

    public function __construct(string $apiName, string $responseType, int $count = 1)
    {
        $this->apiName = $apiName;
        $this->count = $count;
        $this->responseType = $responseType;
    }

    /**
     * This function returns the cache keys to set and the
     * values to set them to.
     */
    public function getCacheKeyValuePairs(): array
    {
        return [
            $this->cacheKey() => $this->count,
        ];
    }

    /**
     * Accepts the format returned by getCacheKeyValuePairs, but sets
     * the values in the class from values returned from the cache.
     */
    public function setValuesFromCacheKeyValuePairs(array $keyValuePairs): void
    {
        $this->count = $keyValuePairs[$this->cacheKey()];
    }

    public function getMetricData(): array
    {
        return [
            [
                'MetricName' => 'api_count',
                'Dimensions' => [
                    [ 'Name' => 'api', 'Value' => $this->apiName ],
                    [ 'Name' => 'response_type', 'Value' => $this->responseType],
                ],
                'Value' => $this->count,
                'Unit' => 'Count',
            ],
        ];
    }

    private function cacheKey()
    {
        return implode('#', ['api_response', $this->apiName, $this->responseType]);
    }
}
