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

            if ($this->isInvalidPath($path)) {
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

    private function isInvalidPath(string $path)
    {
        if (strpos($path, 'blogs/', 0) !== 0) {
            return true;
        }
        // Directory traversal isn't actually a problem, but it
        // does generate spurious 5xx errors due to the redirect
        // that the backend API does. Disable it.
        if (preg_match('/(\.|%2e){2}\//', $path)) {
            return true;
        }

        if (strlen($path) > 995) {
            return true;
        }
        return false;
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
        return $path;
    }
}
