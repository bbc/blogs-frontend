<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Service\PostService;
use DateTimeImmutable;
use Exception;

class PostShowController extends BaseController
{
    public function __invoke(Blog $blog, string $guid, PostService $postService)
    {
        $this->setBrandingId($blog->getBrandingId());

        // This is for dev purposes and will be done properly later.
        $result = $postService->getPostsByBlog($blog, new DateTimeImmutable());
        $posts = $result->getDomainModels();

        $testPost = null;

        foreach ($posts as $post) {
            if ((string) $post->getGuid() === $guid) {
                $testPost = $post;
            }
        }

        if (!isset($testPost)) {
            throw new Exception('The post has not been found, please choose another for testing.');
        }

        return $this->renderWithChrome('post/show.html.twig', ['blog' => $blog, 'post' => $testPost]);
    }
}
