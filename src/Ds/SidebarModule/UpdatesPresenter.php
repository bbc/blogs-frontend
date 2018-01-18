<?php
declare(strict_types = 1);

namespace App\Ds\SidebarModule;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\Tag;
use App\Ds\Presenter;

class UpdatesPresenter extends Presenter
{
    /** @var Blog */
    private $blog;

    /** @var Tag|null */
    private $tag;

    public function __construct(Blog $blog, ?Tag $tag, array $options = [])
    {
        parent::__construct($options);

        $this->blog = $blog;
        $this->tag = $tag;
    }

    public function getBlogId(): string
    {
        return $this->blog->getId();
    }

    public function getTagId(): string
    {
        return $this->tag ? $this->tag->getId() : '';
    }

    public function shouldShowTagFeeds(): bool
    {
        return $this->tag !== null;
    }
}
