<?php
declare(strict_types = 1);

namespace App\Controller;

use App\Controller\Helpers\Services\AtiAnalyticsHelper;
use App\Controller\Helpers\Services\BrandingHelper;
use App\Controller\Helpers\Services\PageMetadataHelper;
use App\Controller\Helpers\ValueObjects\AtiAnalyticsLabels;
use App\Controller\Helpers\ValueObjects\PageMetadata;
use Symfony\Contracts\Translation\TranslatorInterface;
use BBC\BrandingClient\Branding;
use BBC\BrandingClient\OrbitClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractController
{
    /**
     * These are private so that they cannot be overwritten by a child class,
     * only modified via returning the object from a function
     */

    /**
     * @var Response
     */
    private $response;

    /** @var PageMetadataHelper */
    private $pageMetadataHelper;

    /** @var BrandingHelper */
    private $brandingHelper;

    /** @var AtiAnalyticsHelper */
    private $atiAnalyticsHelper;

    public static function getSubscribedServices()
    {
        return array_merge(parent::getSubscribedServices(), [
            OrbitClient::class,
            TranslatorInterface::class,
        ]);
    }

    public function __construct(
        PageMetadataHelper $pageMetadataHelper,
        BrandingHelper $brandingHelper,
        AtiAnalyticsHelper $atiAnalyticsHelper
    ) {
        $this->response = new Response();
        $this->pageMetadataHelper = $pageMetadataHelper;
        $this->brandingHelper = $brandingHelper;
        $this->atiAnalyticsHelper = $atiAnalyticsHelper;
        // It is required to set the cache-control header when creating the response object otherwise Symfony
        // will create and set its value to "no-cache, private" by default
        $this->response()->setPublic()->setMaxAge(300);
        // The page can only be displayed in a frame on the same origin as the page itself.
        $this->response()->headers->set('X-Frame-Options', 'SAMEORIGIN');
        // Blocks a request if the requested type is different from the MIME type
        $this->response()->headers->set('X-Content-Type-Options', 'nosniff');
    }

    protected function brandingHelper(): BrandingHelper
    {
        return $this->brandingHelper;
    }

    protected function pageMetadataHelper(): PageMetadataHelper
    {
        return $this->pageMetadataHelper;
    }

    protected function atiAnalyticsHelper(): AtiAnalyticsHelper
    {
        return $this->atiAnalyticsHelper;
    }

    protected function response(): Response
    {
        return $this->response;
    }

    protected function renderWithBrandingAndOrbit(
        string $view,
        PageMetadata $pageMetadata,
        AtiAnalyticsLabels $analyticsLabels,
        Branding $branding,
        array $viewParameters = []
    ) {
        if ($pageMetadata->isPreview()) {
            $this->response()->headers->remove('X-Frame-Options');
        }

        // We should change the language if it has been set by the blog
        // Otherwise, we should default to the language set by branding
        $locale = $pageMetadata->getLocale() ?? $branding->getLocale();
        $locale = $this->fixLocaleString($locale);
        $translator = $this->container->get(TranslatorInterface::class);
        $translator->setLocale($locale);

        $orb = $this->container->get(OrbitClient::class)->getContent([
            'variant' => $branding->getOrbitVariant(),
            'language' => $branding->getLanguage(),
        ], [
            'page' => $analyticsLabels->getLabels(),
            'searchScope' => $branding->getOrbitSearchScope(),
            'skipLinkTarget' => 'blogs-content',
        ]);
        $viewParameters = array_merge([
            'orb' => $orb,
            'branding' => $branding,
            'page_metadata' => $pageMetadata,
        ], $viewParameters);
        return $this->render($view, $viewParameters, $this->response);
    }

    protected function cachedRedirect($url, $status = 302): RedirectResponse
    {
        $headers = $this->response->headers->all();
        return new RedirectResponse($url, $status, $headers);
    }

    protected function cachedRedirectToRoute($route, array $parameters = [], $status = 302): RedirectResponse
    {
        return $this->cachedRedirect($this->generateUrl($route, $parameters), $status);
    }

    private function fixLocaleString(string $locale): string
    {
        $locale = str_replace('-', '_', $locale);
        if (strpos($locale, '_') !== false) {
            $locale = substr($locale, 0, -strlen(strstr($locale, '_')));
        }

        return $locale;
    }
}
