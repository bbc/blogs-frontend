<?php
declare(strict_types = 1);
namespace Tests\App\EventSubscriber;

use App\EventSubscriber\CacheFlushSubscriber;
use BBC\BrandingClient\BrandingClient;
use BBC\BrandingClient\OrbitClient;
use BBC\ProgrammesCachingLibrary\CacheInterface;
use BBC\ProgrammesMorphLibrary\MorphClient;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class CacheFlushSubscriberTest extends TestCase
{
    public function testCacheFlushSubscriber(): void
    {
        $cache = $this->createMock(CacheInterface::class);
        $cache->expects($this->once())->method('setFlushCacheItems')->with(true);

        $branding = $this->createMock(BrandingClient::class);
        $branding->expects($this->once())->method('setFlushCacheItems')->with(true);

        $orbit = $this->createMock(OrbitClient::class);
        $orbit->expects($this->once())->method('setFlushCacheItems')->with(true);

        $morph = $this->createMock(MorphClient::class);
        $morph->expects($this->once())->method('setFlushCacheItems')->with(true);

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->willReturnMap([
            [CacheInterface::class, $cache],
            [BrandingClient::class, $branding],
            [OrbitClient::class, $orbit],
            [MorphClient::class, $morph],
        ]);

        $request = new Request(['__flush_cache' => '']);
        $cacheFlushSubscriber = new CacheFlushSubscriber($container);
        $cacheFlushSubscriber->setupCacheFlush($this->event($request));
    }

    private function event(Request $request, bool $isMasterRequest = true): GetResponseEvent
    {
        return new GetResponseEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            $isMasterRequest ? HttpKernelInterface::MASTER_REQUEST : HttpKernelInterface::SUB_REQUEST
        );
    }
}
