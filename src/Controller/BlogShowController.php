<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Service\PostService;
use DateTimeImmutable;

class BlogShowController extends BaseController
{
    public function __invoke(Blog $blog, PostService $postService)
    {
        $this->setBrandingId($blog->getBrandingId());

        $result = $postService->getPostsByBlog($blog, new DateTimeImmutable());
        $posts = $result->getDomainModels();

        return $this->renderWithChrome('blog/show.html.twig', ['blog' => $blog, 'posts' => $posts]);
    }
}
