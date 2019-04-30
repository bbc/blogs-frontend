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
        $this->setIstatsPageType('index_single');
        $this->setAtiChapterOneVariable('blog-homepage');
        $this->setBlog($blog);

        $result = $postService->getPostsByBlog($blog, ApplicationTimeProvider::getLocalTime());

        /** @var Post[] $posts */
        $posts = $result->getDomainModels();

        $this->hasVideo = $this->postsContainVideo($posts);

        return $this->renderWithChrome('blog/show.html.twig', ['posts' => $posts]);
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
