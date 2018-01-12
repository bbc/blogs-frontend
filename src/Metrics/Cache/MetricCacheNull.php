<?php
declare(strict_types = 1);

namespace App\Metrics\Cache;

class MetricCacheNull implements MetricCacheInterface
{
    public function cacheMetrics(array $metrics): void
    {
    }

    public function getAndClearReadyToSendMetrics(callable $getAllPossibleMetrics): array
    {
        return [];
    }
}
