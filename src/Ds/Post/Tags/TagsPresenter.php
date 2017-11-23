<?php
declare(strict_types = 1);

namespace App\Ds\Post\Tags;

use App\BlogsService\Domain\Tag;
use App\Ds\Presenter;

class TagsPresenter extends Presenter
{
    /** @var Tag[] */
    private $tags;

    /** @var string */
    private $blogId;

    public function __construct(array $tags, string $blogId, array $options = [])
    {
        parent::__construct($options);

        $this->tags = $tags;
        $this->blogId = $blogId;
    }

    /**
     * @return Tag[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    public function getBlogId(): string
    {
        return $this->blogId;
    }
}
