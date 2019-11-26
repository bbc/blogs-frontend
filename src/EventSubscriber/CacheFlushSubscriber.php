<?php
declare(strict_types = 1);

namespace App\EventSubscriber;

use BBC\BrandingClient\BrandingClient;
use BBC\BrandingClient\OrbitClient;
use BBC\ProgrammesCachingLibrary\CacheInterface;
use BBC\ProgrammesMorphLibrary\MorphClient;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CacheFlushSubscriber implements EventSubscriberInterface, ServiceSubscriberInterface
{
    /** @var ContainerInterface */
    private $container;

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['setupCacheFlush', 512]],
        ];
    }

    public static function getSubscribedServices(): array
    {
        return [BrandingClient::class, OrbitClient::class, CacheInterface::class, MorphClient::class];
    }

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function setupCacheFlush(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        if ($event->getRequest()->query->has('__flush_cache')) {
            $cache = $this->container->get(CacheInterface::class);
            $cache->setFlushCacheItems(true);

            $brandingCache = $this->container->get(BrandingClient::class);
            $brandingCache->setFlushCacheItems(true);

            $orbitCache = $this->container->get(OrbitClient::class);
            $orbitCache->setFlushCacheItems(true);

            $morphCache = $this->container->get(MorphClient::class);
            $morphCache->setFlushCacheItems(true);
        }
    }
}
