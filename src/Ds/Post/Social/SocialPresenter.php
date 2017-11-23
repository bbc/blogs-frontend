<?php
declare(strict_types = 1);

namespace App\Ds\Post\Social;

use App\BlogsService\Domain\Post;
use App\Ds\Presenter;

class SocialPresenter extends Presenter
{
    /** @var Post */
    private $post;

    /** @var string */
    private $blogId;

    public function __construct(Post $post, string $blogId, array $options = [])
    {
        parent::__construct($options);

        $this->post = $post;
        $this->blogId = $blogId;
    }
    public function getGuid(): string
    {
        return (string) $this->post->getGuid();
    }

    public function getBlogId(): string
    {
        return $this->blogId;
    }
}
