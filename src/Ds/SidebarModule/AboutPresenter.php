<?php
declare(strict_types = 1);

namespace App\Ds\SidebarModule;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\Image;
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
        return $this->blog->getId();
    }

    public function getBlogName(): string
    {
        return $this->blog->getName();
    }

    public function getDescription(): string
    {
        return $this->blog->getDescription();
    }

    public function getFacebookUrl(): string
    {
        if ($this->blog->getSocial() === null) {
            return '';
        }

        return $this->blog->getSocial()->getFacebookUrl();
    }

    public function getImage(): Image
    {
        return $this->blog->getImage();
    }

    public function getProseClasses(): string
    {
        $classes = 'grid';
        if ($this->shouldShowImage()) {
            $classes .= ' 1/2@bpb2 1/2@bpw 1/1@bpw2 1/1@bpe';
        }
        return $classes;
    }

    public function getTwitterUsername(): string
    {
        if ($this->blog->getSocial() === null) {
            return '';
        }

        return trim($this->blog->getSocial()->getTwitterUsername(), '@');
    }

    public function shouldShowImage(): bool
    {
        return $this->blog->getShowImageInDescription();
    }

    public function shouldShowSocial(): bool
    {
        return $this->blog->getSocial() !== null;
    }
}
