<?php
declare(strict_types = 1);

namespace Tests\App\Metrics\BlogsMetrics;

use App\Metrics\BlogsMetrics\RouteMetric;
use PHPUnit\Framework\TestCase;

/**
 * @group metrics
 */
class RouteMetricTest extends TestCase
{
    public function testMetricProvideCorrectDataStructureForAWS()
    {
        $metric = new RouteMetric('SchedulesByDayController', 1233);
        $this->assertEquals([
                [
                    'MetricName' => 'route_count',
                    'Dimensions' => [
                        [
                            'Name' => 'controller',
                            'Value' => 'SchedulesByDayController',
                        ],
                    ],
                    'Value' => 1,
                    'Unit' => 'Count',
                ],
                [
                    'MetricName' => 'route_time',
                    'Dimensions' => [
                        [
                            'Name' => 'controller',
                            'Value' => 'SchedulesByDayController',
                        ],
                    ],
                    'Value' => 1233,
                    'Unit' => 'Milliseconds',
                ],
            ], $metric->getMetricData());
    }

    public function testMetricCreateCorrectKeysToCache()
    {
        $metric = new RouteMetric('SchedulesByDayController', 1233, 666);

        $this->assertEquals(
            [
                'route#SchedulesByDayController#count' => 666,
                'route#SchedulesByDayController#time' => 1233,
            ],
            $metric->getCacheKeyValuePairs()
        );
    }

    public function testWeSendAvgTimeToAws()
    {
        $timeMs = 1000;
        $count = 10;
        $metric = new RouteMetric('SchedulesByDayController', $timeMs, $count);

        $this->assertEquals(
            $timeMs/$count,
            $metric->getMetricData()[1]['Value']
        );
    }

    public function testNoRequestsMeansNoAverageTimeSent()
    {
        $metric = new RouteMetric('SchedulesByDayController', 0, 0);
        foreach ($metric->getMetricData() as $metricDatum) {
            $this->assertNotEquals('route_time', $metricDatum['MetricName']);
        }
    }

    public function testMetricCanSetCountFromCachedKey()
    {
        $timeMs = 7777;
        $count = 12;

        $metric = new RouteMetric('SchedulesByDayController', 10);
        $metric->setValuesFromCacheKeyValuePairs(
            [
                'route#SchedulesByDayController#time' => $timeMs,
                'route#SchedulesByDayController#count' => $count,
            ]
        );

        $this->assertEquals(
            [
                [
                    'MetricName' => 'route_count',
                    'Dimensions' => [
                        [
                            'Name' => 'controller',
                            'Value' => 'SchedulesByDayController',
                        ],
                    ],
                    'Value' => $count,
                    'Unit' => 'Count',
                ],
                [
                    'MetricName' => 'route_time',
                    'Dimensions' => [
                        [
                            'Name' => 'controller',
                            'Value' => 'SchedulesByDayController',
                        ],
                    ],
                    'Value' => $timeMs/$count,
                    'Unit' => 'Milliseconds',
                ],
            ],
            $metric->getMetricData()
        );
    }
}
