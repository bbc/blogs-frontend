<?php
declare (strict_types = 1);

namespace App\EventSubscriber;

use App\BlogsService\Service\LegacyBlogService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ArchivedBlogsSubscriber implements EventSubscriberInterface
{
    /**
     * @var LegacyBlogService
     */
    private $legacyBlogService;

    public function __construct(LegacyBlogService $legacyBlogService)
    {
        $this->legacyBlogService = $legacyBlogService;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => [['exceptionEvent', 0]],
        ];
    }

    public function exceptionEvent(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        if ($exception instanceof NotFoundHttpException) {
            $request = $event->getRequest();
            $path = $this->cleanUpPath($request->getPathInfo());

            if (strpos($path, 'blogs/', 0) !== 0) {
                return;
            }

            $response = $this->legacyBlogService->getLegacyBlog($path);
            if (!$response) {
                // It can return null so make it an actual 404 if it does
                return;
            }
            $event->allowCustomResponseCode();
            $event->setResponse($response);
            return;
        }
    }

    private function cleanUpPath(string $path)
    {
        $path = preg_replace('/^\/{0,1000}/', '', $path);
        // Strip out query strings
        $path = preg_replace('/(\?[a-zA-z0-9]{0,1000})/', '', $path);
        // Strip out anything that shouldn't be in a URL
        $path = preg_replace('/[^A-Za-z0-9_\.\-\/]{0,1000}/', '', $path);
        // if it doesn't end with a file extenstion then add a trailing /
        if (!preg_match('/(\/|\.[a-zA-Z0-9]{0,1000})$/', $path)) {
            $path .= '/';
        }
        // Take out any ../'s that could allow for directory traversal
        $path = preg_replace('/(\.\.\/|\%2e){0,1000}/', '', $path);
        return $path;
    }
}
