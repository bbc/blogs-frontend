<?php
declare(strict_types = 1);

namespace Tests\App\ExternalApi\CircuitBreaker;

use App\ExternalApi\ApiType\ApiTypeEnum;
use App\ExternalApi\CircuitBreaker\Apcu;
use App\ExternalApi\CircuitBreaker\CircuitBreaker;
use App\ExternalApi\CircuitBreaker\CircuitBreakerFactory;
use App\Metrics\MetricsManager;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class CircuitBreakerFactoryTest extends TestCase
{
    private $mockMetricsManager;

    private $mockLogger;

    private $mockApcu;

    /** @var CircuitBreakerFactory */
    private $circuitBreakerFactory;

    public function setUp()
    {
        $this->mockMetricsManager = $this->createMock(MetricsManager::class);
        $this->mockLogger = $this->createMock(LoggerInterface::class);
        $this->mockApcu = $this->createMock(Apcu::class);
        $this->circuitBreakerFactory = new CircuitBreakerFactory(
            $this->mockMetricsManager,
            $this->mockLogger,
            $this->mockApcu
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGettingInvalidApiThrows()
    {
        $this->circuitBreakerFactory->getBreakerFor('A frightened ocelot');
    }

    public function testGettingValidApiReturns()
    {
        $this->assertInstanceOf(
            CircuitBreaker::class,
            $this->circuitBreakerFactory->getBreakerFor(ApiTypeEnum::API_ORBIT)
        );
    }

    public function testBrandingHasNoBreaker()
    {
        $this->assertNull(
            $this->circuitBreakerFactory->getBreakerFor(ApiTypeEnum::API_BRANDING)
        );
    }
}
