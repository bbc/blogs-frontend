<?php

namespace App\Metrics;

use App\ExternalApi\ApiType\UriToApiTypeMapper;
use GuzzleHttp\TransferStats;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;

class MetricsMiddleware
{
    /** @var MetricsManager */
    private $metricsManager;

    /** @var LoggerInterface */
    private $logger;

    /** @var UriToApiTypeMapper */
    private $uriToApiTypeMapper;

    public function __construct(MetricsManager $metricsManager, LoggerInterface $logger, UriToApiTypeMapper $uriToApiTypeMapper)
    {
        $this->metricsManager = $metricsManager;
        $this->logger = $logger;
        $this->uriToApiTypeMapper = $uriToApiTypeMapper;
    }

    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $this->logger->info("HTTP Request:" . $request->getUri());

            $options['on_stats'] = function (TransferStats $stats) use (&$responseTime) {
                $uri = $stats->getEffectiveUri();
                $apiName = $this->uriToApiTypeMapper->getApiNameFromUriInterface($uri);
                if (!$apiName) {
                    return;
                }
                $responseTime = (int) round($stats->getTransferTime() * 1000);
                // No response/timeout is logged as a 504 (gateway timeout). Which isn't correct. But whatever.
                $responseCode = 504;
                if ($stats->hasResponse()) {
                    $responseCode = $stats->getResponse()->getStatusCode();
                }
                $this->metricsManager->addApiMetric($apiName, $responseTime, $responseCode);
            };

            return $handler($request, $options);
        };
    }
}
