<?php
declare(strict_types = 1);

namespace App\Ds\Organism\Module;

use App\BlogsService\Domain\Blog;
use App\Ds\Presenter;

class AboutPresenter extends Presenter
{
    /** @var Blog */
    private $blog;

    public function __construct(Blog $blog, array $options = [])
    {
        parent::__construct($options);

        $this->blog = $blog;
    }

    public function getBlogId(): string
    {
        return str_replace(Blog::BLOG_PREFIX, '', $this->blog->getId());
    }

    public function getBlogName(): string
    {
        return $this->blog->getName();
    }

    public function getDescription(): string
    {
        return $this->blog->getDescription();
    }

    public function getFacebookUrl(): ?string
    {
        if ($this->blog->getSocial() === null) {
            return null;
        }

        return $this->blog->getSocial()->getFacebookUrl();
    }

    public function getImageUrl(int $width): string
    {
        return $this->blog->getImage()->getUrl($width);
    }

    public function getProseClasses(): string
    {
        $classes = 'grid';
        if ($this->shouldShowImage()) {
            $classes .= ' 1/2@bpb2 1/2@bpw 1/1@bpw2 1/1@bpe';
        }
        return $classes;
    }

    public function getTwitterUsername(): ?string
    {
        if ($this->blog->getSocial() === null) {
            return null;
        }

        return trim($this->blog->getSocial()->getTwitterUsername(), '@');
    }

    public function shouldShowImage(): bool
    {
        return $this->blog->getShowImageInDescription() && $this->blog->getImage();
    }

    public function shouldShowSocial(): bool
    {
        return $this->blog->getSocial() !== null;
    }
}
