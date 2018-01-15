<?php
declare(strict_types = 1);

namespace Tests\App\ExternalApi\CircuitBreaker;

use App\ExternalApi\ApiType\ApiTypeEnum;
use App\ExternalApi\CircuitBreaker\CircuitBreaker;
use App\Metrics\MetricsManager;
use Cake\Chronos\Chronos;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class CircuitBreakerTest extends TestCase
{
    private $mockMetricsManager;

    private $mockLoggerInterface;

    /** @var ApcuMock */
    private $mockApcu;

    /** @var CircuitBreaker */
    private $circuitBreaker;

    public function setUp()
    {
        $this->mockMetricsManager = $this->createMock(MetricsManager::class);
        $this->mockLoggerInterface = $this->createMock(LoggerInterface::class);
        $this->mockApcu = new ApcuMock(Chronos::now());
    }

    public function testOpens()
    {
        $maxFailuresPerMinute = 20;
        $this->createCircuitBreaker(ApiTypeEnum::API_ORBIT, $maxFailuresPerMinute, 10);
        $this->mockLoggerInterface->expects($this->once())->method('error');
        $this->mockLoggerInterface->expects($this->never())->method('warning');
        $this->mockMetricsManager->expects($this->atLeastOnce())
            ->method('addApiCircuitBreakerOpenMetric')
            ->with(ApiTypeEnum::API_ORBIT);

        $failuresToTrip = $maxFailuresPerMinute * (CircuitBreaker::MONITORING_INTERVAL / 60) + 1;
        for ($i = 0; $i < $failuresToTrip; $i++) {
            $this->circuitBreaker->logFailure();
        }
        $this->assertTrue($this->circuitBreaker->isOpen());
    }

    public function testOpensForCorrectAmountOfTime()
    {
        $maxFailuresPerMinute = 20;
        $this->createCircuitBreaker(ApiTypeEnum::API_ORBIT, $maxFailuresPerMinute, 10);

        $failuresToTrip = $maxFailuresPerMinute * (CircuitBreaker::MONITORING_INTERVAL / 60) + 1;
        for ($i = 0; $i < $failuresToTrip; $i++) {
            $this->circuitBreaker->logFailure();
        }
        $this->assertTrue($this->circuitBreaker->isOpen(), 'Breaker opens first time');
        $this->mockApcu->clockForward(9);
        $this->assertTrue($this->circuitBreaker->isOpen(), 'Breaker still open after 9s');
        $this->mockApcu->clockForward(2);
        $this->assertFalse($this->circuitBreaker->isOpen(), 'Breaker closed again after 10s expiry time');
    }

    public function testStaysClosedIfNotEnoughFails()
    {
        $maxFailuresPerMinute = 20;
        $this->createCircuitBreaker(ApiTypeEnum::API_ORBIT, $maxFailuresPerMinute, 10);
        $this->mockMetricsManager->expects($this->never())->method('addApiCircuitBreakerOpenMetric');
        $notEnough = $maxFailuresPerMinute * (CircuitBreaker::MONITORING_INTERVAL / 60);
        for ($i = 0; $i < $notEnough; $i++) {
            $this->circuitBreaker->logFailure();
        }
        $this->assertFalse($this->circuitBreaker->isOpen());
    }

    public function testStaysClosedIfFailuresAreSpacedOut()
    {
        $maxFailuresPerMinute = 20;
        $this->createCircuitBreaker(ApiTypeEnum::API_ORBIT, $maxFailuresPerMinute, 10);
        $this->mockMetricsManager->expects($this->never())->method('addApiCircuitBreakerOpenMetric');
        $failureThreshold = $maxFailuresPerMinute * (CircuitBreaker::MONITORING_INTERVAL / 60);
        for ($clockLoopCounter = 0; $clockLoopCounter < 10; $clockLoopCounter++) {
            for ($i = 0; $i < $failureThreshold; $i++) {
                $this->circuitBreaker->logFailure();
            }
            $this->mockApcu->clockForward(CircuitBreaker::MONITORING_INTERVAL + 1);
        }

        $this->assertFalse($this->circuitBreaker->isOpen());
    }

    private function createCircuitBreaker(string $apiName, int $maxFailsPerMinute, $secondsToOpenWhenFailed)
    {
        $this->circuitBreaker = new CircuitBreaker(
            $this->mockMetricsManager,
            $this->mockLoggerInterface,
            $this->mockApcu,
            $apiName,
            $maxFailsPerMinute,
            $secondsToOpenWhenFailed
        );
    }
}
