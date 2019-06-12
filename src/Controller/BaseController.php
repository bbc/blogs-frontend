<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Image;
use App\Controller\Helpers\Services\AtiAnalyticsHelper;
use App\Controller\Helpers\Services\BrandingHelper;
use App\Controller\Helpers\Services\PageContextHelper;
use App\Controller\Helpers\ValueObjects\AtiAnalyticsLabels;
use App\Controller\Helpers\ValueObjects\PageContext;
use App\Translate\TranslateProvider;
use App\ValueObject\MetaContext;
use BBC\BrandingClient\OrbitClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractController
{
    /** @var string */
    protected $counterName = '';

    /** @var bool */
    protected $hasVideo = false;

    /** @var string */
    protected $istatsPageType = '';

    /** @var mixed[] */
    protected $otherIstatsLabels = [];

    /** @var string|null */
    protected $locale;

    /** @var string */
    private $atiChapterOne;

    /** @var bool */
    private $preview = false;

    /**
     * Private so that it cannot be overwritten by a child class, only modified via response()
     *
     * @var Response
     */
    private $response;

    /** @var PageContextHelper */
    private $pageContextHelper;

    /** @var BrandingHelper */
    private $brandingHelper;

    /** @var AtiAnalyticsHelper */
    private $atiAnalyticsHelper;

    public static function getSubscribedServices()
    {
        return array_merge(parent::getSubscribedServices(), [
            OrbitClient::class,
            TranslateProvider::class,
            PageContextHelper::class,
            BrandingHelper::class,
            AtiAnalyticsHelper::class,
        ]);
    }

    public function __construct()
    {
        $this->response = new Response();
        $this->pageContextHelper = $this->container->get(PageContextHelper::class);
        $this->brandingHelper = $this->container->get(BradingHelper::class);
        $this->atiAnalyticsHelper = $this->container->get(AtiAnalyticsHelper::class);
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

    protected function pageContextHelper(): PageContextHelper
    {
        return $this->pageContextHelper;
    }

    protected function analyticsHelper(): AtiAnalyticsHelper
    {
        return $this->atiAnalyticsHelper;
    }

    protected function setIstatsPageType(string $pageType)
    {
        $this->istatsPageType = $pageType;
    }

    protected function response(): Response
    {
        return $this->response;
    }

    protected function renderWithChrome(
        string $view,
        PageContext $pageContext,
        AtiAnalyticsLabels $analyticsLabels,
        array $viewParameters = []
    ) {
        if ($pageContext->isPreview()) {
            $this->response()->headers->remove('X-Frame-Options');
        }
        $branding = $this->brandingHelper->requestBranding();

        // We should change the language if it has been set by the blog
        // Otherwise, we should default to the language set by branding
        $locale = $this->pageContextHelper->getLocale() ?? $branding->getLocale();
        $translateProvider = $this->container->get(TranslateProvider::class);
        $translateProvider->setLocale($locale);

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
            'page_context' => $pageContext,
        ], $viewParameters);
        return $this->render($view, $viewParameters, $this->response);
    }

    protected function setLocale(string $locale)
    {
        // The translations library doesn't support multiple variations of the same language
        // so this allows us to have two different versions of English
        if ($locale === 'en-GB_articles') {
            $locale = 'articles';
        }

        $this->locale = $locale;
    }

    protected function setAtiChapterOneVariable(string $chapterOne): void
    {
        $this->atiChapterOne = $chapterOne;
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


}
