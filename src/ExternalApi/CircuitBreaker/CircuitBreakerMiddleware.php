<?php

namespace App\ExternalApi\CircuitBreaker;

use App\ExternalApi\ApiType\UriToApiTypeMapper;
use App\ExternalApi\Exception\CircuitBreakerClosedException;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;

class CircuitBreakerMiddleware
{
    /** @var LoggerInterface */
    private $logger;

    /** @var CircuitBreakerFactory */
    private $circuitBreakerFactory;

    /** @var UriToApiTypeMapper */
    private $urlToApiTypeMapper;

    public function __construct(
        LoggerInterface $logger,
        CircuitBreakerFactory $circuitBreakerFactory,
        UriToApiTypeMapper $urlToApiTypeMapper
    ) {
        $this->logger = $logger;
        $this->circuitBreakerFactory = $circuitBreakerFactory;
        $this->urlToApiTypeMapper = $urlToApiTypeMapper;
    }

    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $uri = $request->getUri();
            $apiName = $this->urlToApiTypeMapper->getApiNameFromUriInterface($uri);
            if ($apiName && ($circuitBreaker = $this->circuitBreakerFactory->getBreakerFor($apiName))) {
                if ($circuitBreaker->isOpen()) {
                    $this->logger->error("Circuit breaker for $apiName open. Not making HTTP call");
                    $reason = new CircuitBreakerClosedException("Circuit breaker is open", $request);
                    return \GuzzleHttp\Promise\rejection_for($reason);
                }
                return $handler($request, $options)->then($this->onFulfilled($circuitBreaker), $this->onRejected($circuitBreaker));
            }
            return $handler($request, $options);
        };
    }

    private function onFulfilled(CircuitBreaker $circuitBreaker)
    {
        return function ($value) use ($circuitBreaker) {
            if ($value instanceof Response) {
                // We consider all 5xx and 4xx (except 404) errors to be API failures
                $statusCode = $value->getStatusCode();
                $isClientError = ($statusCode >= 400 && $statusCode <= 499 && $statusCode != 404);
                $isServerError = ($statusCode >= 500 && $statusCode <= 599);
                if ($isClientError || $isServerError) {
                    $circuitBreaker->logFailure();
                }
            }
            return $value;
        };
    }

    private function onRejected(CircuitBreaker $circuitBreaker)
    {
        return function ($reason) use ($circuitBreaker) {
            // Transport failures end up here
            $circuitBreaker->logFailure();
            return \GuzzleHttp\Promise\rejection_for($reason);
        };
    }
}
