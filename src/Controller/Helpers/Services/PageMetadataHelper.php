<?php
declare(strict_types = 1);

namespace App\Controller\Helpers\Services;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\Image;
use App\Controller\Helpers\ValueObjects\PageMetadata;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PageMetadataHelper
{
    /** @var RequestStack */
    private $requestStack;

    /** @var UrlGeneratorInterface */
    private $router;

    /** @var bool */
    private $allowPreview = false;

    public function __construct(
        RequestStack $requestStack,
        UrlGeneratorInterface $router
    ) {
        $this->requestStack = $requestStack;
        $this->router = $router;
    }

    public function setAllowPreview(bool $allowPreview = true): void
    {
        $this->allowPreview = $allowPreview;
    }

    public function blogNameForDescription(Blog $blog): string
    {
        if (stristr($blog->getName(), 'blog')) {
            return '"' . $blog->getName() . '"';
        }
        return '"' . $blog->getName() . '" blog';
    }

    public function makePageMetadata(?string $description = null, ?Blog $blog = null, ?Image $socialImage = null): PageMetadata
    {
        return new PageMetadata(
            $this->getDescription($description, $blog),
            $this->getCanonicalUrl(),
            $this->getPageImage($socialImage, $blog),
            $this->getLocale($blog),
            $this->isPreview()
        );
    }

    public function getDescription(?string $description, ?Blog $blog): string
    {
        return $description ? $description : $blog->getDescription();
    }

    public function getCanonicalUrl(): string
    {
        $requestAttributes = $this->request()->attributes;
        return $this->router->generate(
            $requestAttributes->get('_route'),
            $requestAttributes->get('_route_params'),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    public function isPreview(): bool
    {
        if (!$this->allowPreview) {
            return false;
        }
        $preview = filter_var($this->request()->get('preview', 'false'), FILTER_VALIDATE_BOOLEAN);
        return (bool) $preview;
    }

    public function getPageImage(?Image $socialImage, ?Blog $blog): Image
    {
        if ($socialImage) {
            return $socialImage;
        }
        if ($blog) {
            return $blog->getImage();
        }
        return new Image('p01tqv8z.png');
    }

    private function request(): Request
    {
        return $this->requestStack->getMasterRequest();
    }

    private function getLocale(?Blog $blog): ?string
    {
        if (!$blog) {
            return null;
        }
        $locale = $blog->getLanguage();
        // The translations library doesn't support multiple variations of the same language
        // so this allows us to have two different versions of English
        if ($locale === 'en-GB_articles') {
            $locale = 'articles';
        }
        return $locale;
    }
}
