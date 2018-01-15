<?php
declare(strict_types = 1);

namespace App\Metrics\Backend;

use App\Metrics\BlogsMetrics\BlogsMetricInterface;
use Aws\CloudWatch\Exception\CloudWatchException;
use Psr\Log\LoggerInterface;
use RMP\CloudwatchMonitoring\MonitoringHandler;

class CloudWatchMetricBackend implements MetricBackendInterface
{
    /** @var MonitoringHandler */
    private $monitoringHandler;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(MonitoringHandler $monitoringHandler, LoggerInterface $logger)
    {
        $this->monitoringHandler = $monitoringHandler;
        $this->logger = $logger;
    }

    /**
     * @param BlogsMetricInterface[] $metrics
     */
    public function sendMetrics(array $metrics): void
    {
        /** @var BlogsMetricInterface $metric */
        foreach ($metrics as $metric) {
            foreach ($metric->getMetricData() as $metricDatum) {
                $this->monitoringHandler->putMetricData(
                    $metricDatum['MetricName'],
                    $metricDatum['Value'],
                    $metricDatum['Dimensions'],
                    $metricDatum['Unit']
                );
            }
        }
        try {
            $this->monitoringHandler->sendMetrics();
        } catch (CloudWatchException $e) {
            $this->logger->error("Error sending cloudwatch metrics: " . $e->getMessage());
        }
    }
}
