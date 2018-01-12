<?php
declare(strict_types = 1);

namespace App\Metrics\Backend;

use App\Metrics\BlogsMetrics\BlogsMetricInterface;
use Psr\Log\LoggerInterface;

class LogMetricBackend implements MetricBackendInterface
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param BlogsMetricInterface[] $metrics
     */
    public function sendMetrics(array $metrics): void
    {
        foreach ($metrics as $metric) {
            foreach ($metric->getMetricData() as $metricDatum) {
                $logString = implode(',', [$metricDatum['MetricName'], $metricDatum['Value'], $metricDatum['Unit']]);
                foreach ($metricDatum['Dimensions'] as $dimension) {
                    $logString .= ",$dimension[Name]=$dimension[Value]";
                }
                $this->logger->notice($logString);
            }
        }
    }
}
