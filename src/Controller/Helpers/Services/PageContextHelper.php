<?php
declare(strict_types = 1);

namespace App\Controller\Helpers\Services;

use App\BlogsService\Domain\Author;
use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\Image;
use App\BlogsService\Domain\Post;
use App\Controller\Helpers\ValueObjects\PageContext;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PageContextHelper
{
    /** @var RequestStack */
    private $requestStack;

    /** @var Blog */
    private $blog;

    /** @var Image */
    private $socialImage;

    /** @var string */
    private $description;

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

    public function setBlog(Blog $blog)
    {
        $this->blog = $blog;
    }

    public function setSocialImage(?Image $image)
    {
        $this->socialImage = $image;
    }

    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    public function setAllowPreview(bool $allowPreview = true)
    {
        $this->allowPreview = $allowPreview;
    }

    public function makePageContext()
    {
        return new PageContext(
            $this->description,
            $this->getCanonicalUrl(),
            $this->getPageImage(),
            $this->isPreview()
        );
    }

    public function getCanonicalUrl()
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

    public function getPageImage(): Image
    {
        if ($this->socialImage) {
            return $this->socialImage;
        }
        if ($this->blog && $this->blog->getImage()) {
            return $this->blog->getImage();
        }
        return new Image('p01tqv8z.png');
    }

    private function request(): Request
    {
        return $this->requestStack->getMasterRequest();
    }
}
