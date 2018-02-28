<?php
declare(strict_types = 1);

namespace App\EventSubscriber;

use GuzzleHttp\ClientInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use GuzzleHttp\Exception\GuzzleException;

class WibbleSubscriber implements EventSubscriberInterface
{
    /**
     * @var ClientInterface
     */
    private $guzzleClient;

    public function __construct(ClientInterface $guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => [['responseEvent', 0]],
            KernelEvents::EXCEPTION => [['exceptionEvent', 0]],
        ];
    }

    public function responseEvent($event)
    {
        $a = 1;
        //        var_dump($event);
//        die();
    }

    public function exceptionEvent(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        if ($exception instanceof NotFoundHttpException) {
            $request = $event->getRequest();
            $path = $request->getPathInfo();
            $path = preg_replace('/^\//', '', $path);
            if (strpos($path, 'blogs/', 0) !== 0) {
                return;
            }
            if (!preg_match('/(\/|\.[a-zA-Z0-9]+)$/', $path)) {
                $path .= '/';
            }

            $url = 'https://archivewww.live.bbc.co.uk/' . $path;
            try {
                $httpResponse = $this->guzzleClient->request('GET', $url);
            } catch (GuzzleException $e) {
                return;
            }

            $response = new Response($httpResponse->getBody()->getContents(), 200, []);
            $event->allowCustomResponseCode();
            $event->setResponse($response);
            $event->stopPropagation();
            return;
        }
    }
}
