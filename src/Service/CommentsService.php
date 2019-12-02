<?php
declare(strict_types=1);

namespace App\Service;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\Post;
use BBC\ProgrammesMorphLibrary\MorphClient;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Log\LoggerInterface;

class CommentsService
{
    /** @var string */
    private $env;

    /** @var MorphClient */
    private $client;

    /** @var LoggerInterface */
    private $logger;

    /** @var string */
    private $version;

    public function __construct(
        LoggerInterface $logger,
        MorphClient $client,
        string $env,
        string $version
    ) {
        $this->env = $env;
        $this->client = $client;
        $this->logger = $logger;
        $this->version = $version;
    }

    public function getByBlogAndPost(Blog $blog, Post $post): PromiseInterface
    {
        return $this->client->makeCachedViewPromise(
            'bbc-morph-comments-view',
            'comments-module',
            [
                'apiKey' => $blog->getCommentsApiKey(),
                'mode' => 'embedded',
                'idctaEnv' => $this->env,
                'forumId' => $this->getForumId($blog, $post),
                'version' => $this->version,
            ],
            [],
            10
        );
    }

    private function getForumId(Blog $blog, Post $post): string
    {
        return 'blogs_' . $blog->getId() . $post->getForumId();
    }
}
