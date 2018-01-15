<?php
declare(strict_types = 1);

namespace App\ExternalApi\CircuitBreaker;

use App\ExternalApi\ApiType\ApiTypeEnum;
use App\Metrics\MetricsManager;
use Psr\Log\LoggerInterface;
use InvalidArgumentException;

class CircuitBreakerFactory
{
    /** @var MetricsManager */
    private $metricsManager;

    /** @var LoggerInterface */
    private $logger;

    /** @var Apcu */
    private $apcu;

    private $circuitBreakers = [];

    /**
     * Any API name added here gets a circuit breaker.
     * Anything not added, doesn't.
     */
    private const BREAKER_PARAMETERS = [
        ApiTypeEnum::API_ORBIT              => ['maxFailsPerMinute' => 40, 'secondsToOpenWhenFailed' => 20],
        ApiTypeEnum::API_COMMENTS           => ['maxFailsPerMinute' => 40, 'secondsToOpenWhenFailed' => 20],
        ApiTypeEnum::API_ISITE              => ['maxFailsPerMinute' => 40, 'secondsToOpenWhenFailed' => 20],
        ApiTypeEnum::API_MORPH              => ['maxFailsPerMinute' => 40, 'secondsToOpenWhenFailed' => 20],
    ];

    public function __construct(MetricsManager $metricsManager, LoggerInterface $logger, Apcu $apcu)
    {
        $this->metricsManager = $metricsManager;
        $this->logger = $logger;
        $this->apcu = $apcu;
    }

    public function getBreakerFor(string $apiName): ?CircuitBreaker
    {
        if (!ApiTypeEnum::isValid($apiName)) {
            throw new InvalidArgumentException("$apiName is not a valid API name");
        }
        if (!isset(self::BREAKER_PARAMETERS[$apiName])) {
            // Some things don't need a circuit breaker
            return null;
        }
        if (!isset($this->circuitBreakers[$apiName])) {
            $this->circuitBreakers[$apiName] = new CircuitBreaker(
                $this->metricsManager,
                $this->logger,
                $this->apcu,
                $apiName,
                self::BREAKER_PARAMETERS[$apiName]['maxFailsPerMinute'],
                self::BREAKER_PARAMETERS[$apiName]['secondsToOpenWhenFailed']
            );
        }
        return $this->circuitBreakers[$apiName];
    }
}
