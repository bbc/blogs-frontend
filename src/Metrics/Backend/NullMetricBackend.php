<?php
declare(strict_types = 1);

namespace App\Metrics\Backend;

use App\Metrics\BlogsMetrics\BlogsMetricInterface;

class NullMetricBackend implements MetricBackendInterface
{
    /**
     * @param BlogsMetricInterface[] $metrics
     */
    public function sendMetrics(array $metrics): void
    {
        return;
    }
}
