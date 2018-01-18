<?php
declare(strict_types = 1);
namespace App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Service\PostService;
use App\BlogsService\Service\TagService;
use App\FeedGenerator\FeedGenerator;
use Symfony\Component\HttpFoundation\Response;

class TagRssFeedController extends AbstractTagFeedController
{
    public function __invoke(Blog $blog, string $tagId, PostService $postService, TagService $tagService, FeedGenerator $generator): Response
    {
        $posts = $this->getPostsForFeed($blog, $tagId, $postService, $tagService);
        $feedData = $generator->generateRssFeed($blog, $posts);

        return $this->generateResponse($feedData);
    }
}
