<?php
declare(strict_types = 1);
namespace App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Service\PostService;
use App\BlogsService\Service\TagService;

abstract class AbstractTagFeedController extends AbstractFeedController
{
    protected function getPostsForFeed(Blog $blog, string $tagId, PostService $postService, TagService $tagService): array
    {
        $tag = $tagService->getTagById($tagId, $blog);

        if (!$tag) {
            throw $this->createNotFoundException('Tag not found');
        }

        $postResult = $postService->getPostsByTag($blog, $tag, 1, 15);
        return $postResult->getDomainModels();
    }
}
