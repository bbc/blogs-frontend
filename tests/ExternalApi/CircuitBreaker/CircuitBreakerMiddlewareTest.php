<?php
declare(strict_types = 1);

namespace Tests\App\ExternalApi\CircuitBreaker;

use App\ExternalApi\ApiType\ApiTypeEnum;
use App\ExternalApi\ApiType\UriToApiTypeMapper;
use App\ExternalApi\CircuitBreaker\CircuitBreaker;
use App\ExternalApi\CircuitBreaker\CircuitBreakerFactory;
use App\ExternalApi\CircuitBreaker\CircuitBreakerMiddleware;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Class CircuitBreakerMiddlewareTest
 *
 * Testing guzzle middleware? What's more fun than that! Root canal possibly...
 */
class CircuitBreakerMiddlewareTest extends TestCase
{
    /** @var CircuitBreakerMiddleware */
    private $circuitBreakerMiddleware;

    private $mockLogger;

    private $mockBreakerFactory;

    private $mockUrlMapper;

    public function setUp()
    {
        $this->mockLogger = $this->createMock(LoggerInterface::class);
        $this->mockBreakerFactory = $this->createMock(CircuitBreakerFactory::class);
        $this->mockUrlMapper = $this->createMock(UriToApiTypeMapper::class);
        $this->circuitBreakerMiddleware = new CircuitBreakerMiddleware(
            $this->mockLogger,
            $this->mockBreakerFactory,
            $this->mockUrlMapper
        );
    }

    /**
     * @expectedException \App\ExternalApi\Exception\CircuitBreakerClosedException
     */
    public function testOpenBreakerThrowsCorrectException()
    {
        $guzzleClient = $this->createGuzzleClient(new Response(200, [], 'Response!!!ONE'));
        $uri = new Uri('http://www.api.com');

        // Set mock breaker factory to return an Open Circuit breaker
        $this->mockUrlMapper->expects($this->any())
            ->method('getApiNameFromUriInterface')
            ->with($uri)
            ->willReturn(ApiTypeEnum::API_ORBIT);
        $mockBreaker = $this->createMock(CircuitBreaker::class);
        $this->mockBreakerFactory->expects($this->once())
            ->method('getBreakerFor')
            ->with(ApiTypeEnum::API_ORBIT)
            ->willReturn($mockBreaker);

        $mockBreaker->expects($this->once())
            ->method('isOpen')
            ->willReturn(true);

        // Make HTTP call
        $guzzleClient->get($uri);
    }

    /**
     * @dataProvider responseToFailureProvider
     */
    public function testCorrectResponsesLoggedAsFailures(int $responseCode, bool $shouldFail)
    {
        $guzzleClient = $this->createGuzzleClient(new Response($responseCode, [], 'Content'));
        $uri = new Uri('http://www.api.com');

        // Set mock breaker factory to return a circuit breaker
        $this->mockUrlMapper->expects($this->any())
            ->method('getApiNameFromUriInterface')
            ->with($uri)
            ->willReturn(ApiTypeEnum::API_ORBIT);
        $mockBreaker = $this->createMock(CircuitBreaker::class);
        $this->mockBreakerFactory->expects($this->once())
            ->method('getBreakerFor')
            ->with(ApiTypeEnum::API_ORBIT)
            ->willReturn($mockBreaker);

        $mockBreaker->expects($this->once())
            ->method('isOpen')
            ->willReturn(false);

        if ($shouldFail) {
            $mockBreaker->expects($this->once())
                ->method('logFailure');
        } else {
            $mockBreaker->expects($this->never())
                ->method('logFailure');
        }

        // Make HTTP call
        try {
            $guzzleClient->get($uri);
        } catch (RequestException $e) {
            // Some HTTP errors generate an exception.
            // Normal guzzle behaviour. We don't care.
        }
    }

    public function responseToFailureProvider()
    {
        return [
            [200, false],
            [201, false],
            [301, false],
            [400, true],
            [403, true],
            [404, false],
            [500, true],
            [503, true],
        ];
    }


    /**
     * @expectedException \GuzzleHttp\Exception\ConnectException
     */
    public function testErrorsLoggedAsFailures()
    {
        $uri = new Uri('http://www.api.com');
        $request = new Request('GET', $uri);
        $guzzleClient = $this->createGuzzleClient(new ConnectException('Timeout etc.', $request));

        $this->mockUrlMapper->expects($this->any())
            ->method('getApiNameFromUriInterface')
            ->with($uri)
            ->willReturn(ApiTypeEnum::API_ORBIT);
        $mockBreaker = $this->createMock(CircuitBreaker::class);
        $this->mockBreakerFactory->expects($this->once())
            ->method('getBreakerFor')
            ->with(ApiTypeEnum::API_ORBIT)
            ->willReturn($mockBreaker);

        $mockBreaker->expects($this->once())
            ->method('isOpen')
            ->willReturn(false);

        $mockBreaker->expects($this->once())->method('logFailure');
        $guzzleClient->get($uri);
    }

    public function testNormalResponseWhenBreakerExistsForUrl()
    {
        $guzzleClient = $this->createGuzzleClient(new Response(200, [], 'Content'));
        $uri = new Uri('http://www.api.com');

        $this->mockUrlMapper->expects($this->any())
            ->method('getApiNameFromUriInterface')
            ->with($uri)
            ->willReturn(ApiTypeEnum::API_ORBIT);
        $mockBreaker = $this->createMock(CircuitBreaker::class);
        $this->mockBreakerFactory->expects($this->once())
            ->method('getBreakerFor')
            ->with(ApiTypeEnum::API_ORBIT)
            ->willReturn($mockBreaker);

        $mockBreaker->expects($this->once())
            ->method('isOpen')
            ->willReturn(false);

        $mockBreaker->expects($this->never())->method('logFailure');

        // Make HTTP call
        $response = $guzzleClient->get($uri);
        $this->assertEquals('Content', $response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testNormalResponseWhenBreakerDoesNotExistForUrl()
    {
        $guzzleClient = $this->createGuzzleClient(new Response(200, [], 'Content'));
        $uri = new Uri('http://www.api.com');

        $this->mockUrlMapper->expects($this->any())
            ->method('getApiNameFromUriInterface')
            ->with($uri)
            ->willReturn(null);

        $this->mockBreakerFactory->expects($this->never())
            ->method('getBreakerFor');

        // Make HTTP call
        $response = $guzzleClient->get($uri);
        $this->assertEquals('Content', $response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testErrorsFromUrlsThatDontHaveCircuitBreakersAreIgnored()
    {
        $guzzleClient = $this->createGuzzleClient(new Response(500, [], 'Content'));
        $uri = new Uri('http://www.api.com');

        $this->mockUrlMapper->expects($this->any())
            ->method('getApiNameFromUriInterface')
            ->with($uri)
            ->willReturn(null);

        $this->mockBreakerFactory->expects($this->never())
            ->method('getBreakerFor');

        // Make HTTP call
        try {
            $guzzleClient->get($uri);
        } catch (RequestException $e) {
            // Some HTTP errors generate an exception.
            // Normal guzzle behaviour. We don't care.
        }
    }

    /**
     * @param Response|\Exception $responseOrException
     * @return Client
     */
    private function createGuzzleClient($responseOrException)
    {
        $stack = MockHandler::createWithMiddleware([$responseOrException]);
        $stack->push($this->circuitBreakerMiddleware);
        return new Client(['handler' => $stack]);
    }
}
