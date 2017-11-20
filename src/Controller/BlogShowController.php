<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Service\PostService;
use App\BlogsService\Service\TagService;
use DateTimeImmutable;

class BlogShowController extends BlogsBaseController
{
    public function __invoke(Blog $blog, PostService $postService)
    {
        $this->setBlog($blog);

        $result = $postService->getPostsByBlog($blog, new DateTimeImmutable());
        $posts = $result->getDomainModels();

        return $this->renderWithChrome('blog/show.html.twig', ['posts' => $posts]);
    }
}
