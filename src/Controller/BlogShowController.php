<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\Post;
use App\BlogsService\Service\PostService;
use App\Helper\ApplicationTimeProvider;

class BlogShowController extends BlogsBaseController
{
    public function __invoke(Blog $blog, PostService $postService)
    {
        $result = $postService->getPostsByBlog($blog, ApplicationTimeProvider::getTimeOffsetByCurrentDSTOffset());

        /** @var Post[] $posts */
        $posts = $result->getDomainModels();

        $analyticsLabels = $this->atiAnalyticsHelper()->makeLabels('blog-homepage', 'index-section', $blog, $this->postsContainVideo($posts));
        $pageMetadata = $this->pageMetadataHelper()->makePageMetadata(null, $blog);

        return $this->renderBlogPage(
            'blog/show.html.twig',
            $analyticsLabels,
            $pageMetadata,
            $blog,
            ['posts' => $posts]
        );
    }

    /**
     * @param Post[] $posts
     * @return bool
     */
    private function postsContainVideo(array $posts): bool
    {
        foreach ($posts as $post) {
            if ($post->hasVideo()) {
                return true;
            }
        }

        return false;
    }
}
