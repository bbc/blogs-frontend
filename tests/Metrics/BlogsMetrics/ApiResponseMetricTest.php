<?php
declare(strict_types = 1);

namespace Tests\App\Metrics\BlogsMetrics;

use App\Metrics\BlogsMetrics\ApiResponseMetric;
use PHPUnit\Framework\TestCase;

/**
 * @group metrics
 */
class ApiResponseMetricTest extends TestCase
{
    public function testMetricProvideCorrectDataStructureForAWS()
    {
        $metric = new ApiResponseMetric('BRANDING', 'ERROR', 67);
        $this->assertEquals([
                [
                    'MetricName' => 'api_count',
                    'Dimensions' => [
                        [
                            'Name' => 'api',
                            'Value' => 'BRANDING',
                        ],
                        [
                            'Name' => 'response_type',
                            'Value' => 'ERROR',
                        ],
                    ],
                    'Value' => 67,
                    'Unit' => 'Count',
                ],
        ], $metric->getMetricData());
    }

    public function testMetricCreateCorrectKeysToCache()
    {
        $metric = new ApiResponseMetric('BRANDING', 'ERROR', 5);
        $this->assertEquals(
            ['api_response#BRANDING#ERROR' => 5],
            $metric->getCacheKeyValuePairs()
        );
    }

    public function testMetricCanSetCountFromCachedKey()
    {
        $metric = new ApiResponseMetric('BRANDING', 'ERROR', 0);
        $metric->setValuesFromCacheKeyValuePairs(['api_response#BRANDING#ERROR' => 5]);
        $this->assertEquals([
                [
                    'MetricName' => 'api_count',
                    'Dimensions' => [
                        [
                            'Name' => 'api',
                            'Value' => 'BRANDING',
                        ],
                        [
                            'Name' => 'response_type',
                            'Value' => 'ERROR',
                        ],
                    ],
                    'Value' => 5,
                    'Unit' => 'Count',
                ],
        ], $metric->getMetricData());
    }
}
