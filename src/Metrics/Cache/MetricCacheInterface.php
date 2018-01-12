<?php
declare(strict_types = 1);

namespace App\Metrics\Cache;

use App\Metrics\BlogsMetrics\BlogsMetricInterface;

interface MetricCacheInterface
{
    /**
     * @param BlogsMetricInterface[] $metrics
     */
    public function cacheMetrics(array $metrics): void;

    /**
     * @param callable $getAllMetrics - This function must return a list of all possible ProgrammesMetricInterface
     * @return BlogsMetricInterface[]
     */
    public function getAndClearReadyToSendMetrics(callable $getAllMetrics): array;
}
