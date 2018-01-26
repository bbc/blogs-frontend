<?php
declare(strict_types = 1);
namespace App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\Post;
use App\BlogsService\Service\PostService;
use Cake\Chronos\Chronos;

abstract class AbstractBlogFeedController extends AbstractFeedController
{
    /**
     * @param Blog $blog
     * @param PostService $postService
     * @return Post[]
     */
    protected function getPostsForFeed(Blog $blog, PostService $postService): array
    {
        $postResult = $postService->getPostsByBlog($blog, Chronos::now(), 1, 15);
        return $postResult->getDomainModels();
    }
}
