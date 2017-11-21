<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\ValueObject\GUID;
use App\BlogsService\Service\PostService;

class PostShowController extends BlogsBaseController
{
    public function __invoke(Blog $blog, string $guid, PostService $postService)
    {
        $this->setBlog($blog);

        $post = $postService->getPostByGuid(new GUID($guid), $blog);

        if (!$post) {
            throw $this->createNotFoundException('Post not found');
        }

        return $this->renderWithChrome('post/show.html.twig', ['post' => $post]);
    }
}
