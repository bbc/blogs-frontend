<?php
declare(strict_types = 1);

namespace App\Ds\Post\PostPreview;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\Post;
use App\Ds\Post\AbstractPostPresenter;
use App\ValueObject\CosmosInfo;

class PostPreviewPresenter extends AbstractPostPresenter
{
    /** @var Blog */
    private $blog;

    /** @var int */
    private $charLimit;

    /** @var Post */
    private $post;

    /** @var bool */
    private $showReadMore = false;

    public function __construct(CosmosInfo $cosmosInfo, Blog $blog, Post $post, int $charLimit, array $options = [])
    {
        parent::__construct($options);

        $this->blog = $blog;
        $this->post = $post;
        $this->charLimit = $charLimit;
        $this->cosmosInfo = $cosmosInfo;
        $this->postPresenters = $this->setupPostPresenters();
    }

    public function getBlogId(): string
    {
        return $this->blog->getId();
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function getBlog(): Blog
    {
        return $this->blog;
    }

    /** Presenter[] */
    public function setupPostPresenters(): array
    {
        $limit = $this->charLimit;
        $presenters = [];
        foreach ($this->post->getContent() as $contentBlock) {
            $presenters[] = $this->findPresenter($contentBlock, $limit);
            $limit -= $contentBlock->getCharacterCount();
            if ($limit <= 0) {
                $this->showReadMore = true;
                return $presenters;
            }
        }

        return $presenters;
    }

    public function shouldShowShowMoreLink(): bool
    {
        return $this->showReadMore;
    }
}
