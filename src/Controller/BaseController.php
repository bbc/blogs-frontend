<?php
declare(strict_types = 1);

namespace App\Controller;

use App\Translate\TranslateProvider;
use App\ValueObject\AnalyticsCounterName;
use App\ValueObject\CosmosInfo;
use App\ValueObject\IstatsAnalyticsLabels;
use App\ValueObject\MetaContext;
use BBC\BrandingClient\Branding;
use BBC\BrandingClient\BrandingClient;
use BBC\BrandingClient\BrandingException;
use BBC\BrandingClient\OrbitClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractController
{
    /** @var string */
    protected $counterName = '';

    /** @var bool */
    protected $hasVideo = false;

    /** @var mixed[] */
    protected $otherIstatsLabels = [];

    /** @var string|null */
    protected $locale;

    /** @var string */
    private $brandingId = 'br-07918';

    /** @var string */
    private $fallbackBrandingId = 'br-07918';

    /**
     * Private so that it cannot be overwritten by a child class, only modified via response()
     *
     * @var Response
     */
    private $response;

    public static function getSubscribedServices()
    {
        return array_merge(parent::getSubscribedServices(), [
            BrandingClient::class,
            OrbitClient::class,
            TranslateProvider::class,
            CosmosInfo::class,
        ]);
    }

    public function __construct()
    {
        $this->response = new Response();
        // It is required to set the cache-control header when creating the response object otherwise Symfony
        // will create and set its value to "no-cache, private" by default
        $this->response()->setPublic()->setMaxAge(300);
        // The page can only be displayed in a frame on the same origin as the page itself.
        $this->response()->headers->set('X-Frame-Options', 'SAMEORIGIN');
        // Blocks a request if the requested type is different from the MIME type
        $this->response()->headers->set('X-Content-Type-Options', 'nosniff');
    }

    abstract protected function getIstatsPageType(): string;

    protected function response(): Response
    {
        return $this->response;
    }

    /**
     * @param string $view
     * @param mixed[] $parameters
     * @return Response
     */
    protected function renderWithChrome(string $view, array $parameters = [])
    {
        $branding = $this->requestBranding();

        // We should change the language if it has been set by the blog
        // Otherwise, we should default to the language set by branding
        $locale = $this->locale ?? $branding->getLocale();

        $translateProvider = $this->container->get(TranslateProvider::class);
        $cosmosInfo = $this->container->get(CosmosInfo::class);
        $istatsAnalyticsLabels = new IstatsAnalyticsLabels($parameters['blog'] ?? null, $this->getIstatsPageType(), $cosmosInfo->getAppVersion(), $this->hasVideo, $this->otherIstatsLabels);
        $istatsCounterName = (string) new AnalyticsCounterName($parameters['blog'] ?? null, $this->counterName);

        $translateProvider->setLocale($locale);
        $orb = $this->container->get(OrbitClient::class)->getContent([
            'variant' => $branding->getOrbitVariant(),
            'language' => $branding->getLanguage(),
        ], [
            'searchScope' => $branding->getOrbitSearchScope(),
            'skipLinkTarget' => 'programmes-content',
            'analyticsCounterName' => $istatsCounterName,
            'analyticsLabels' => $istatsAnalyticsLabels->orbLabels(),
        ]);
        $parameters = array_merge([
            'orb' => $orb,
            'branding' => $branding,
            'meta_context' => new MetaContext(),
            'istats_counter_name' => $istatsCounterName,
        ], $parameters);
        return $this->render($view, $parameters, $this->response);
    }

    protected function request(): Request
    {
        return $this->container->get('request_stack')->getCurrentRequest();
    }

    protected function setBrandingId(string $brandingId)
    {
        if ($brandingId) {
            $this->brandingId = $brandingId;
        }
    }

    protected function setLocale(string $locale)
    {
        $this->locale = $locale;
    }

    private function requestBranding(): Branding
    {
        $brandingClient = $this->container->get(BrandingClient::class);
        $previewId = $this->request()->query->get($brandingClient::PREVIEW_PARAM, null);

        try {
            $branding = $brandingClient->getContent(
                $this->brandingId,
                $previewId
            );
        } catch (BrandingException $e) {
            // Could not find that branding id (or preview id), someone probably
            // mistyped it. Use a default branding instead of blowing up.
            $this->setBrandingId($this->fallbackBrandingId);
            $branding = $brandingClient->getContent($this->brandingId, null);
        }

        return $branding;
    }
}
