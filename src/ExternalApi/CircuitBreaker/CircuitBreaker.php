<?php
declare(strict_types = 1);

namespace App\ExternalApi\CircuitBreaker;

use App\ExternalApi\ApiType\ApiTypeEnum;
use App\Metrics\MetricsManager;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;

class CircuitBreaker
{
    public const MONITORING_INTERVAL = 15;

    private const APC_KEY_PREFIX = 'PRF_CIRCUIT_BREAKER';

    /** @var MetricsManager */
    private $metricsManager;

    /** @var LoggerInterface */
    private $logger;

    /** @var Apcu */
    private $apcu;

    /** @var string */
    private $apiName;

    /** @var int */
    private $maxFailsPerMinute;

    /** @var int */
    private $secondsToOpenWhenFailed;

    /**
     * CircuitBreaker constructor.
     *
     * @param MetricsManager $metricsManager
     * @param LoggerInterface $logger
     * @param Apcu $apcu
     * @param string $apiName - the name of the api, must be a constant in MetricsManager
     * @param int $maxFailsPerMinute - If more failures than this are detected in a minute, close the breaker
     * @param int $secondsToOpenWhenFailed - Open the breaker for this long if $maxFailsPerMinute are exceeded
     */
    public function __construct(
        MetricsManager $metricsManager,
        LoggerInterface $logger,
        Apcu $apcu,
        string $apiName,
        int $maxFailsPerMinute,
        int $secondsToOpenWhenFailed
    ) {
        $this->metricsManager = $metricsManager;
        $this->logger = $logger;
        $this->apcu = $apcu;
        if (!ApiTypeEnum::isValid($apiName)) {
            throw new InvalidArgumentException("$apiName is not a valid API type");
        }
        $this->apiName = $apiName;
        $this->maxFailsPerMinute = $maxFailsPerMinute;
        $this->secondsToOpenWhenFailed = $secondsToOpenWhenFailed;
    }

    public function logFailure(): void
    {
        if ($this->apcu->fetch($this->makeApcKey('is_open'))) {
            $this->logger->error('CircuitBreaker logFailure called when breaker open. Something is wrong in calling code');
            return;
        }
        $failureCountKey = $this->makeApcKey('failure_count');

        // This is potentially leaky
        // multiple requests running at the same time could end up setting $countCache to 1
        // But for the purposes of this exercise, 99% accurate is good enough
        if ($this->apcu->exists($failureCountKey)) {
            $this->apcu->inc($failureCountKey);
        } else {
            $this->apcu->store($failureCountKey, 1, self::MONITORING_INTERVAL);
        }

        // Check if we have failed and set something if so
        $maxFailures = $this->maxFailsPerMinute * (self::MONITORING_INTERVAL / 60);
        $failureCount = (int) $this->apcu->fetch($failureCountKey);
        if ($failureCount > $maxFailures) {
            $this->logger->error(
                "Closing circuit breaker for $this->apiName for $this->secondsToOpenWhenFailed seconds " .
                "due to failureCount ($failureCount) > maxFailureCount ($maxFailures)."
            );
            $this->metricsManager->addApiCircuitBreakerOpenMetric($this->apiName);
            $this->apcu->store($this->makeApcKey('is_open'), 'true', $this->secondsToOpenWhenFailed);
        }
    }

    /**
     * When this returns true, the circuit breaker is "open". Meaning too many requests
     * to this API have failed and no further requests should be attempted for now.
     *
     * Called before any API request by the CircuitBreakerMiddleware
     *
     * @return bool
     */
    public function isOpen(): bool
    {
        if ($this->apcu->fetch($this->makeApcKey('is_open'))) {
            $this->metricsManager->addApiCircuitBreakerOpenMetric($this->apiName);
            return true;
        }
        return false;
    }

    public function clear(): void
    {
        $this->apcu->delete($this->makeApcKey('is_open'));
        $this->apcu->delete($this->makeApcKey('failure_count'));
    }

    private function makeApcKey(string $name): string
    {
        $pieces = [self::APC_KEY_PREFIX, $this->apiName, $name];
        return implode('|', $pieces);
    }
}
