<?php
declare(strict_types = 1);
namespace App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Service\PostService;
use App\FeedGenerator\FeedGenerator;
use Symfony\Component\HttpFoundation\Response;

class BlogRssFeedController extends AbstractBlogFeedController
{
    public function __invoke(Blog $blog, PostService $postService, FeedGenerator $generator): Response
    {
        $posts = $this->getPostsForFeed($blog, $postService);
        $feedData = $generator->generateRssFeed($blog, $posts);

        return $this->generateResponse($feedData);
    }
}
