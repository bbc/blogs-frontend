<?php
declare(strict_types = 1);

namespace App\Ds\SidebarModule;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\Tag;
use App\Ds\Presenter;

class BlogTagsPresenter extends Presenter
{
    /** @var Tag[] */
    private $tags;

    /** @var Blog */
    private $blog;

    public function __construct(Blog $blog, array $tags, array $options = [])
    {
        parent::__construct($options);

        $this->tags = $tags;
        $this->blog = $blog;
    }

    public function getBlogTags(): array
    {
        return $this->tags;
    }

    public function getBlogId(): string
    {
        return $this->blog->getId();
    }
}
