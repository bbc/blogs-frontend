<?php
declare(strict_types = 1);
namespace App\EventSubscriber;

use App\Metrics\MetricsManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Stopwatch\Stopwatch;

class MonitoringSubscriber implements EventSubscriberInterface
{
    const REQUEST_TIMER = 'app.request_time';

    private $logger;
    private $stopwatch;

    /** @var MetricsManager */
    private $metricsManager;

    public function __construct(LoggerInterface $logger, Stopwatch $stopwatch, MetricsManager $metricsManager)
    {
        $this->logger = $logger;
        $this->stopwatch = $stopwatch;
        $this->metricsManager = $metricsManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            // Start timer
            KernelEvents::REQUEST => [['requestStart', 512]],
            // Stop timer and log the duration
            KernelEvents::TERMINATE => [['terminateEnd', 128]],
        ];
    }

    public function requestStart(KernelEvent $event)
    {
        if ($event->isMasterRequest()) {
            $this->stopwatch->start(self::REQUEST_TIMER, 'section');
        }
    }

    public function terminateEnd(KernelEvent $event)
    {
        $this->sendMetrics($event);
    }

    private function sendMetrics(KernelEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        $request = $event->getRequest();
        $controllerName = $request->get('_controller');
        if ($this->stopwatch->isStarted(self::REQUEST_TIMER)) {
            $this->stopwatch->stop(self::REQUEST_TIMER);
        }

        $controllerPeriod = $this->getControllerPeriod();
        if ($controllerPeriod && $controllerName) {
            $this->metricsManager->addRouteMetric($controllerName, $controllerPeriod);
        }

        $this->metricsManager->send();
    }

    private function getControllerPeriod()
    {
        foreach ($this->stopwatch->getSections() as $section) {
            $event = $section->getEvents()[self::REQUEST_TIMER] ?? null;
            if ($event) {
                return (int) round($event->getDuration());
            }
        }

        return null;
    }
}
