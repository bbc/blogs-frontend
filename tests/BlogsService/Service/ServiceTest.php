<?php
declare(strict_types = 1);

namespace Tests\App\BlogsService\Service;

use BBC\ProgrammesCachingLibrary\CacheInterface;
use App\BlogsService\Infrastructure\IsiteFeedResponseHandler;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

abstract class ServiceTest extends TestCase
{
    /** @var  IsiteFeedResponseHandler | PHPUnit_Framework_MockObject_MockObject */
    protected $mockIsiteFeedResponseHandler;

    /** @var CacheInterface | PHPUnit_Framework_MockObject_MockObject */
    protected $mockCache;

    protected function setUpMockResponseHandler()
    {
        $this->mockIsiteFeedResponseHandler = $this->createMock(IsiteFeedResponseHandler::class);
    }

    protected function setUpMockCache()
    {
        $this->mockCache = $this->createMock(CacheInterface::class);
        $this->mockCache
            ->expects($this->once())
            ->method('getOrSet')
            ->will($this->returnCallback(
                function (string $key, $ttl, callable $function, array $arguments = []) {
                    return $function(...$arguments);
                }
            ));
    }
}
