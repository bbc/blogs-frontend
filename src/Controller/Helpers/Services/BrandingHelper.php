<?php
declare(strict_types = 1);

namespace App\Controller\Helpers\Services;

use BBC\BrandingClient\Branding;
use BBC\BrandingClient\BrandingClient;
use BBC\BrandingClient\BrandingException;
use Symfony\Component\HttpFoundation\RequestStack;

class BrandingHelper
{
    /** @var string */
    private $fallbackBrandingId = 'br-07918';

    /** @var BrandingClient */
    private $brandingClient;

    /** @var RequestStack */
    private $requestStack;

    public function __construct(BrandingClient $brandingClient, RequestStack $requestStack)
    {
        $this->brandingClient = $brandingClient;
        $this->requestStack = $requestStack;
    }

    public function requestBranding(string $brandingId): Branding
    {
        if (!$brandingId) {
            $brandingId = $this->fallbackBrandingId;
        }
        $previewId = $this->requestStack->getMasterRequest()->query->get(BrandingClient::PREVIEW_PARAM, null);

        try {
            $branding = $this->brandingClient->getContent(
                $brandingId,
                $previewId
            );
        } catch (BrandingException $e) {
            // Could not find that branding id (or preview id), someone probably
            // mistyped it. Use a default branding instead of blowing up.
            $branding = $this->brandingClient->getContent($this->fallbackBrandingId, null);
        }

        return $branding;
    }
}
