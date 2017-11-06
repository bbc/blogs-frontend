<?php
declare(strict_types = 1);

namespace App\Controller;

use App\Translate\TranslateProvider;
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
    private $brandingId = 'br-07918';

    /** @var string */
    private $fallbackBrandingId = 'br-07918';

    public static function getSubscribedServices()
    {
        return array_merge(parent::getSubscribedServices(), [
            BrandingClient::class,
            OrbitClient::class,
            TranslateProvider::class,
        ]);
    }

    protected function renderWithChrome($view, array $parameters = [], Response $response = null)
    {
        $branding = $this->requestBranding();
        // We only need to change the translation language if it is different
        // to the language the translation extension was initially created with
        $locale = $branding->getLocale();

        $translateProvider = $this->container->get(TranslateProvider::class);

        $translateProvider->setLocale($locale);
        $orb = $this->container->get(OrbitClient::class)->getContent([
            'variant' => $branding->getOrbitVariant(),
            'language' => $branding->getLanguage(),
        ], [
            'searchScope' => $branding->getOrbitSearchScope(),
            'skipLinkTarget' => 'programmes-content',
        ]);
        $parameters = array_merge([
            'orb' => $orb,
            'branding' => $branding,
        ], $parameters);
        return $this->render($view, $parameters, $response);
    }

    protected function request(): Request
    {
        return $this->container->get('request_stack')->getCurrentRequest();
    }

    protected function setBrandingId(string $brandingId)
    {
        $this->brandingId = $brandingId;
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
